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
    $bindParam[":company_id"] = $companyId;
    $bindParam[":branch_id"] = $branchId;
    $bindParam[":main_shift_id"] = $mainShiftId;
    $checkCloseShift = ["status" => false];
    $checkCloseShift["message"] = "ไม่สามารถปิดกะ/ปิดวันได้ โปรดลองอีกครั้ง/ติดต่อเจ้าหน้าที่";

    //validate
    $checkValidation = validateDate($jsonData);
    if (count($checkValidation) == 0) {
        $dataForUpdateSubShift = setDataForUpdateSubShift($jsonData);
        $toDay = date("Y-m-d");
        // company_sub_shift
        $checkCloseShift = updateSubShiftTable($companyId, $bindParam, $dataForUpdateSubShift);
        if (!$checkCloseShift["status"]) {
            throw new Exception("Error: " . $checkCloseShift["message"]);
        }
        $sumCloseShift = sumAllDailySale($companyId, $bindParam); // ถ้าปิดวันให้ sum ยอดขายของกะทั้งหมดก่อน
        if (!$sumCloseShift["status"]) {
            throw new Exception("Error: " . $sumCloseShift["message"]);
        }
        // company_main_shift
        $jsonData->shiftTotalSum = $sumCloseShift["results"][0]["shiftTotalSum"];
        $jsonData->shiftSum = $sumCloseShift["results"][0]["shiftSum"];
        $jsonData->shiftCashSum = $sumCloseShift["results"][0]["shiftCashSum"];
        $jsonData->shiftSystemCashSum = $sumCloseShift["results"][0]["shiftSystemCashSum"];
        $jsonData->shiftOtherSum = $sumCloseShift["results"][0]["shiftOtherSum"];
        $jsonData->shiftDiscountSum = $sumCloseShift["results"][0]["shiftDiscountSum"];
        $jsonData->shiftCountBillSum = $sumCloseShift["results"][0]["shiftCountBillSum"];
        $dataForUpdateMainShift = setDataForUpdateMainShift($jsonData);
        $checkCloseShift = updateMainShiftTable($companyId, $mainShiftId, $dataForUpdateMainShift);
        if (!$checkCloseShift["status"]) {
            // ถ้าให้ดีตรงนี้ update fail ให้ update table sub_shift กลับมาเป็นก่อนปิดกะด้วย
            throw new Exception("Error: " . $checkCloseShift["message"]);
        }
    }
    if ($checkCloseShift["status"]) {
        $checkCloseShift["message"] = giveEncourageMessage();
        $returnToFontend->message = $checkCloseShift["message"];
        $returnToFontend->results = array("openDayAt" => $sumCloseShift["results"][0]["sub_shift_created_at"]);
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
    $returnData[2] = $data->cashPayment;
    return $returnData;
}
function setDataForUpdateMainShift($data)
{
    $returnData = [];
    $returnData[0] = $data->closeBy;
    $returnData[1] = 1;
    $returnData[2] = $data->shiftTotalSum;
    $returnData[3] = $data->shiftSum;
    $returnData[4] = $data->shiftCashSum;
    $returnData[5] = $data->shiftSystemCashSum;
    $returnData[6] = $data->shiftOtherSum;
    $returnData[7] = $data->shiftDiscountSum;
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
function updateMainShiftTable($companyId, $mainShiftId, $setDataForInsert)
{
    $CRUD = new CRUD($companyId);
    $mainShiftTableName = "company_main_shift";
    $mainShiftWhereField = ["main_shift_id"];
    $mainShiftWhereValue = [$mainShiftId];
    $mainShiftFieldSet = [
        "main_shift_close_by",
        "main_shift_close_status",
        "main_shift_total_sum",
        "main_shift_sum",
        "main_shift_cash_payment",
        "main_shift_system_cash_payment",
        "main_shift_other_payment",
        "main_shift_total_discount",
        "main_shift_bill_count",
    ];
    $checkCloseShift = $CRUD->updateWhere($setDataForInsert, $mainShiftFieldSet, $mainShiftTableName, $mainShiftWhereField, $mainShiftWhereValue);
    return $checkCloseShift;
}
function updateSubShiftTable($companyId, $bindParam, $setDataForInsert)
{
    $CRUD = new CRUD($companyId);
    $subShiftTableName = "company_sub_shift";
    $subShiftWhereField = ["sub_shift_id"];
    $subShiftFieldSet = [
        "sub_shift_close_by",
        "sub_shift_close_status",
        "sub_shift_cash_payment",
    ];
    $sql = "SELECT 
    *
    FROM $subShiftTableName
    WHERE
    company_id = :company_id
    AND branch_id = :branch_id
    AND main_shift_id = :main_shift_id
    ORDER BY sub_shift_id DESC
    LIMIT 1
    ";
    $checkCloseShift = $CRUD->select($sql, $bindParam);
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
    $subShiftTableName = "company_sub_shift";
    $sql = "SELECT 
    SUM(sub_shift_total_sum) AS shiftTotalSum,
    SUM(sub_shift_sum) AS shiftSum,
    SUM(sub_shift_cash_payment) AS shiftCashSum,
    SUM(sub_shift_system_cash_payment) AS shiftSystemCashSum,
    SUM(sub_shift_other_payment) AS shiftOtherSum,
    SUM(sub_shift_total_discount) AS shiftDiscountSum,
    SUM(sub_shift_bill_count) AS shiftCountBillSum,
    DATE(sub_shift_created_at) AS sub_shift_created_at
    FROM $subShiftTableName
    WHERE
    company_id = :company_id
    AND branch_id = :branch_id
    AND main_shift_id = :main_shift_id
    ";
    $checkCloseShift = $CRUD->select($sql, $bindParam);
    return $checkCloseShift;
}
