<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");
require_once("../../class/CRUD.php");

try {
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $company_id = $_GET["companyId"];
    $branch_id = $_GET["branchId"];
    $date = $_GET["currentDate"];
    $CRUD = new CRUD($company_id);
    $bindParam[":company_id"] = $company_id;
    $bindParam[":branch_id"] = $branch_id;
    $bindParam[":date"] = $date;
    $data = [];
    $data["status"] = true;
    $data["results"]["mainShift"] = [];
    $data["results"]["subShift"] = [];
    $sql = "SELECT 
            *
            FROM company_main_shift
            WHERE
            company_id = :company_id
            AND branch_id = :branch_id
            AND DATE(main_shift_created_at) = :date
            ";
    $mainShiftData = $CRUD->select($sql, $bindParam);
    if (count($mainShiftData["results"]) != 0) {
        $mainShiftId = $mainShiftData["results"][0]["main_shift_id"];
        $bindParam[":mainShiftId"] = $mainShiftId;
        $sql = "SELECT 
                *
                FROM company_sub_shift
                WHERE 
                company_id = :company_id
                AND branch_id = :branch_id
                AND DATE(sub_shift_created_at) = :date
                AND main_shift_id = :mainShiftId
                ";

        $subShiftData = $CRUD->select($sql, $bindParam);
        $data["results"]["mainShift"] = $mainShiftData["results"];
        $data["results"]["subShift"] = $subShiftData["results"];
    }
    if (count($data["results"]["mainShift"]) == 0 || count($data["results"]["subShift"]) == 0) {
        // ถ้า throw เลยมันจะดูเหมือน error เลยทั้งที่แค่ data not found
        // throw new Exception("mainShift: " . $data["mainShift"]["message"] . "subShift: " . $data["subShift"]["message"]);
        $data["status"] = false;
    }


    if ($data["status"]) {
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
