<?php
require_once("../../class/connect.php");
require_once("../../class/CRUD.php");
require_once("../../class/CustomValidation.php");
require_once("returnToFontend.php");
try {
    $jsonData = json_decode($_POST["jsonData"]);
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $customValidation = new CustomValidation();
    $CRUD = new CRUD($jsonData->companyId);
    $returnMessage = "ไม่สามารถเพิ่มข้อมูลประเภทการชำระเงินได้ โปรดลองอีกครั้ง/ติดต่อเจ้าหน้าที่";
    $setDataForInsert = setDataForInsert($jsonData);
    $checkValidation = array();
    $checkSpecialCharacters = $customValidation->checkSpecialCharacters($jsonData,  ["paymentTypeJson"], '/[\'^£$%&*()}{@#~?><>,|=+¬]/');
    $checkEmpty = $customValidation->checkEmpty($jsonData, ["paymentTypeDateExpired"]);
    $checkTypeDataString = $customValidation->checkTypeData($jsonData, [], "string");
    $checkTypeDataNumber = $customValidation->checkTypeData($jsonData, ["paymentTypeSubId", "paymentTypeName", "paymentTypeDetail", "paymentTypeDateExpired", "paymentTypeJson"], "number");
    $checkValidation = array_merge($checkSpecialCharacters, $checkEmpty, $checkTypeDataNumber, $checkTypeDataString);
    $checkAddType = ["status" => false];
    if (count($checkValidation) == 0) {
        $fieldSet = [
            "company_id",
            "branch_id",
            "payment_sub_id",
            "payment_name",
            "payment_detail",
            "payment_created_by",
            "payment_expired_at",
            "payment_active",
            "payment_json",
        ];
        $tableName = "payments_type";
        $checkAddType = $CRUD->insert($setDataForInsert, $fieldSet, $tableName);
    }

    if ($checkAddType["status"]) {
        $returnMessage = "เพิ่มประเภทการชำระเงิน $jsonData->paymentTypeName({$jsonData->paymentTypeSubId}) เรียบร้อย";
        $returnToFontend->message = $returnMessage;
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = $returnMessage;
        $returnToFontend->results = $checkValidation;
        $returnToFontend->sendToFontend();
    }
} catch (PDOException $e) {
    $returnToFontend->status = false;
    $returnToFontend->message = "Error: " . $e->getMessage();
    $returnToFontend->returnCode = 500;
    $returnToFontend->sendToFontend();
}
function setDataForInsert($data)
{
    $returnData = [];
    $returnData[0]["company_id"] = $data->companyId;
    $returnData[0]["branch_id"] = $data->paymentTypeBranch;
    $returnData[0]["payment_sub_id"] = $data->paymentTypeSubId;
    $returnData[0]["payment_name"] = $data->paymentTypeName;
    $returnData[0]["payment_detail"] = $data->paymentTypeDetail;
    $returnData[0]["payment_created_by"] = $data->createBy;
    $returnData[0]["payment_expired_at"] = (!empty($data->paymentTypeDateExpired) ? $data->paymentTypeDateExpired : NULL);
    $returnData[0]["payment_active"] = 1;
    $returnData[0]["payment_json"] = json_encode($data);
    return $returnData;
}
