<?php
require_once("../../class/connect.php");
require_once("../../class/CRUD.php");
require_once("../../class/CustomValidation.php");
require_once("returnToFontend.php");

try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $customValidation = new CustomValidation();
    $jsonData = json_decode($_POST["jsonData"]);
    $openShiftBy = $jsonData->closeBy;
    $companyId = $jsonData->companyId;
    $branchId = $jsonData->branchId;
    $mainShiftId = $jsonData->mainShiftId;
    $subShiftId = $jsonData->subShiftId;
    $bindParam[":company_id"] = $companyId;
    $bindParam[":branch_id"] = $branchId;
    // $bindParam[":main_shift_id"] = $mainShiftId;
    $bindParam[":sub_shift_id"] = $subShiftId;
    $checkCloseShift = ["status" => false];
    $checkCloseShift["message"] = "ไม่สามารถปิดกะ/ปิดวันได้ โปรดลองอีกครั้ง/ติดต่อเจ้าหน้าที่";

    //validate
    $checkValidation = validateDate($jsonData);
    if (count($checkValidation) == 0) {

        $toDay = date("Y-m-d");
        // company_sub_shift
        $sumCloseShift = sumAllDailySale($companyId, $bindParam); // ถ้าปิดวันให้ sum ยอดขายของกะทั้งหมดก่อน
        $getShiftDate = getShiftDate($companyId, $bindParam); // ถ้าปิดวันให้ sum ยอดขายของกะทั้งหมดก่อน
        $getShiftDateResults = $getShiftDate["results"]; // ถ้าปิดวันให้ sum ยอดขายของกะทั้งหมดก่อน
        if (!$sumCloseShift["status"]) {
            throw new Exception("Error: " . $sumCloseShift["message"]);
        }
        $jsonData->shiftSum = $sumCloseShift["results"][0]["shiftSum"];
        $jsonData->shiftTotalSum = $sumCloseShift["results"][0]["shiftTotalSum"];
        $jsonData->shiftTotalDiscount = $sumCloseShift["results"][0]["shiftTotalDiscount"];
        $jsonData->shiftTotalCashPay = $sumCloseShift["results"][0]["shiftTotalCashPay"];
        $jsonData->shiftOtherPay = $sumCloseShift["results"][0]["shiftOtherPay"];
        $jsonData->shiftCountBillSum = $sumCloseShift["results"][0]["shiftCountBillSum"];
        $dataForUpdateSubShift = setDataForUpdateSubShift($jsonData);

        $checkCloseShift = updateSubShiftTable($companyId, $bindParam, $dataForUpdateSubShift);
        if (!$checkCloseShift["status"]) {
            throw new Exception("Error: " . $checkCloseShift["message"]);
        }
    }
    if ($checkCloseShift["status"]) {
        $checkCloseShift["message"] = giveEncourageMessage();
        $returnToFontend->message = $checkCloseShift["message"];
        $returnToFontend->results = array("openDayAt" => $getShiftDateResults[0]["sub_shift_created_at"]);
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = $checkCloseShift["message"];
        $returnToFontend->results = $checkValidation;
        $returnToFontend->sendToFontend();
    }
} catch (PDOException $e) {
    $returnToFontend->status = false;
    $returnToFontend->message = "Error: " . $e->getMessage();
    $returnToFontend->returnCode = 500;
    $returnToFontend->sendToFontend();
}
function setDataForUpdateSubShift($data)
{
    $returnData = [];
    $returnData[0] = $data->closeBy;
    $returnData[1] = 1;
    $returnData[3] = $data->shiftSum;
    $returnData[4] = $data->shiftTotalSum;
    $returnData[5] = $data->shiftTotalDiscount;
    $returnData[2] = $data->cashPayment;
    $returnData[6] = $data->shiftTotalCashPay;
    $returnData[7] = $data->shiftOtherPay;
    $returnData[8] = $data->shiftCountBillSum;
    return $returnData;
}
function validateDate($jsonData)
{
    $customValidation = new CustomValidation();
    $checkTypeDataNumber = $customValidation->checkTypeData((array) $jsonData, [], "number");

    return $checkTypeDataNumber;
}
function giveEncourageMessage()
{
    $text = [
        "ยิ่งกว่ามรสุม ก็ลูกค้าร้านเรานี้ละ ฮึบ!",
        "เราก็ใช้ชีวิตไปวันๆ เพราะใช้ทีละสองวันไม่ได้ ลุย!",
        "คิดต่างไปทางอื่น คิดถึงเงินเดือนสิ้นเดือนล้าวว",
    ];
    return $text[rand(0, count($text) - 1)];
}
function updateSubShiftTable($companyId, $bindParam, $setDataForInsert)
{
    $CRUD = new CRUD($companyId);
    $subShiftTableName = "company_sub_shift";
    $subShiftWhereField = ["sub_shift_id"];
    $subShiftFieldSet = [
        "sub_shift_close_by",
        "sub_shift_close_status",
        "sub_shift_sum",
        "sub_shift_total_sum",
        "sub_shift_total_discount",
        "sub_shift_cash_payment",
        "sub_shift_system_cash_payment",
        "sub_shift_other_payment",
        "sub_shift_bill_count"
    ];
    $sql = "SELECT 
    *
    FROM $subShiftTableName
    WHERE
    company_id = :company_id
    AND branch_id = :branch_id
    -- AND main_shift_id = :main_shift_id
    AND sub_shift_id = :sub_shift_id
    ";
    $checkCloseShift = $CRUD->select($sql, $bindParam); // เช็คว่ามีการเปิดกะหรือยังมั้ง
    if (!$checkCloseShift["status"]) {
        return $checkCloseShift;
    }
    
    $subShiftId = $checkCloseShift["results"][0]["sub_shift_id"];
    $subShiftWhereValue = [$subShiftId];
    $checkCloseShift = $CRUD->updateWhere($setDataForInsert, $subShiftFieldSet, $subShiftTableName, $subShiftWhereField, $subShiftWhereValue);
    return $checkCloseShift;
}
function sumAllDailySale($companyId, $bindParam)
{
    $CRUD = new CRUD($companyId);
    $subShiftTableName = "bills";
    $sql = "SELECT 
    SUM(bill_sum) AS shiftSum,
    SUM(bill_total_sum) AS shiftTotalSum,
    SUM(bill_discount) AS shiftTotalDiscount,
    SUM(case when payment_id  = 1 then bill_money_pay else 0 end) AS shiftTotalCashPay,
    SUM(case when payment_id  <> 1 then bill_money_pay else 0 end) AS shiftOtherPay,
    COUNT(bill_id) AS shiftCountBillSum
    FROM $subShiftTableName
    WHERE
    company_id = :company_id
    AND branch_id = :branch_id
    -- AND main_shift_id = :main_shift_id
    AND bill_of_shift  = :sub_shift_id
    ";
    $returnData = $CRUD->select($sql, $bindParam);
    return $returnData;
}
function getShiftDate($companyId, $bindParam)
{
    $CRUD = new CRUD($companyId);
    $subShiftTableName = "company_sub_shift";
    $sql = "SELECT 
    DATE(sub_shift_created_at) AS sub_shift_created_at
    FROM $subShiftTableName
    WHERE
    company_id = :company_id
    AND branch_id = :branch_id
    AND sub_shift_id  = :sub_shift_id
    ";
    $returnData = $CRUD->select($sql, $bindParam);
    return $returnData;
}
