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
                        SUM(bills.bill_total_sum) AS totalSum,
                        payments_type.payment_name
                        FROM bills 
                        JOIN payments_type ON bills.payment_id = payments_type.payment_id
                        WHERE 
                        bills.company_id = :company_id 
                        AND (bills.branch_id = :branch_id || bills.branch_id = 0)
                        AND bills.bill_active = 1
                        AND DATE(bills.bill_created_at) = DATE(:currentDate)
                        GROUP BY bills.payment_id
                        ");
    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':branch_id', $branch_id);
    $stmt->bindParam(':currentDate', $currentDate);
    $stmt->execute();
    $results["dailySaleTotalSumByType"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $connectDb->conn->prepare("SELECT 
                        SUM(bills.bill_sum) AS billSum,
                        SUM(bills.bill_discount) AS billDiscount
                        FROM bills 
                        JOIN payments_type ON bills.payment_id = payments_type.payment_id
                        WHERE 
                        bills.company_id = :company_id 
                        AND (bills.branch_id = :branch_id || bills.branch_id = 0)
                        AND bills.bill_active = 1
                        AND DATE(bills.bill_created_at) = DATE(:currentDate)
                        ");

    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':branch_id', $branch_id);
    $stmt->bindParam(':currentDate', $currentDate);
    $stmt->execute();
    $billSumData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $dailySaleSumAll = 0 ;
    $dailySaleDiscountSumAll = 0 ;
    if (count($billSumData) > 0) {
        $dailySaleSumAll = $billSumData[0]["billSum"];
        $dailySaleDiscountSumAll = $billSumData[0]["billDiscount"];
    }

    $stmt = $connectDb->conn->prepare("SELECT 
                        product_type.type_id,
                        product_type.type_name,
                        products.product_id,
                        products.product_name,
                        orders.order_product_price,
                        orders.order_product_amount,
                        orders.order_product_total_sum
                        FROM bills 
                        JOIN orders ON bills.bill_id = orders.bill_id
                        JOIN products ON orders.order_product_id = products.product_id
                        JOIN product_type ON products.type_id = product_type.type_id
                        WHERE 
                        bills.company_id = :company_id 
                        AND bills.branch_id = :branch_id
                        AND bills.bill_active = 1
                        AND DATE(bills.bill_created_at) = DATE('$currentDate')
                        ");
    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':branch_id', $branch_id);
    $stmt->execute();
    // $results["dailySaleTotalDetailByProductType"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $resultsDailySaleDetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
        $dailySaleTotalSumAll = 0;
        $sumType = 0;
        $countSubType = 0;
        //ให้ไปจัดรูปแบบข้อมูลที่ fontend ถ้าจัดที่ backend เดี๋ยว request เยอะแล้วช้า
        for ($i = 0; $i < count($results["dailySaleTotalSumByType"]); $i++) {
            $dailySaleTotalSumAll += $results["dailySaleTotalSumByType"][$i]["totalSum"];
        }

        //ทำ logic ใหม่ ถ้าไม่โอเคก็แค่เพิ่ม query
        for ($i = 0; $i < count($resultsDailySaleDetail); $i++) {
            if ($i > 0) {
                if ($resultsDailySaleDetail[$i]["type_id"] != $resultsDailySaleDetail[$i - 1]["type_id"]) {
                    $countSubType = 0;
                    $sumType = 0;
                }
            }

            $countSubType += $resultsDailySaleDetail[$i]["order_product_amount"];
            $sumType += $resultsDailySaleDetail[$i]["order_product_total_sum"];
            $results["dailySaleDetail"][$resultsDailySaleDetail[$i]["type_name"]]["countSubType"] = $countSubType;
            $results["dailySaleDetail"][$resultsDailySaleDetail[$i]["type_name"]]["sumType"] = $sumType;
            $results["dailySaleDetail"][$resultsDailySaleDetail[$i]["type_name"]]["subTypeDetails"][] = $resultsDailySaleDetail[$i];
        }
        //end ทำ logic ใหม่
        $results["dailySaleTotalSumAll"] = $dailySaleTotalSumAll;
        $results["dailySaleSumAll"] = $dailySaleSumAll;
        $results["dailySaleDiscountSumAll"] = $dailySaleDiscountSumAll;
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
