<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");


try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $formLogin = json_decode($_POST["formLogin"]);
    $companyId = $formLogin->companyBy;
    $branchId = $formLogin->branchBy;
    $password = $formLogin->confirmPassword;
    $userRegisterCheck = false;
    $companyUserFieldSet = ["company_users_id", "position_id", "company_users_firstname", "company_users_nickname"];
    $stmt = $connectDb->conn->prepare("SELECT 
                    " . implode(",", $companyUserFieldSet) . "
                    FROM company_users
                    WHERE 
                    company_id = :companyId
                    AND company_branch_id = :branchId
                    AND company_users_password = :password
                    AND position_id <= 3
    ;");
    $stmt->bindParam(':companyId', $companyId);
    $stmt->bindParam(':branchId', $branchId);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
        $returnToFontend->message = "You have permission.";
        $returnToFontend->results = [];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = "You don't have permission!!";
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
