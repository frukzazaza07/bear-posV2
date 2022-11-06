<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");
class addMaterial
{
    private $conn;
    private $companyBy;
    public $fieldMaterial = [
        "company_id",
        "branch_id",
        "material_sub_id",
        "material_name",
        "material_detail",
        "material_by",
        "material_amount",
        "material_insert",
        "material_expired_at",
        "material_active",
        "material_json",
    ];
    private $returnData = [
        "status" => true,
        "message" => "",
    ];
    function __construct($companyBy)
    {
        $connect = new connectDb();
        $this->conn = $connect->conn;
        $this->companyBy = $companyBy;
    }
    public function insert($data = [])
    {
        try {
            // set data ให้ key เหมือน column ในฐานข้อมูลให้เรียบร้อย
            $question_marks = array(); //set ให้ว่าง
            $insert_values = array();
            foreach ($data as $d) {
                $question_marks[] = '('  . $this->placeholders('?', sizeof($d)) . ')';
                $insert_values = array_merge($insert_values, array_values($d));
            }

            $sql = "INSERT INTO product_raw_material (" . implode(",", $this->fieldMaterial) . ") VALUES " .
                implode(',', $question_marks);

            $stmt = $this->conn->prepare($sql);
            $materialInsertCheck = $stmt->execute($insert_values);
            return $this->returnData;
        } catch (PDOException $e) {
            $this->returnData["status"] = false;
            $this->returnData["message"] = "Error: " . $e->getMessage();
            return $this->returnData;
        }
    }
    private function placeholders($text, $count = 0, $separator = ",")
    {
        $result = array();
        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }
        return implode($separator, $result);
    }
}
