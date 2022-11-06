<?php
require_once("../../class/connect.php");
require_once("../../class/authUsers.php");
require_once("returnToFontend.php");

try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $authUsers = new authUsers();
    $formLogin = json_decode($_POST["formLogin"]);
    $userRegisterCheck = false;
    $checkPermision = false;
    $returnMessage = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง :(";

    if (isset($formLogin->userUsername)) {
        $authUsers->userUsername = $formLogin->userUsername;
        $authUsers->userPassword = $formLogin->userPassword;
    }
    $companyLoginCheck = $authUsers->companyLogin($formLogin);
    if (is_array($companyLoginCheck) &&  count($companyLoginCheck) > 0) {
        $passwordHash = $companyLoginCheck[0]["company_users_password"];
        $passwordVerify = password_verify($formLogin->userPassword, $passwordHash);
        if ($passwordVerify === true) {
            $returnMessage = "Login Successfuly.";
            if ($formLogin->pageOption == "1" && $companyLoginCheck[0]["position_id"] < 4) $checkPermision = true;
            else if ($formLogin->pageOption == "0") $checkPermision = true;
            else $returnMessage = "คุณไม่มีสิทธิ์เข้าหน้า Admin";
        }
    }

    if ($checkPermision) {
        $_SESSION["usersLogin"] = [
            "loginStatus" => true,
            "companyUserId" => $companyLoginCheck[0]["company_users_id"],
            "companyUserFirstname" => $companyLoginCheck[0]["company_users_firstname"],
            "companyUserLastname" => $companyLoginCheck[0]["company_users_lastname"],
            "companyUserNickname" => $companyLoginCheck[0]["company_users_nickname"],
            "companyId" => $companyLoginCheck[0]["company_id"],
            "branchId" => $companyLoginCheck[0]["company_branch_id"],
            "positionId" => $companyLoginCheck[0]["position_id"],
        ];
        $returnToFontend->message = $returnMessage;
        $returnToFontend->results = $_SESSION["usersLogin"];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = $returnMessage;
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
