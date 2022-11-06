<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");
class CRUD
{
    private $conn;
    private $companyBy;
    public $fieldSet = [];
    private $returnData = [
        "status" => true,
        "message" => "",
        "results" => [],
    ];
    function __construct($companyBy)
    {
        $connect = new connectDb();
        $this->conn = $connect->conn;
        $this->companyBy = $companyBy;
    }
    public function insert($data = [], $fieldSet = [], $tableName = "")
    {
        try {
            // set data ให้ key เหมือน column ในฐานข้อมูลให้เรียบร้อย
            $question_marks = array(); //set ให้ว่าง
            $insert_values = array();
            foreach ($data as $d) {
                $question_marks[] = '('  . $this->placeholders('?', sizeof($d)) . ')';
                $insert_values = array_merge($insert_values, array_values($d));
            }
            $sql = "INSERT INTO $tableName (" . implode(",", $fieldSet) . ") VALUES " .
                implode(',', $question_marks);

            $stmt = $this->conn->prepare($sql);
            // $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->execute($insert_values)) {
                $this->returnData["insertId"] = $this->conn->lastInsertId();
                $this->returnData["message"] = "Insert successfully.";
            } else {
                $this->returnData["status"] = false;
                $this->returnData["message"] = "Insert fail!";
            }
            return $this->returnData;
        } catch (PDOException $e) {
            $this->returnData["status"] = false;
            $this->returnData["message"] = "Error: " . $e->getMessage();
            return $this->returnData;
        }
    }
    public function select($sql, $bind)
    {
        try {
            $sqlStr = $sql;
            $stmt = $this->conn->prepare($sqlStr);
            $stmt->execute($bind);
            $this->returnData["results"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->returnData;
        } catch (PDOException $e) {
            $this->returnData["status"] = false;
            $this->returnData["message"] = "Error: " . $e->getMessage();
            return $this->returnData;
        }
    }
    public function updateWhere($data = [], $fieldSet = [], $tableName = "", $whereField = [], $whereData = [])
    {
        try {
            // set data ให้ key เหมือน column ในฐานข้อมูลให้เรียบร้อย
            $sqlUpdateSet = $this->updateSetSyntax($fieldSet);
            $sqlUpdateWhere = $this->updateSetSyntax($whereField, "");
            $updateValue = array_merge($data, $whereData);
            // UPDATE MyGuests SET lastname='Doe' WHERE id=2
            $sql = "UPDATE $tableName SET $sqlUpdateSet WHERE $sqlUpdateWhere";
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                $this->returnData["status"] = false;
                $this->returnData["message"] = trigger_error($this->mysqli->error, E_USER_ERROR);
                return $this->returnData;
            }
            // $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($stmt->execute($updateValue)) {
                $this->returnData["message"] = "update successfully.";
            } else {
                $this->returnData["status"] = false;
                $this->returnData["message"] = "update fail!";
            }
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
    private function updateSetSyntax($field, $comma = ",") //$comma ไว้แยกว่า set data ธรรมดาหรือ data ตรง where
    {
        $dataReturn = "";
        $i = 0;
        foreach ($field as $key => $val) {
            if ($i == count($field) - 1) {
                $dataReturn .= $val . "=? ";
            } else {
                $dataReturn .= $val . "=?$comma ";
            }

            $i++;
        }
        return $dataReturn;
    }
    public function manualSql($sqlStr, $lastInsertId = false)
    {
        try {
            $stmt = $this->conn->prepare($sqlStr);
            if (!$stmt->execute()) {
                throw new Exception("Error: " . $this->conn->errorInfo());
            }
            $this->returnData["message"] = "successfully.";
            if($lastInsertId == true){
                $this->returnData["insertId"] = $this->conn->lastInsertId();
            }
            return $this->returnData;
        } catch (PDOException $e) {
            $this->returnData["status"] = false;
            $this->returnData["message"] = "Error: " . $e->getMessage();
            return $this->returnData;
        }
    }
}
