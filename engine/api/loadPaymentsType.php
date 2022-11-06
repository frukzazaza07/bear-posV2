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
            *
            FROM payments_type
            WHERE 
            company_id = :company_id";
    if ($branch_id != 0) {
        $sql .= " AND branch_id = :branch_id OR branch_id = 0";
        $bindParam[":branch_id"] = $branch_id;
    }
    $data = $CRUD->select($sql, $bindParam);
    if (!$data["status"]) {
        throw new Exception($data["message"]);
    }

    if ($data["status"] && count($data) > 0) {
        //ให้ไปจัดรูปแบบข้อมูลที่ fontend ถ้าจัดที่ backend เดี๋ยว request เยอะแล้วช้า
        $returnToFontend->message = "ok";
        $returnToFontend->results = $data["results"];
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
