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
    $bindParam[":company_id"] = $company_id;
    $sql = "SELECT 
                        discount_type.discount_type_id,
                        discount_type.discount_type_name,
                        discount_type.discount_type_name,
                        discount_choice.discount_choice_id,
                        discount_choice.discount_choice_name,
                        discount_choice.discount_choice_value
                        FROM discount_type 
                        JOIN discount_choice ON discount_type.discount_type_id = discount_choice.discount_type_id
                        WHERE 
                        discount_type.company_id = :company_id 
    ";
    if ($branch_id != 0) {
        $sql .= " AND discount_type.branch_id = :branch_id OR discount_type.branch_id = 0";
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
        $returnToFontend->message = "discount_type";
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
