<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");
try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $company_id = $_GET["companyId"];
    $branch_id = $_GET["branchId"];
    $currentDate = $_GET["dateSelect"];
    $stmt = $connectDb->conn->prepare("SELECT 
                        bills.*,
                        orders.order_product_amount,
                        orders.order_product_price,
                        orders.order_product_total_sum,
                        products.product_name
                        FROM bills 
                        JOIN orders ON bills.bill_id = orders.bill_id
                        JOIN products ON orders.order_product_id = products.product_id
                        WHERE 
                        bills.company_id = :company_id 
                        AND (bills.branch_id = :branch_id || bills.branch_id = 0)
                        AND DATE(bills.bill_created_at) = DATE('$currentDate')
                        ");

    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':branch_id', $branch_id);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
        //ให้ไปจัดรูปแบบข้อมูลที่ fontend ถ้าจัดที่ backend เดี๋ยว request เยอะแล้วช้า
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
