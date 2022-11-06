<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");


try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $company_id = $_GET["companyId"];
    $branch_id = $_GET["branchId"];
    $strSql = "SELECT * FROM product_raw_material WHERE company_id = :company_id AND branch_id = 0";
    if(!is_null($branch_id) && !empty($branch_id))
    {
        $strSql .= " OR branch_id = :branch_id";
    }

    $stmt = $connectDb->conn->prepare($strSql);
    $stmt->bindParam(':company_id', $company_id);
    if(!is_null($branch_id) && !empty($branch_id))
    {
        $stmt->bindParam(':branch_id', $branch_id);
    }
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
