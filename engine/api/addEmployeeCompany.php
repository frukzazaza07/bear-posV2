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
    $jsonData->employeeJson = $_POST["jsonData"];
    $companyId = $jsonData->createBy->companyId;
    $branchId = $jsonData->employeeBranch;
    $imageData = $jsonData->employeeImage;
    $CRUD = new CRUD($companyId);
    $checkAddEmployee = ["status" => false];
    $returnMessage = "ไม่สามารถเพิ่มข้อมูลพนักงานใหม่ได้ โปรดลองอีกครั้ง/ติดต่อเจ้าหน้าที่";
    //validate 3 step
    $checkValidation = validateDate($jsonData);
    $checkUsernameUsed = checkAlreadyUsername($jsonData->employeeUsername, $companyId, $branchId);
    if ($checkUsernameUsed != "") {
        $checkValidation["step2"][] = $checkUsernameUsed;
    }
    $uploadCheck = uploadImage($imageData, $companyId, $branchId);
    if ($uploadCheck["status"] == false) {
        $checkValidation["step3"] = $uploadCheck["message"];
    }
    if (count($checkValidation["step1"]) == 0 && count($checkValidation["step2"]) == 0 && count($checkValidation["step3"]) == 0) {
        $jsonData->companyUserImg =  $uploadCheck["pathUpload"];
        $setDataForInsert = setDataForInsert($jsonData);
        $fieldSet = [
            "company_id",
            "company_branch_id",
            "position_id",
            "company_users_username",
            "company_users_password",
            "company_users_firstname",
            "company_users_lastname",
            "company_users_nickname",
            "company_users_birthday",
            "company_users_address",
            "company_users_province",
            "company_users_amphur",
            "company_users_tambon",
            "company_users_tel",
            "company_users_email",
            "company_user_img",
            "company_users_by",
            "company_users_active",
            "company_users_json",
        ];
        $tableName = "company_users";
        $checkAddEmployee = $CRUD->insert($setDataForInsert, $fieldSet, $tableName);
        $checkAddEmployee["results"] = $checkAddEmployee["message"];
    } else {
        $checkAddEmployee["results"] = $checkValidation;
    }
    if ($checkAddEmployee["status"]) {
        $returnMessage = "เพิ่มพนักงาน $jsonData->employeeFirstname  $jsonData->employeeLastname เรียบร้อย";
        $returnToFontend->message = $returnMessage;
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = $returnMessage;
        $returnToFontend->results = $checkAddEmployee["results"];
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
    $defultPassword = "123456789";
    $returnData = [];
    $returnData[0]["company_id"] = $data->createBy->companyId;
    $returnData[0]["company_branch_id"] = $data->employeeBranch;
    $returnData[0]["position_id"] = $data->employeePosition;
    $returnData[0]["company_users_username"] = $data->employeeUsername;
    $returnData[0]["company_users_password"] = password_hash($defultPassword, PASSWORD_BCRYPT);
    $returnData[0]["company_users_firstname"] = $data->employeeFirstname;
    $returnData[0]["company_users_lastname"] = $data->employeeLastname;
    $returnData[0]["company_users_nickname"] = $data->employeeNickname;
    $returnData[0]["company_users_birthday"] = $data->employeeBirthday;
    $returnData[0]["company_users_address"] = $data->employeeAddress;
    $returnData[0]["company_users_province"] = $data->employeeProvinces;
    $returnData[0]["company_users_amphur"] = $data->employeeAmphur;
    $returnData[0]["company_users_tambon"] = $data->employeeTambon;
    $returnData[0]["company_users_tel"] = $data->employeeMobile;
    $returnData[0]["company_users_email"] = $data->employeeEmail;
    $returnData[0]["company_user_img"] = $data->companyUserImg;
    $returnData[0]["company_users_by"] = $data->createBy->loginBy;
    $returnData[0]["company_users_active"] = 1;
    $returnData[0]["company_users_json"] = $data->employeeJson;

    return $returnData;
}
function uploadImage($imageData, $companyId, $branchId)
{
    $customValidation = new CustomValidation();
    $targetUploads = $_SERVER['DOCUMENT_ROOT'] . "/bear/uploads/" . $companyId . "/" . $branchId . "/" . "employee_image/" . date("Ymd") . "/";
    $statusUpload = [];
    $statusUpload["status"] = true;
    for ($i = 0; $i < count($imageData); $i++) {
        // แปลงเป็น image url ทั้ง upload ผ่าน input และ uplaod ผ่าน canvas เลย อิอิ
        $customValidation->checkFolderAlreadyExists($targetUploads); // check fodler upload ไม่มี ให้สร้างใหม่
        $fileName = "profile_" . date("Ymd") . time() . rand() . '.png';
        // img1 = idCard img2 = profile
        $pathUpload[$i]["img" . ($i + 1)] = $targetUploads . $fileName;
        if ($imageData[$i]->uploadType == "canvas") {
            if (!$customValidation->uploadImageFileFromCanvasHTML($imageData[$i]->imageData, $targetUploads, $fileName)) {
                $statusUpload["status"] = false;
                $statusUpload["message"][$i] = "#" . ($i + 1) . " Upload fail!";
            }
        } else {
            if (!$customValidation->uploadImageFileFromCanvasHTML($imageData[$i]->imagePreview, $targetUploads, $fileName)) {
                $statusUpload["status"] = false;
                $statusUpload["message"][$i] = "#" . ($i + 1) . " Upload fail!";
            }
        }
    }
    $statusUpload["pathUpload"] = json_encode($pathUpload);
    return $statusUpload;
}
function validateDate($jsonData)
{
    $returnToFontend = new returnToFontend();
    $customValidation = new CustomValidation();
    // แยก form 3step แบบ fontend
    $formStep = [
        "step1" => [
            "employeeFirstname" => $jsonData->employeeFirstname,
            "employeeLastname" => $jsonData->employeeLastname,
            "employeeNickname" => $jsonData->employeeNickname,
            "employeeBirthday" => $jsonData->employeeBirthday,
            "employeeEmail" => $jsonData->employeeEmail,
            "employeeAddress" => $jsonData->employeeAddress,
            "employeeImage" => $jsonData->employeeImage,
            "employeeMobile" => $jsonData->employeeMobile,
            "employeeProvinces" => $jsonData->employeeProvinces,
            "employeeAmphur" => $jsonData->employeeAmphur,
            "employeeTambon" => $jsonData->employeeTambon,
            "employeePostcode" => $jsonData->employeePostcode,
            "employeeBranch" => $jsonData->employeeBranch,

        ],
        "step2" => [
            "employeeUsername" => $jsonData->employeeUsername,
            "employeeDetail" => $jsonData->employeeDetail,
            "employeePosition" => $jsonData->employeePosition,
            "employeeSubId" => $jsonData->employeeSubId,
            "employeeBranch" => $jsonData->employeeBranch,
        ],
        "step3" => [
            "employeeImage" => $jsonData->employeeImage,
        ],
    ];
    $dataReturn = array(
        "step1" => [],
        "step2" => [],
        "step3" => [],
    );

    // set option validate
    $optionCheckStringOnly = [
        "employeeMobile",
        "employeeBranch",
        "employeeProvinces",
        "employeeAmphur",
        "employeeTambon",
        "employeePostcode",
        "employeeBranch",
        "employeePosition",
        "employeeImage",
        "createBy",
    ];
    $optionCheckNumericOnly = [
        "employeeFirstname",
        "employeeLastname",
        "employeeNickname",
        "employeeBirthday",
        "employeeEmail",
        "employeeSubId",
        "employeeUsername",
        "employeeDetail",
        "employeeAddress",
        "employeeImage",
        "createBy",
        "employeeJson",
    ];
    $optionCheckSpecialCharactersOnly = ["employeeJson", "employeeImage", "createBy"];

    // validate work
    foreach ($formStep as $key => $val) {
        $checkSpecialCharacters = $customValidation->checkSpecialCharacters($val,  $optionCheckSpecialCharactersOnly, '/[\'^£$%&*()}{#~?><>,|=+¬]/');
        $checkEmpty = $customValidation->checkEmpty($val);
        $checkTypeDataString = $customValidation->checkTypeData($val, $optionCheckStringOnly, "string");
        $checkTypeDataNumber = $customValidation->checkTypeData($val, $optionCheckNumericOnly, "number");

        $validateData = array_merge($checkSpecialCharacters, $checkEmpty, $checkTypeDataNumber, $checkTypeDataString);
        if (count($validateData) > 0) {
            $dataReturn[$key] = $validateData;
        }
    }
    return $dataReturn;
}
