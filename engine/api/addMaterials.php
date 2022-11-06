<?php
require_once("../../class/connect.php");
require_once("../../class/addMaterial.php");
require_once("../../class/CustomValidation.php");
require_once("returnToFontend.php");


try {
    $jsonData = json_decode($_POST["jsonData"]);
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $customValidation = new CustomValidation();
    $addMaterial = new addMaterial($jsonData->companyId);
    $returnMessage = "ไม่สามารถเพิ่มข้อมูลวัตถุดิบได้ โปรดลองอีกครั้ง/ติดต่อเจ้าหน้าที่";
    $setDataForInsert = setDataForInsert($jsonData);
    $checkAddMaterial["status"] = false;
    $checkValidation = array();
    $checkSpecialCharacters = $customValidation->checkSpecialCharacters($jsonData,  ["material_json"], '/[\'^£$%&*()}{@#~?><>,|=+¬]/');
    $checkEmpty = $customValidation->checkEmpty($jsonData, ["materialDateExpired"]);
    $checkTypeDataString = $customValidation->checkTypeData($jsonData, [], "string");
    $checkTypeDataNumber = $customValidation->checkTypeData($jsonData, ["materialSubId", "materialName", "materialDetail", "materialDateExpired", "material_json"], "number");
    $checkValidation = array_merge($checkSpecialCharacters, $checkEmpty,$checkTypeDataNumber, $checkTypeDataString);
    if (count($checkValidation) == 0) {
        $checkAddMaterial["status"] = $addMaterial->insert($setDataForInsert);
    }

    if ($checkAddMaterial["status"]) {
        $returnMessage = "เพิ่มวัตถุดิบ $jsonData->materialName({$jsonData->materialSubId}) เรียบร้อย";
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

function setUserField($usersData)
{
    $usersDataNew = [];
    $usersDataNew[0]["social_login"] = $usersData->socialLogin;
    $usersDataNew[0]["users_email"] = $usersData->socialEmail;
    $usersDataNew[0]["users_firstname"] = $usersData->socialFirstname;
    $usersDataNew[0]["users_lastname"] = $usersData->socialLastname;
    $usersDataNew[0]["users_active"] = 0;
    return $usersDataNew;
}
function setDataForInsert($data)
{
    $returnData = [];
    $returnData[0]["company_id"] = $data->companyId;
    $returnData[0]["branch_id"] = $data->materialBranch;
    $returnData[0]["material_sub_id"] = $data->materialSubId;
    $returnData[0]["material_name"] = $data->materialName;
    $returnData[0]["material_detail"] = $data->materialDetail;
    $returnData[0]["material_by"] = $data->createBy;
    $returnData[0]["material_amount"] = $data->materialStockAmount;
    $returnData[0]["material_insert"] = $data->materialStockAmount;
    $returnData[0]["material_expired_at"] = (!empty($data->materialDateExpired) ? $data->materialDateExpired : NULL);
    $returnData[0]["material_active"] = 1;
    $returnData[0]["material_json"] = json_encode($data);
    return $returnData;
}
