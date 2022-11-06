<?php
require_once("../../class/connect.php");
require_once("../../class/authUsers.php");
require_once("returnToFontend.php");


try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $authUsers = new authUsers();
    $testData = array(
        "frukza3000",
        "123456789",
        "wanutpong",
        "boonpunya",
        "84",
        "bangkok",
        "bangkean",
        "tahrang",
        "0865297465",
        "frukza3000@hotmail.com",
    );
    // $userRegisterCheck = $authUsers->register(json_encode($testData));
    $setRegisterData = setUserField(json_decode($_POST["registerForm"]));
    $userRegisterCheck = $authUsers->userRegister($setRegisterData);
    if ($userRegisterCheck === true) {
        //ให้ไปจัดรูปแบบข้อมูลที่ fontend ถ้าจัดที่ backend เดี๋ยว request เยอะแล้วช้า
        $returnToFontend->message = "Register Successfuly.";
        $returnToFontend->results = [];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = $userRegisterCheck;
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
    $usersDataNew["user_username"] = $usersData->userUsername;
    $usersDataNew["user_password"] = password_hash($usersData->userPassword, PASSWORD_DEFAULT);;
    $usersDataNew["user_firstname"] = $usersData->userFirstname;
    $usersDataNew["user_lastname"] = $usersData->userLastname;
    $usersDataNew["user_address"] = $usersData->address;
    $usersDataNew["user_province"] = $usersData->province;
    $usersDataNew["user_amphur"] = $usersData->amphur;
    $usersDataNew["user_tambon"] = $usersData->tambon;
    $usersDataNew["user_tel"] = $usersData->tel;
    $usersDataNew["user_active"] = 0;
    return $usersDataNew;
}
