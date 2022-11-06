<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");


try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $companyId = base64_decode($_GET["companyId"]);
    $sql = "SELECT 
                    *
                    FROM company_branch 
                    WHERE 
                    company_id = :companyId
    ;";

    if (isset($_GET["branchId"])) {
        $sql .= " AND branch_id = :branchId";
        $branchId = $_GET["branchId"];
    }
    $stmt = $connectDb->conn->prepare($sql);
    if (isset($_GET["branchId"])) {
        $stmt->bindParam(':branchId', $branchId);
    }
    $stmt->bindParam(':companyId', $companyId);
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
