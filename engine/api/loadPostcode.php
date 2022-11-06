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
    $tambonId = $_GET["tambonId"];
    $stmt = $connectDb->conn->prepare("SELECT 
                    zip_code
                    FROM districts
                    WHERE
                    id = :id
    ;");
    $stmt->bindParam(':id', $tambonId);
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
