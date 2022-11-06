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
    $openShiftBy = $jsonData->openBy;
    $companyId = $jsonData->companyId;
    $branchId = $jsonData->branchId;
    $CRUD = new CRUD($companyId);
    $checkOpenShift = ["status" => false];
    $checkOpenShift["message"] = "ไม่สามารถเปิดกะได้ โปรดลองอีกครั้ง/ติดต่อเจ้าหน้าที่";
    $toDay = date("Y-m-d");

    //validate
    $checkValidation = validateDate($jsonData);
    $checkOpenDayData =  checkOpenDay($toDay, $companyId, $branchId);
    $checkCloseDayStatus =  $checkOpenDayData["mainShiftCloseStatus"];
    if ((int) $checkCloseDayStatus == 1) {
        $returnToFontend->status = false;
        $returnToFontend->message = "Cant open day!";
        // $returnToFontend->results = array("currentMainShiftId" => "", "currentSubShiftId" => "");
        $returnToFontend->results = array("ไม่สามารถเปิดวันได้อีก เนื่องจากได้มีการปิดวันไปแล้ว");
        $returnToFontend->sendToFontend();
        return;
    }
    if (count($checkValidation) == 0) {
        $setDataForInsert = setDataForInsert($jsonData);

        // เช็คก่อนว่าเปิดวันไปหรือยัง false = ยังไม่เปิด true = เปิดวันแล้ว
        $checkLastMainShiftInsertId =  $checkOpenDayData["mainShiftId"];
        if (!$checkLastMainShiftInsertId) {
            // insert to company_main_shift เปิดวัน
            $fieldSet = [
                "company_id",
                "branch_id",
                "main_shift_open_by",
                "main_shift_cash_change",
            ];
            $tableName = "company_main_shift";
            $checkOpenShift = $CRUD->insert($setDataForInsert, $fieldSet, $tableName);
            if (!$checkOpenShift["status"]) {
                throw new Exception("Error: " . $checkOpenShift["message"]);
            }
            $lastMainShiftInsertId = $checkOpenShift["insertId"];
        } else {
            $lastMainShiftInsertId = $checkLastMainShiftInsertId;
        }
        // เช็คก่อนว่าเปิดกะไปหรือยัง false = ไม่ให้เปิดกะ(ยังไม่ปิดกะก่อนหน้า)  true = ให้เปิดกะได้
        $checkOpenShiftStatus = checkOpenShift($lastMainShiftInsertId, $companyId, $branchId);
        if ($checkOpenShiftStatus != false) {
            // insert to company_sub_shift เปิดกะ
            $sql = "INSERT INTO company_sub_shift (main_shift_id, company_id, branch_id, sub_shift_open_by, sub_shift_cash_change)
                    SELECT main_shift_id,company_id, branch_id, main_shift_open_by, main_shift_cash_change FROM company_main_shift WHERE main_shift_id = $lastMainShiftInsertId;
            ";
            $checkOpenShift = $CRUD->manualSql($sql, true);
            if (!$checkOpenShift["status"]) {
                if ($checkLastMainShiftInsertId == false) { //check false เพราะ ถ้าครั้งแรกถึงลบ แต่ถ้าเคยเปิดไปแล้วไม่ลบ
                    $sql = "DELETE FROM `company_main_shift` WHERE main_shift_id = $lastMainShiftInsertId";
                    $CRUD->manualSql($sql);
                }
                throw new Exception("Error: " . $checkOpenShift["message"]);
            }
        } else {
            $checkOpenShift["status"] = false;
            $checkValidation[0] = "ต้องปิดกะก่อนหน้าก่อน!";
        }
    }
    if ($checkOpenShift["status"]) {
        $checkOpenShift["message"] = giveEncourageMessage();
        $returnToFontend->message = $checkOpenShift["message"];
        $returnToFontend->results = array("currentMainShiftId" => $lastMainShiftInsertId, "currentSubShiftId" => $checkOpenShift["insertId"]);
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = $checkOpenShift["message"];
        $returnToFontend->results = $checkValidation;
        $returnToFontend->sendToFontend();
    }
} catch (PDOException $e) {
    $returnToFontend->status = false;
    $returnToFontend->message = "Error: " . $e->getMessage();
    $returnToFontend->returnCode = 500;
    $returnToFontend->sendToFontend();
}
function checkAlreadyUsername($inputUsername, $companyId, $branchId)
{
    $CRUD = new CRUD($companyId);
    $returnData = "";
    $sql = "SELECT 
            company_users_username 
            FROM 
            company_users 
            WHERE 
            company_users_username = :company_username
            AND company_id = :company_id
            AND company_branch_id = :company_branch_id
            ";
    $data = $CRUD->select($sql, [":company_username" => $inputUsername, ":company_id" => $companyId, ":company_branch_id" => $branchId]);
    if (count($data["results"]) > 0) {
        $returnData = "employee username '$inputUsername' already used.";
    }
    return $returnData;
}
function setDataForInsert($data)
{
    $dataArray = (array) $data;
    $returnData = [];
    $returnData[0]["company_id"] = $data->companyId;
    $returnData[0]["branch_id"] = $data->branchId;
    $returnData[0]["main_shift_open_by"] = $data->openBy;
    $returnData[0]["main_shift_cash_change"] = $data->openShiftAmount;


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
function checkOpenDay($toDay, $companyId, $branchId)
{
    $CRUD = new CRUD($companyId);
    $bindParam[":company_id"] = $companyId;
    $bindParam[":branch_id"] = $branchId;
    $bindParam[":date"] = $toDay;
    $returnData = false;
    $sql = "SELECT 
    main_shift_id,
    main_shift_close_status 
    FROM company_main_shift
    WHERE 
    company_id = :company_id
    AND branch_id = :branch_id
    AND DATE(main_shift_created_at) = :date
    ";
    $mainShiftData = $CRUD->select($sql, $bindParam);
    if (count($mainShiftData["results"]) > 0) {
        $returnData["mainShiftId"] = $mainShiftData["results"][0]["main_shift_id"];
        $returnData["mainShiftCloseStatus"] = $mainShiftData["results"][0]["main_shift_close_status"];
    }
    return $returnData;
}
function checkOpenShift($mainShiftId, $companyId, $branchId)
{
    $CRUD = new CRUD($companyId);
    $bindParam[":company_id"] = $companyId;
    $bindParam[":branch_id"] = $branchId;
    $bindParam[":main_shift_id"] = $mainShiftId;
    $returnData = true;
    $sql = "SELECT 
    sub_shift_close_status
    FROM company_sub_shift
    WHERE 
    company_id = :company_id
    AND branch_id = :branch_id
    AND main_shift_id = :main_shift_id
    ORDER BY sub_shift_id DESC
    LIMIT 1
    ";
    $subShiftData = $CRUD->select($sql, $bindParam);
    if (count($subShiftData["results"]) > 0) {
        $returnData = $subShiftData["results"][0]["sub_shift_close_status"];
        if ((int)$returnData == 0) {
            $returnData = false;
        }
    }
    return $returnData;
}
