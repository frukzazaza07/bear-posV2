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
    $testData = array(
        "admin",
        "1234",
    );
    if (isset($formLogin->userUsername)) {
        $authUsers->userUsername = $formLogin->userUsername;
        $authUsers->userPassword = $formLogin->userPassword;
    }
    if (isset($formLogin->socialLogin)) {
        $authUsers->socialLogin = $formLogin->socialLogin;
    }
    $userLoginCheck = $authUsers->userLogin($formLogin);
    if (is_array($userLoginCheck) && count($userLoginCheck) == 0) {
        $authUsers->setUserFieldRegister = ["social_login", "users_email", "users_firstname", "users_lastname", "users_active"];
        $setRegisterData = setUserField(json_decode($_POST["formLogin"]));
        $userRegisterCheck = $authUsers->userRegister($setRegisterData);
    }
    if (is_array($userLoginCheck) && count($userLoginCheck) > 0 || $userRegisterCheck === true) {
        $_SESSION["usersLogin"] = [
            "userId" => 1,
            "companyUserId" => 1,
            "companyUserFirstname" => "วณัฐพงศ์",
            "companyUserLastname" => "บุญปัญญา",
            "companyUserNickname" => "ฟลุ๊ค",
            "companyId" => 1,
            "branchId" => 1
        ];
        $returnToFontend->message = "Login Successfuly.";
        $returnToFontend->results = $_SESSION["usersLogin"];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message =
            "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง :(";
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
