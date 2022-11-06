<?php
require_once("../../class/connect.php");
require_once("../../class/calProductMaterial.php");
require_once("../../class/CustomValidation.php");
require_once("returnToFontend.php");

$connectDb = new connectDb();
$calProductMaterial = new calProductMaterial($_POST["companyBy"], $_POST["branchBy"]);
$returnToFontend = new returnToFontend();
// $connectDb->conn->beginTransaction(); // also helps speed up your inserts.
try {
    $ordersAll = json_decode($_POST["ordersAll"]);
    $discountAll = json_decode($_POST["discountAll"]);
    $paymentType = $_POST["paymentType"];
    $currentShiftId = $_POST["currentShiftId"];
    $billSum = $_POST["sum"];
    $billVat = $_POST["vat"];
    $billDiscount = $_POST["discount"];
    $billTotalSum = $_POST["totalSum"];
    $billOrderCount = $_POST["orderCount"];
    $billOrderCountAll = $_POST["orderCountAll"];
    $billMoneyPay = $_POST["moneyPay"];
    $billCashChange = $_POST["cashChange"];
    $billBy = $_POST["billBy"];
    $slipUpload = (isset($_POST["slipFile"]) ? json_decode($_POST["slipFile"]) : "");
    $companyBy = $_POST["companyBy"];
    $branchBy = $_POST["branchBy"];
    $billDiscountInsertCheck = true;
    $updateStockCheck = false;
    $billSubId = genBillSubId($connectDb, $companyBy, $branchBy);
    $billsFields = array("company_id", "branch_id", "bill_sub_id", "payment_id", "bill_of_shift " ,"bill_sum", "bill_vat", "bill_discount", "bill_total_sum", "bill_money_pay", "bill_money_change", "bill_order_count", "bill_order_count_all", "bill_slip_img", "bill_by");
    $ordersFields = array("company_id", "branch_id", "bill_id", "bill_sub_id", "order_product_id", "order_product_amount", "order_product_price", "order_product_total_sum");
    $setBillDiscountField = array("company_id", "branch_id", "bill_id", "bill_sub_id", "discount_id", "bill_discount_value", "bill_discount_total_value", "discount_detail", "bill_discount_count");
    $pathUploadSlipFile = "";
    // ถ้ามีการแนบ slip
    if ((int) $paymentType != 1) {
        $statusUpload = uploadImage($slipUpload, $companyBy, $branchBy);
        if ($statusUpload["status"] == false) {
            throw new Exception("Can't upload slip file!");
        } else {
            $pathUploadSlipFile = $statusUpload["pathUpload"];
        }
    }
    // insert to bills
    $stmt = $connectDb->conn->prepare("INSERT INTO bills(" . implode(",", $billsFields) . ") 
        VALUES(
        :companyBy,
        :branchBy,
        :billSubId,
        :paymentType,
        :billOfShift ,
        :billSum,
        :billVat,
        :billDiscount,
        :billTotalSum,
        :billMoneyPay,
        :billCashChange,
        :billOrderCount,
        :billOrderCountAll,
        :billSlipImg,
        :billBy
        );");
    $stmt->bindParam(':companyBy', $companyBy);
    $stmt->bindParam(':branchBy', $branchBy);
    $stmt->bindParam(':billSubId', $billSubId);
    $stmt->bindParam(':paymentType', $paymentType);
    $stmt->bindParam(':billOfShift', $currentShiftId);
    $stmt->bindParam(':billSum', $billSum);
    $stmt->bindParam(':billVat', $billVat);
    $stmt->bindParam(':billDiscount', $billDiscount);
    $stmt->bindParam(':billTotalSum', $billTotalSum);
    $stmt->bindParam(':billMoneyPay', $billMoneyPay);
    $stmt->bindParam(':billCashChange', $billCashChange);
    $stmt->bindParam(':billOrderCount', $billOrderCount);
    $stmt->bindParam(':billOrderCountAll', $billOrderCountAll);
    $stmt->bindParam(':billSlipImg', $pathUploadSlipFile);
    $stmt->bindParam(':billBy', $billBy);
    $stmt->execute();
    $billLastId = $connectDb->conn->lastInsertId();

    // insert to orders
    $ordersAll = setOrdersAll($ordersAll, $billLastId, $billSubId);
    $insert_values = array();
    foreach ($ordersAll as $d) {
        $question_marks[] = '('  . placeholders('?', sizeof($d)) . ')';
        $insert_values = array_merge($insert_values, array_values($d));
    }

    $sql = "INSERT INTO orders (" . implode(",", $ordersFields) . ") VALUES " .
        implode(',', $question_marks);

    $stmt = $connectDb->conn->prepare($sql);
    $ordersInsertCheck = $stmt->execute($insert_values);
    $question_marks = array(); //set ให้ว่าง

    // insert to bill_discount
    if (count($discountAll) > 0) {
        $discountAll = setDiscountAll($discountAll, $billLastId, $billSubId, $companyBy, $branchBy);
        $insert_values = array();
        foreach ($discountAll as $d) {
            $question_marks[] = '('  . placeholders('?', sizeof($d)) . ')';
            $insert_values = array_merge($insert_values, array_values($d));
        }

        $sql = "INSERT INTO bill_discount (" . implode(",", $setBillDiscountField) . ") VALUES " .
            implode(',', $question_marks);

        $stmt = $connectDb->conn->prepare($sql);
        $billDiscountInsertCheck = $stmt->execute($insert_values);
    }

    // update stock
    if ($ordersInsertCheck && $billDiscountInsertCheck) {
        $updateStockCheck = $calProductMaterial->updateNewStock($billLastId);
    }
    // $connectDb->conn->commit();
    if ($updateStockCheck == 1) {
        $returnToFontend->message = "เลขที่รายการ: " . $billSubId;
        $returnToFontend->results = ["billId" => $billLastId, "billSubId" => $billSubId];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = "ไม่สามารถทำรายการได้";
        $returnToFontend->results = $updateStockCheck;
        $returnToFontend->sendToFontend();
    }
} catch (PDOException $e) {
    $returnToFontend->status = false;
    $returnToFontend->message = "Error: " . $e->getMessage();
    $returnToFontend->returnCode = 500;
    $returnToFontend->sendToFontend();
}
function placeholders($text, $count = 0, $separator = ",")
{
    $result = array();
    if ($count > 0) {
        for ($x = 0; $x < $count; $x++) {
            $result[] = $text;
        }
    }

    return implode($separator, $result);
}
function setOrdersAll($ordersAll, $billLastId, $billSubId)
{
    $newOrdersAll = [];
    for ($i = 0; $i < count($ordersAll); $i++) {
        $newOrdersAll[$i]["company_id"] = $ordersAll[$i]->companyId;
        $newOrdersAll[$i]["branch_id"] = $ordersAll[$i]->branchId;
        $newOrdersAll[$i]["bill_id"] = $billLastId;
        $newOrdersAll[$i]["bill_sub_id"] = $billSubId;
        $newOrdersAll[$i]["order_product_id"] = $ordersAll[$i]->id;
        $newOrdersAll[$i]["order_product_amount"] = $ordersAll[$i]->amount;
        $newOrdersAll[$i]["order_product_price"] = $ordersAll[$i]->price;
        $newOrdersAll[$i]["order_product_total_sum"] = $ordersAll[$i]->total;
    }
    return $newOrdersAll;
}
function setDiscountAll($discountAll, $billLastId, $billSubId, $companyBy, $branchBy)
{
    $newDiscountAll = [];
    for ($i = 0; $i < count($discountAll); $i++) {
        $newDiscountAll[$i]["company_id"] = $companyBy;
        $newDiscountAll[$i]["branch_id"] = $branchBy;
        $newDiscountAll[$i]["bill_id"] = $billLastId;
        $newDiscountAll[$i]["bill_sub_id"] = $billSubId;
        $newDiscountAll[$i]["discount_id"] = $discountAll[$i]->discount_choice_id;
        $newDiscountAll[$i]["bill_discount_value"] = $discountAll[$i]->discount_choice_value;
        $newDiscountAll[$i]["bill_discount_total_value"] = $discountAll[$i]->discount_choice_value * $discountAll[$i]->discount_choice_count;
        $newDiscountAll[$i]["discount_detail"] = $discountAll[$i]->discount_choice_name;
        $newDiscountAll[$i]["bill_discount_count"] = $discountAll[$i]->discount_choice_count;
    }
    return $newDiscountAll;
}
function genBillSubId($connectDb, $company_id, $branch_id)
{
    $stmt = $connectDb->conn->prepare("SELECT bill_sub_id,DATE(bill_created_at) AS bill_created_at FROM bills 
                WHERE 
                company_id = :company_id 
                AND branch_id = :branch_id
                ORDER BY bill_id DESC 
                LIMIT 1
                ");
    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':branch_id', $branch_id);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $genBills = "";
    if (count($results) > 0 && $results[0]["bill_created_at"] == date("Y-m-d")) {
        $billSubIdExplode = explode("/", $results[0]["bill_sub_id"]);
        $billNext = (int)$billSubIdExplode[1] += 1;
        $billFormat = "";
        for ($i = strlen($billNext); $i < 5; $i++) {
            $billFormat .= "0";
        }
        $genBills = date("Ymd") . "/" . $billFormat . $billNext;
    } else {
        $genBills = date("Ymd") . "/00001";
    }
    return $genBills;
}
function uploadImage($imageData, $companyId, $branchId)
{
    $customValidation = new CustomValidation();
    $targetUploads = $_SERVER['DOCUMENT_ROOT'] . "/bear/uploads/" . $companyId . "/" . $branchId . "/" . "slip_image/" . date("Ymd") . "/";
    $fileName = "slip_" . date("Ymd") . time() . rand() . '.png';
    $statusUpload = [];
    $statusUpload["status"] = true;
    $statusUpload["pathUpload"] = $targetUploads . $fileName;
    // แปลงเป็น image url ทั้ง upload ผ่าน input และ uplaod ผ่าน canvas เลย อิอิ
    $customValidation->checkFolderAlreadyExists($targetUploads); // check fodler upload ไม่มี ให้สร้างใหม่
    $sizeImage = $customValidation->checkSizeImageBase64($imageData->imageData);
    if ((int)$sizeImage > 3000000) {
        $statusUpload["status"] = false;
        $statusUpload["message"] = "Upload fail!";
    } else {

        $uploadCheck = $customValidation->uploadImageFileFromCanvasHTML($imageData->imageData, $targetUploads, $fileName);
        if (!$uploadCheck) {
            $statusUpload["status"] = false;
            $statusUpload["message"] = "Upload fail!";
        }
    }

    return $statusUpload;
}
