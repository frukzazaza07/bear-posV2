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
    $returnMessage = "ไม่สามารถเพิ่มข้อมูลประเภทสินค้าได้ โปรดลองอีกครั้ง/ติดต่อเจ้าหน้าที่";
    $setDataForInsert = setDataForInsert($jsonData);
    $checkValidation = array();
    $checkSpecialCharacters = $customValidation->checkSpecialCharacters($jsonData,  ["productTypeJson"], '/[\'^£$%&*()}{@#~?><>,|=+¬]/');
    $checkEmpty = $customValidation->checkEmpty($jsonData, ["productTypeDateExpired"]);
    $checkTypeDataString = $customValidation->checkTypeData($jsonData, [], "string");
    $checkTypeDataNumber = $customValidation->checkTypeData($jsonData, ["productTypeSubId", "productTypeName", "productTypeDetail", "productTypeDateExpired", "productTypeJson"], "number");
    $checkValidation = array_merge($checkSpecialCharacters, $checkEmpty, $checkTypeDataNumber, $checkTypeDataString);
    $checkAddType = false;
    if (count($checkValidation) == 0) {
        $fieldSet = [
            "company_id",
            "branch_id",
            "Type_sub_id",
            "Type_name",
            "Type_detail",
            "Type_by",
            "Type_expired_at",
            "Type_active",
            "Type_json",
        ];
        $tableName = "product_type";
        $checkAddType = $CRUD->insert($setDataForInsert, $fieldSet, $tableName);
    }

    if ($checkAddType) {
        $returnMessage = "เพิ่มประเภทสินค้า $jsonData->productTypeName({$jsonData->productTypeSubId}) เรียบร้อย";
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
    $returnData[0]["branch_id"] = $data->productTypeBranch;
    $returnData[0]["Type_sub_id"] = $data->productTypeSubId;
    $returnData[0]["Type_name"] = $data->productTypeName;
    $returnData[0]["Type_detail"] = $data->productTypeDetail;
    $returnData[0]["Type_by"] = $data->createBy;
    $returnData[0]["Type_expired_at"] = (!empty($data->productTypeDateExpired) ? $data->productTypeDateExpired : NULL);
    $returnData[0]["Type_active"] = 1;
    $returnData[0]["Type_json"] = json_encode($data);
    return $returnData;
}
