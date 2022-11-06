<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");


try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $companyId = base64_decode($_GET["companyId"]);
    $branchId = base64_decode($_GET["branchId"]);
    $stmt = $connectDb->conn->prepare("SELECT 
                    *
                    FROM company_users_position 
                    WHERE 
                    company_id = :companyId 
                    AND branch_id = :branchId
    ");
    //                    AND (branch_id = :branch_id OR branch_id = 0)
    $stmt->bindParam(':companyId', $companyId);
    $stmt->bindParam(':branchId', $branchId);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
        $returnToFontend->message = "ok";
        $returnToFontend->results = $results;
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = "Data not found!";
        $returnToFontend->sendToFontend();
    }
} catch (PDOException $e) {
    $returnToFontend->status = false;
    $returnToFontend->message = "Error: " . $e->getMessage();
    $returnToFontend->returnCode = 500;
    $returnToFontend->sendToFontend();
}
