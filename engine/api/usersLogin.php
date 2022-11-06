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
    $message = "";
    $status = false;
    if (isset($formLogin->userUsername)) {
        $authUsers->userUsername = $formLogin->userUsername;
        $authUsers->userPassword = $formLogin->userPassword;
    }
    if (isset($formLogin->socialLogin)) {
        $authUsers->socialLogin = $formLogin->socialLogin;
    }
    // มาเช็ค login facebook
    $userLoginCheck = $authUsers->userLogin();
    if (is_array($userLoginCheck) && count($userLoginCheck) == 0 && !empty($authUsers->socialLogin)) {
        // $authUsers->setUserFieldRegister = ["social_login", "users_email", "users_firstname", "users_lastname", "users_active"];
        $authUsers->setUserFieldRegister = ["social_login",  "users_firstname", "users_lastname", "users_active"];
        $setRegisterData = setUserField($formLogin);
        $userRegisterCheck = $authUsers->userRegister($setRegisterData);
    }
    if (is_array($userLoginCheck) && count($userLoginCheck) > 0 && (int) $userLoginCheck[0]["users_active"] == 1  || $userRegisterCheck === true) {
        $_SESSION["usersLogin"] = [
            "userId" => $userLoginCheck[0]["users_id"],
            // "companyUserId" => $userLoginCheck[0]["users_active"],
            "companyUserFirstname" => $userLoginCheck[0]["users_firstname"],
            "companyUserLastname" => $userLoginCheck[0]["users_lastname"],
            // "companyId" => $userLoginCheck[0]["company_id"], //ค่อยอัพเดทหลังจากสร้างร้านตัวเองเสร็จ
            // "branchId" => $userLoginCheck[0]["branch_id"]
        ];
        $status = true;
        $message = "Login successfully.";
    } else if (isset($userLoginCheck[0]["users_active"]) && (int) $userLoginCheck[0]["users_active"] == 0) {
        $message = "โปรดติดต่อ Admin เพื่ออนุมัติเข้าใช้งาน";
    } else {
        $message = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง :(";
    }
    $returnToFontend->status = $status;
    $returnToFontend->message = $message;
    $returnToFontend->results = $_SESSION;
    $returnToFontend->sendToFontend();
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
    // $usersDataNew[0]["users_email"] = $usersData->socialEmail;
    $usersDataNew[0]["users_firstname"] = $usersData->socialFirstname;
    $usersDataNew[0]["users_lastname"] = $usersData->socialLastname;
    $usersDataNew[0]["users_active"] = 0;
    return $usersDataNew;
}
