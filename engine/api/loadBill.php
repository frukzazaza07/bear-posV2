<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");


try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $billId = base64_decode($_GET["billId"]);
    $companyId = $_GET["companyId"];
    $branch_id = $_GET["branchId"];
    $stmt = $connectDb->conn->prepare("SELECT
                    orders.bill_id, 
                    orders.bill_sub_id, 
                    bill_order_count_all,
                    bill_sum, 
                    bill_vat,
                    bill_discount,
                    bill_total_sum,
                    bill_money_pay,
                    bill_money_change,
                    product_name,
                    order_product_amount,
                    order_product_price,
                    order_product_total_sum
                    FROM bills 
                    JOIN orders ON bills.bill_id = orders.bill_id
                    JOIN products ON orders.order_product_id = products.product_id
                    WHERE 
                    bills.bill_id = :billId
                    AND bills.bill_active = 1
    ;");
    $stmt->bindParam(':billId', $billId);
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
