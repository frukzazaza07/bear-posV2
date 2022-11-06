<?php
require_once("../../class/connect.php");
require_once("../../class/calProductMaterial.php");
require_once("returnToFontend.php");

$connectDb = new connectDb();
$returnToFontend = new returnToFontend();
$dataJson = json_decode($_POST["dataSendJson"]);
$companyBy = $dataJson->productCompany;
$branchBy = $dataJson->productBranch;
$userCreate = $dataJson->userCreate;
$validateStatus = true;
$productsInsertCheck = false;
$productSetMaterialInsertCheck = false;
$dataFailReturn = [];
$checkStep1 = [];
$checkStep2 = [];
$checkStep3 = [];
try {

    $productsSetMaterialField = array(
        'company_id',
        'branch_id',
        'product_id',
        'material_id',
        'set_material_detail',
        'set_material_value',
        'set_material_by',
        'set_material_active',
        'set_material_json',
    );
    $productsField = array(
        'product_sub_id',
        'type_id',
        'company_id',
        'branch_id',
        'product_name',
        'product_detail',
        'product_price',
        'product_vat',
        'product_by',
        'product_expired_at',
        'product_img',
        'product_active',
        'product_json',
    );

    // set data insert product
    $productsData = array();
    $productsData[0]['product_sub_id'] = trim($dataJson->productCode);
    $productsData[0]['type_id'] = trim($dataJson->productType);
    $productsData[0]['company_id'] = trim($companyBy);
    $productsData[0]['branch_id'] = ($dataJson->productBranch == "materialBranchAll" ? 0 : trim($dataJson->productBranch));
    $productsData[0]['product_name'] = trim($dataJson->productName);
    $productsData[0]['product_detail'] = trim($dataJson->productDetail);
    $productsData[0]['product_price'] = trim($dataJson->productPrice);
    $productsData[0]['product_vat'] = 0;
    $productsData[0]['product_by'] = trim($userCreate);
    $productsData[0]['product_expired_at'] = (empty($dataJson->productExpired) ? null : trim($dataJson->productExpired));


    $checkValidation = validation($productsData[0]);  // check empty && checkSpecialCharacters
    if (count($checkValidation) > 0) $validateStatus = false;


    if ($validateStatus) {
        // upload + resize
        $checkUploadFile = uploadFile($companyBy, $branchBy);
        if ($checkUploadFile["uploadPath"]) {
            $pathUploadFile = $checkUploadFile["uploadPath"];
            // insert to products
            $productsData[0]['product_img'] = $pathUploadFile;
            $productsData[0]['product_active'] = 1;
            $productsData[0]['product_json'] = json_encode($dataJson);
            $insert_values = array();
            foreach ($productsData as $d) {
                $question_marks[] = '('  . placeholders('?', sizeof($d)) . ')';
                $insert_values = array_merge($insert_values, array_values($d));
            }

            $sql = "INSERT INTO products (" . implode(",", $productsField) . ") VALUES " .
                implode(',', $question_marks);

            $stmt = $connectDb->conn->prepare($sql);
            if ($stmt->execute($insert_values)) {
                $productsInsertCheck = $connectDb->conn->lastInsertId();
            }
        } else {
            $dataFailReturn[] = $checkUploadFile;
        }

        // insert to products_material
        if ($productsInsertCheck) {
            $question_marks = array(); //set ให้ว่าง
            $lastProductsIdInsert = $productsInsertCheck;
            $productMaterialData = setProductsMaterialData($dataJson->productMaterialList, $lastProductsIdInsert, $companyBy, $userCreate);
            $insert_values = array();
            foreach ($productMaterialData as $d) {
                $question_marks[] = '('  . placeholders('?', sizeof($d)) . ')';
                $insert_values = array_merge($insert_values, array_values($d));
            }

            $sql = "INSERT INTO products_set_material (" . implode(",", $productsSetMaterialField) . ") VALUES " .
                implode(',', $question_marks);

            $stmt = $connectDb->conn->prepare($sql);
            $productSetMaterialInsertCheck = $stmt->execute($insert_values);
        }
    } else {
        $dataFailReturn[] = $checkValidation;
    }

    if ($productsInsertCheck && $productSetMaterialInsertCheck) {
        $returnToFontend->message = "เพิ่มสินค้า " . $productsData[0]['product_sub_id'] . " เรียบร้อย";
        $returnToFontend->results = [];
        $returnToFontend->sendToFontend();
    } else {
        $returnToFontend->status = false;
        $returnToFontend->message = "ไม่สามารถทำรายการได้ลองทำรายการใหม่หรือโปรดติดต่อเจ้าหน้าที่";
        $returnToFontend->results = $dataFailReturn;
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
function setProductsMaterialData($productMaterialData, $productId, $companyId, $userCreate)
{
    // "stockCutBranch";
    $newProductsSetMaterial = [];
    for ($i = 0; $i < count($productMaterialData); $i++) {
        $cutStockForBranch = $productMaterialData[$i]->stockCutBranch;
        if ($productMaterialData[$i]->stockCutBranch == "materialBranchAll") {
            $cutStockForBranch = 0;
        }
        $newProductsSetMaterial[$i]["company_id"] = $companyId;
        $newProductsSetMaterial[$i]["branch_id"] = $cutStockForBranch;
        $newProductsSetMaterial[$i]["product_id"] = $productId;
        $newProductsSetMaterial[$i]["material_id"] = $productMaterialData[$i]->materialSelect;
        $newProductsSetMaterial[$i]["set_material_detail"] = $productMaterialData[$i]->stockCutDetail;
        $newProductsSetMaterial[$i]["set_material_value"] = $productMaterialData[$i]->stockCutAmount;
        $newProductsSetMaterial[$i]["set_material_by"] = $userCreate;
        $newProductsSetMaterial[$i]["set_material_active"] = 1;
        $newProductsSetMaterial[$i]["set_material_json"] = json_encode($productMaterialData);
    }
    return $newProductsSetMaterial;
}
function uploadFile($companyId, $branchId)
{
    $defultFileSize = 1000000;
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/bear/uploads/" . $companyId . "/" . $branchId;
    $fileName = base64_encode("company-" . $companyId . "branch-" . $companyId . "-" . date("YmdHis") . microtime(true));
    $fileTypeExplode = explode(".", $_FILES["pictureFile"]["name"]);
    $imageFileType = $fileTypeExplode[count($fileTypeExplode) - 1];
    $targetFile = $target_dir . "/" . $fileName . "." . $imageFileType;
    $messageFail = [
        "message" => [],
        "uploadPath" => $targetFile,
    ];
    $messageFail["uploadPath"] = $targetFile;

    if (!getimagesize($_FILES["pictureFile"]["tmp_name"])) array_push($messageFail["message"], "Please check you file!");    // Check if image file is a actual image or fake image
    if (!checkFileSize($_FILES["pictureFile"]["size"])) array_push($messageFail["message"], "Your file size is " . convertKBtoMB($_FILES["pictureFile"]["size"]) . "MB greater than required(" . convertKBtoMB($defultFileSize) . "MB)!");
    if (!checkFileType($imageFileType)) array_push($messageFail["message"], "File type allow for jpeg and png!");
    checkFolderAlreadyExists($target_dir); // check folder if not create
    if (!resizeImageCustom($targetFile)) {
        array_push($messageFail["message"], "Can resize this file!");
    }
    if (count($messageFail["message"]) > 0) {
        $messageFail["uploadPath"] = false;
    }
    // if (!move_uploaded_file($_FILES["pictureFile"]["tmp_name"], $targetFile)) {
    //     $checkUploadFile = false;
    // }
    return $messageFail;
}
function convertKBtoMB($kb)
{
    return number_format($kb / 1000000, 2);
}
function checkFileSize($fileSize)
{
    $functionReturn = true;
    if ($fileSize > 10000000) $functionReturn = false;
    return $functionReturn;
}
function checkFileType($imageFileType)
{
    $allowFileType = ["jpg", "jpeg", "png"];
    $functionReturn = true;
    // if (!array_search($imageFileType, $allowFileType)) $functionReturn =  false;
    return $functionReturn;
}
function checkFolderAlreadyExists($targetFile)
{
    $functionReturn = true;
    if (!is_dir($targetFile)) {
        if (mkdir($targetFile, 0777, true)) {
        } else {
            $functionReturn = false;
        }
    }
    return $functionReturn;
}
//  ตัวอย่างการ resize ใช้งานได้ fix file
function resizeImage()
{
    $image_name =  $_SERVER['DOCUMENT_ROOT'] . "/bear/uploads/1/หมูสับเล็ก.jpg";

    resize_image($image_name, 50, 50, true);
}
//  ตัวอย่างการ resize ใช้งานได้ fix file
function resize_image($path, $width, $height, $update = false)
{
    // ใช้ได้ แต่ต้อง fixpath
    $size  = getimagesize($path); // [width, height, type index]
    $types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
    if (array_key_exists($size['2'], $types)) {
        $load        = 'imagecreatefrom' . $types[$size['2']];
        $save        = 'image'           . $types[$size['2']];
        $image       = $load($path);
        $resized     = imagecreatetruecolor($width, $height);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagesavealpha($resized, true);
        imagefill($resized, 0, 0, $transparent);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $size['0'], $size['1']);
        imagedestroy($image);
        return $save($resized, $update ? $path : null);
    }
}
//  ตัวอย่างการ resize ใช้งานได้รับค่าไฟล์ที่ส่งมา resize และทำการ upload ให้เลย
function resizeImageCustom($targetFile)
{
    $source         = $_FILES["pictureFile"]["tmp_name"];
    $destination    = $targetFile;
    $maxsize        = 1000;

    $size = getimagesize($source);
    $width_orig = $size[0];
    $height_orig = $size[1];
    unset($size);
    $height = $maxsize + 1;
    $width = $maxsize;
    if ($width_orig < $maxsize && $height_orig < $maxsize) {
        $height = $height_orig;
        $width = $width_orig;
    } else {
        while ($height > $maxsize) {
            //ให้มันคำนวนความสูงจากความกว้าง
            $height = round($width * $height_orig / $width_orig);
            $width = ($height > $maxsize) ? --$width : $width;
        }
    }


    unset($width_orig, $height_orig, $maxsize);
    $images_orig    = imagecreatefromstring(file_get_contents($source));
    $photoX         = imagesx($images_orig);
    $photoY         = imagesy($images_orig);
    $images_fin     = imagecreatetruecolor($width, $height);
    imagesavealpha($images_fin, true);
    $trans_colour   = imagecolorallocatealpha($images_fin, 0, 0, 0, 127);
    imagefill($images_fin, 0, 0, $trans_colour);
    unset($trans_colour);
    ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width + 1, $height + 1, $photoX, $photoY);
    unset($photoX, $photoY, $width, $height);
    $checkUploadFile = imagepng($images_fin, $destination); // imagejpeg
    unset($destination);
    ImageDestroy($images_orig);
    ImageDestroy($images_fin);
    return $checkUploadFile;
}
function validation($dataForCheck)
{
    $returnFieldFail = [];
    $checkSpecialCharacters = checkSpecialCharacters($dataForCheck);
    $checkEmpty = checkEmpty($dataForCheck);
    $checkTypeData = checkTypeData($dataForCheck);

    if (count($checkEmpty) > 0) {
        foreach ($checkEmpty as $key => $val) {
            $returnFieldFail["step1"]["results"][] = $val;
        }
    };
    if (count($checkSpecialCharacters) > 0) {
        foreach ($checkSpecialCharacters as $key => $val) {
            $returnFieldFail["step1"]["results"][] = $val;
        }
    }
    if (count($checkTypeData) > 0) {
        foreach ($checkTypeData as $key => $val) {
            $returnFieldFail["step1"]["results"][] = $val;
        }
    }
    // $returnFieldFail["step1"]["results"][] = $checkSpecialCharacters;
    // $returnFieldFail["step1"]["results"][] = $checkEmpty;
    // $returnFieldFail["step1"]["results"][] = $checkTypeData;
    if (isset($returnFieldFail["step1"]) && count($returnFieldFail["step1"]["results"]) > 0) {
        $returnFieldFail["step1"]["status"] = false;
    }
    return $returnFieldFail;
}
function checkSpecialCharacters($dataForCheck)
{
    /* '/[\'^£$%&*()}{@#~?><>,|=+¬"]/' */

    $returnFieldFail = [];
    foreach ($dataForCheck as $key => $val) {
        $specialCharacters = '/[\'^£$%&*()}{@#~?><>,|=_+¬-]/';
        if ($key == "product_sub_id") {
            $specialCharacters = '/[\'^£$%&*}{@#~?><>,|=+¬"]/';
        }
        if ($key == "product_expired_at") {
            $specialCharacters = '/[\'^£$%&*()}{@#~?><>,|=_+¬"]/';
        }
        if (preg_match($specialCharacters, $val)) {
            $returnFieldFail[] = $key . " can't use special characters!";
        }
    }
    return $returnFieldFail;
}
function checkEmpty($dataForCheck)
{
    $returnFieldFail = [];
    foreach ($dataForCheck as $key => $val) {
        if (empty($val) && $key != "product_expired_at" && $val != 0) {
            $returnFieldFail[] = $key . " is not empty!";
        }
    }
    return $returnFieldFail;
}
function checkTypeData($dataForCheck)
{
    $returnFieldFail = [];
    foreach ($dataForCheck as $key => $val) {
        if (!is_string($val) && $key != "product_vat" && $key != "product_price" && $key != "product_expired_at" && $key != "branch_id") {
            $returnFieldFail[] = $key . " type is not string!";
        }
        if (!is_numeric($key) && $key == "product_vat" && $key == "product_price" && $key == "product_expired_at" && $key == "branch_id") {
            $returnFieldFail[] = $key . " type is not number!";
        }
    }
    return $returnFieldFail;
}
function setValidationDataReturn($dataForSet)
{
    foreach ($dataForSet as $key => $val) {
        $checkStep1["results"][] = $val;
    }
    // return $returnData;
}
