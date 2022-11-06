<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");


try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $company_id = $_GET["companyId"];
    $branch_id = $_GET["branchId"];
    $billId = $_GET["billId"];
    $billSubId = $_GET["billSubId"];
    $billCancelField = ["bill_id", "company_id", "branch_id", "bill_sub_id", "payment_id", "bill_sum", "bill_discount", "bill_vat", "bill_total_sum", "bill_money_pay", "bill_money_change", "bill_order_count", "bill_order_count_all", "bill_active", "bill_by", "bill_json", "bill_created_at", "bill_updated_at"];
    $stmt = $connectDb->conn->prepare("UPDATE 
                        bills
                        SET bill_active = 0 
                        WHERE 
                        bills.company_id = :company_id 
                        AND bills.branch_id = :branch_id
                        AND bills.bill_id = :bill_id
                        AND bills.bill_sub_id = :bill_sub_id
                        ");

    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':branch_id', $branch_id);
    $stmt->bindParam(':bill_id', $billId);
    $stmt->bindParam(':bill_sub_id', $billSubId);
    $stmt->execute();

    $stmt = $connectDb->conn->prepare("INSERT INTO cancel_bills(" . implode(',', $billCancelField) . ")
                        SELECT " . implode(',', $billCancelField) . " FROM bills
                        WHERE 
                        bills.company_id = :company_id 
                        AND bills.branch_id = :branch_id
                        AND bills.bill_id = :bill_id
                        AND bills.bill_sub_id = :bill_sub_id
                        ");

    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':branch_id', $branch_id);
    $stmt->bindParam(':bill_id', $billId);
    $stmt->bindParam(':bill_sub_id', $billSubId);


    if ($stmt->execute()) {
        //ให้ไปจัดรูปแบบข้อมูลที่ fontend ถ้าจัดที่ backend เดี๋ยว request เยอะแล้วช้า
        $returnToFontend->message = "Delete bill: " . $billSubId . " success.";
        $returnToFontend->results = [];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = "Something went wrong!!";
        $returnToFontend->sendToFontend();
    }
} catch (PDOException $e) {
    $returnToFontend->status = false;
    $returnToFontend->message = "Error: " . $e->getMessage();
    $returnToFontend->returnCode = 500;
    $returnToFontend->sendToFontend();
}
