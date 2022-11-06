<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");
require_once("../../class/CRUD.php");

try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $jsonData = json_decode($_POST["jsonData"]);

    $companyId = base64_decode($jsonData[0]->companyId);
    $branchId = base64_decode($jsonData[0]->branchId);
    $typeReport = $jsonData[0]->typeReport;
    $startDate = $jsonData[0]->startDate;
    $endDate = $jsonData[0]->endDate;
    $selectDate = $jsonData[0]->selectDate;
    $CRUD = new CRUD($companyId);
    $bindParam[":companyId"] = $companyId;
    $bindParam[":branchId"] = $branchId;

    // 0=betweenDate 1=รายวัน 2=ย้อนหลัง7วัน 3=รายเดือน 4=รายปี
    $sql = "SELECT *";
    $groupBy = "";
    $where = "";
    switch ($typeReport) {
        case 0:
            $where = " AND DATE(bill_created_at) BETWEEN DATE('$startDate') AND DATE('$endDate')";
            break;
        case 1:
            // $sql .= " , bill_created_at";
            $where = " AND DATE(bill_created_at) = DATE('$selectDate')";
            break;
        case 2:
            $sql .= " , DATE(bill_created_at) AS created_at, SUM(bill_order_count_all) AS daily_order_count, SUM(bill_sum) AS daily_sum_sale,SUM(bill_discount) AS daily_discount, SUM(bill_total_sum) AS daily_total_sale";
            $where = " AND DATE(bill_created_at) BETWEEN DATE('$selectDate') - INTERVAL 7 DAY AND DATE('$selectDate')";
            $groupBy = " GROUP BY DATE(bill_created_at)";
            break;
        case 3:
            $sql .= " , DATE(bill_created_at) AS created_at, SUM(bill_order_count_all) AS daily_order_count, SUM(bill_sum) AS daily_sum_sale,SUM(bill_discount) AS daily_discount, SUM(bill_total_sum) AS daily_total_sale";
            $where = " AND MONTH(bill_created_at) = MONTH('$selectDate')";
            $groupBy = " GROUP BY DATE(bill_created_at)";
            break;
        case 4:
            $sql .= " , CONCAT(
                YEAR(bill_created_at)
                , '-'
                , IF(CHAR_LENGTH(MONTH(bill_created_at)) > 1,MONTH(bill_created_at),CONCAT('0', MONTH(bill_created_at))))
                AS created_at, SUM(bill_order_count_all) AS daily_order_count, SUM(bill_sum) AS daily_sum_sale,SUM(bill_discount) AS daily_discount, SUM(bill_total_sum) AS daily_total_sale";
            $where = " AND YEAR(bill_created_at) = YEAR('$selectDate')";
            $groupBy = " GROUP BY MONTH(bill_created_at)";
            break;
    }
    $sql .= " FROM bills
            WHERE 
            company_id = :companyId
            AND branch_id = :branchId
            $where
            $groupBy
            ";
    $data = $CRUD->select($sql, $bindParam);
    if (!$data["status"]) {
        throw new Exception($data["message"]);
    }
    if ($data["status"] && count($data["results"]) > 0) {
        $data["results"]["sumAll"] = calAllTotalSum($data["results"]);

        //ให้ไปจัดรูปแบบข้อมูลที่ fontend ถ้าจัดที่ backend เดี๋ยว request เยอะแล้วช้า
        $returnToFontend->message = "ok";
        $returnToFontend->results = $data["results"];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = "Data sales not found!";
        $returnToFontend->sendToFontend();
    }
} catch (PDOException $e) {
    $returnToFontend->status = false;
    $returnToFontend->message = "Error: " . $e->getMessage();
    $returnToFontend->returnCode = 500;
    $returnToFontend->sendToFontend();
}

function calAllTotalSum($dataCal)
{
    $dataReturn = 0;
    if (isset($dataCal[0]["daily_total_sale"])) {
        for ($i = 0; $i < count($dataCal); $i++) {
            $dataReturn += $dataCal[$i]["daily_total_sale"];
        }
    } else {
        for ($i = 0; $i < count($dataCal); $i++) {
            $dataReturn += $dataCal[$i]["bill_total_sum"];
        }
    }

    return $dataReturn;
}
