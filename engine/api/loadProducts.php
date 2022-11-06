<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");
require_once("../../class/CRUD.php");

try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $company_id = $_GET["companyId"];
    $branch_id = $_GET["branchId"];

    $CRUD = new CRUD($company_id);
    $bindParam = [":company_id" => $company_id];
    $sql = "SELECT 
            product_id AS id,
            product_name AS name,
            product_price AS price,
            type_id AS type 
            FROM products 
            WHERE 
            company_id = :company_id
            AND product_active = 1
            ";
    if ($branch_id != 0) {
        $sql .= " AND branch_id = :branch_id OR branch_id = 0";
        $bindParam[":branch_id"] = $branch_id;
    }

    $data = $CRUD->select($sql, $bindParam);
    if (!$data["status"]) {
        throw new Exception($data["message"]);
    }
    if ($data["status"] && count($data) > 0) {
        $returnToFontend->message = "ok";
        $returnToFontend->results = $data["results"];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->message = "products";
        $returnToFontend->status = false;
        // $returnToFontend->message = "Data not found!";
        $returnToFontend->sendToFontend();
    }
} catch (PDOException $e) {
    $returnToFontend->status = false;
    $returnToFontend->message = "Error: " . $e->getMessage();
    $returnToFontend->returnCode = 500;
    $returnToFontend->sendToFontend();
}
