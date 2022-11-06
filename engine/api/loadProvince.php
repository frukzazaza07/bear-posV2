<?php
require_once("../../class/connect.php");
require_once("returnToFontend.php");


try {
    $omega = base64_decode($_GET["omega"]);
    if ($omega != "secret") {
        $returnToFontend->status = false;
        $returnToFontend->message = "Fail to request.";
        $returnToFontend->sendToFontend();
    }
    $connectDb = new connectDb();
    $returnToFontend = new returnToFontend();
    $stmt = $connectDb->conn->prepare("SELECT 
                    id,
                    name_th,
                    name_en
                    FROM provinces
                    ORDER BY name_th
    ;");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
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
