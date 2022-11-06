<?php
class CustomValidation
{
    // public $optionIndexForCheck = [];

    public function validation($dataForCheck)
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
        if (isset($returnFieldFail["step1"]) && count($returnFieldFail["step1"]["results"]) > 0) {
            $returnFieldFail["step1"]["status"] = false;
        }
        return $returnFieldFail;
    }
    public function checkSpecialCharacters($dataForCheck, $optionIgnoreIndexForCheck = [], $specialCharacters = '/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $message = " can't use special characters!")
    {
        $returnFieldFail = [];
        if (count($optionIgnoreIndexForCheck) > 0) {
            foreach ($dataForCheck as $key => $val) {
                if (array_search($key, $optionIgnoreIndexForCheck) === false && preg_match($specialCharacters, $val)) {
                    $returnFieldFail[] = $key . " '" . $val . "'" . $message;
                }
            }
        } else {
            foreach ($dataForCheck as $key => $val) {
                if (preg_match($specialCharacters, $val)) {
                    $returnFieldFail[] = $key . " '" . $val . "'" . $message;
                }
            }
        }
        return $returnFieldFail;
    }
    public function checkEmpty($dataForCheck, $optionIgnoreIndexForCheck = [], $message = " is not empty!")
    {
        $returnFieldFail = [];
        if (count($optionIgnoreIndexForCheck) > 0) {
            foreach ($dataForCheck as $key => $val) {
                if ($val === "" && array_search($key, $optionIgnoreIndexForCheck) === false) {
                    $returnFieldFail[] = $key . " '" . $val . "'" . $message;
                }
            }
        } else {
            foreach ($dataForCheck as $key => $val) {
                if ($val === "") {
                    $returnFieldFail[] = $key . " '" . $val . "'" . $message;
                }
            }
        }
        return $returnFieldFail;
    }
    public function checkTypeData($dataForCheck, array $optionIgnoreIndexForCheck = [], string $typeForCheck = "", $message = "")
    {
        if (empty($message) && $typeForCheck == "string") {
            $message = " type is not string!";
        }
        if (empty($message) && $typeForCheck == "number") {
            $message = " type is not number!";
        }
        $returnFieldFail = [];
        if (count($optionIgnoreIndexForCheck) > 0) {
            foreach ($dataForCheck as $key => $val) {
                if ($typeForCheck == "string" && !is_string($val) && array_search($key, $optionIgnoreIndexForCheck) === false) {
                    $returnFieldFail[] = $key . " '" . $val . "'" . $message;
                }
                if ($typeForCheck == "number" && !is_numeric($val) && array_search($key, $optionIgnoreIndexForCheck) === false) {
                    $returnFieldFail[] = $key . " '" . $val . "'" . $message;
                }
            }
        } else {
            foreach ($dataForCheck as $key => $val) {
                if ($typeForCheck == "string" && !is_string($val)) {
                    $returnFieldFail[] = $key . " '" . $val . "'" . $message;
                }
                if ($typeForCheck == "number" && !is_numeric($val)) {
                    $returnFieldFail[] = $key . " '" . $val . "'" . $message;
                }
            }
        }
        return $returnFieldFail;
    }

    // validate file

    private function convertKBtoMB($kb) // เอาไว้โชว์เป็น text
    {
        return number_format($kb / 1000000, 2);
    }
    public function checkFileSize($file)
    {
        $fileSize = $file["pictureFile"]["size"]; //$_FILES["pictureFile"]["size"]
        $functionReturn = true;
        if ($fileSize > 1000000) $functionReturn = false;
        return $functionReturn;
    }
    public function checkFileType($file)
    {
        $fileTypeExplode = explode(".", $file["pictureFile"]["name"]); //$_FILES["pictureFile"]["name"]
        $imageFileType = $fileTypeExplode[count($fileTypeExplode) - 1];
        $allowFileType = ["jpg", "jpeg", "png"];
        $functionReturn = true;
        if (!array_search($imageFileType, $allowFileType)) $functionReturn =  false;
        return $functionReturn;
    }
    public function checkFolderAlreadyExists($targetFile)
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
    public function resizeImage()
    {
        $image_name =  $_SERVER['DOCUMENT_ROOT'] . "/bear/uploads/1/หมูสับเล็ก.jpg";

        resize_image($image_name, 50, 50, true);
    }
    //  ตัวอย่างการ resize ใช้งานได้ fix file (เอา file ที่ upload ไป แล้วมา resize)
    public function resize_image($path, $width, $height, $update = false)
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
    public function resizeImageCustom($file, $targetFile)
    {
        $source         = $file["pictureFile"]["tmp_name"]; //$_FILES["pictureFile"]["tmp_name"];
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
    function uploadImageFileFromCanvasHTML($canvasImage, $uploadPath, $filename)
    {
        // get image data form canvas html5
        $uploadDir = $uploadPath;  //implement this function yourself
        $data = $canvasImage;
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $uploadStatus = file_put_contents($uploadDir . $filename, $data);

        return $uploadStatus;
    }
    function checkSizeImageBase64($base64Img)
    {
        $data = $base64Img;
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $n = strlen($data);
        $y = 0;
        $yCheck = str_split($data);
        if ($yCheck[count($yCheck) - 1] == "=" && $yCheck[count($yCheck) - 2] == "=") {
            $y = 2;
        } else if ($yCheck[count($yCheck) - 1] == "=") {
            $y = 1;
        }
        $sizebase64Image =  ($n * (3 / 4)) - $y;
        return $sizebase64Image;
        // x = (n * (3/4)) - y
        // Where,
        // x is the size of file in bytes
        // n is the length of the Base64 String
        // y will be 2 if Base64 ends with '==' and 1 if Base64 ends with '='.
    }
}
