<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");
class calProductMaterial
{
    private $conn;
    private $companyBy;
    private $branchBy;
    function __construct($companyBy, $branchBy)
    {
        $connect = new connectDb();
        $this->conn = $connect->conn;
        $this->companyBy = $companyBy;
        $this->branchBy = $branchBy;
    }
    private function getMaterial($billId)
    {
        // ส่งจำนวนครั้งที่ซื้อมาให้ครบโดย group เป็น product_id เข้ามา
        $sql = "SELECT
                    c.company_id,
                    c.branch_id,
                    a.bill_id,
                    b.material_id,
                    c.product_id,
                    SUM(
                        b.set_material_value * a.order_product_amount
                    ) sum_cut_stock,
                    (d.material_amount - b.set_material_value * a.order_product_amount) newStock,
                    d.material_amount,
                    b.set_material_value,
                    a.order_product_amount

                FROM
                    orders a
                JOIN products_set_material b ON
                    a.order_product_id = b.product_id
                JOIN products c ON
                    a.order_product_id = c.product_id
                JOIN product_raw_material d ON
                    b.material_id = d.material_id
                    WHERE
                    a.bill_id = :billId
                    AND c.company_id = :companyBy
                    AND c.branch_id = :branchBy
                    AND set_material_active = 1
                    GROUP BY a.order_product_id
                    ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':companyBy', $this->companyBy);
        $stmt->bindParam(':branchBy', $this->branchBy);
        $stmt->bindParam(':billId', $billId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        }
        return false;
    }
    public function updateNewStock($billId)
    {
        try {
            $setFieldUpdateStockLog = array(
                "company_id",
                "branch_id",
                "bill_id",
                "material_id",
                "product_id",
                "sum_cut_stock",
                "new_stock",
                "balance_stock",
                "set_material_value",
                "order_product_amount"
            );

            $setDataStockForUpdate = $this->getMaterial($billId);
            $setMaterialId = $this->setMaterialId($setDataStockForUpdate);
            $setDataForNewStock = $this->setDataForNewStock($setDataStockForUpdate);
            $updateStockCheck = "No data in function getMaterial!!";
            if ($setDataStockForUpdate) {
                $insert_values = array();
                foreach ($setDataStockForUpdate as $d) {
                    $question_marks[] = '('  . placeholders('?', sizeof($d)) . ')';
                    $insert_values = array_merge($insert_values, array_values($d));
                }

                $sqlUpdateStockLog = "INSERT INTO update_stock_log (" . implode(",", $setFieldUpdateStockLog) . ") VALUES " .
                    implode(',', $question_marks);
                $stmt = $this->conn->prepare($sqlUpdateStockLog);
                $stmt->execute($insert_values);
                $sqlProductRawMaterial = "UPDATE product_raw_material
                    SET material_amount = ( CASE ";
                for ($i = 0; $i < count($setDataForNewStock); $i++) {
                    $sqlProductRawMaterial .= " WHEN material_id = " . $setDataForNewStock[$i]["material_id"] . " THEN material_amount - " . ((float) $setDataForNewStock[$i]["sum_cut_stock"]);
                }
                $sqlProductRawMaterial .= " END ) WHERE material_id IN(" . $setMaterialId . ")";

                $stmt = $this->conn->prepare($sqlProductRawMaterial);
                $stmt->bindParam(':billId', $billId);
                $updateStockCheck =  $stmt->execute();
            }

            return $updateStockCheck;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    private function setMaterialId($materialData)
    {
        $newData = [];
        for ($i = 0; $i < count($materialData); $i++) {
            $newData[$i] = $materialData[$i]["material_id"];
        }
        return implode(",", $newData);
    }
    private function setDataForNewStock($dataStock)
    {
        $newData = [];
        $sumNewCutStock = 0;
        for ($i = 0; $i < count($dataStock); $i++) {
            $newData[$i]["material_id"] = $dataStock[$i]["material_id"];
            for ($x = $i + 1; $x < count($dataStock); $x++) {
                if ($dataStock[$i]["material_id"] == $dataStock[$x]["material_id"]) {
                    $dataStock[$i]["sum_cut_stock"] += $dataStock[$x]["sum_cut_stock"];
                    unset($dataStock[$x]);
                }
            }
            $newData[$i]["sum_cut_stock"] = $dataStock[$i]["sum_cut_stock"];
        }
        return $newData;
    }
}
