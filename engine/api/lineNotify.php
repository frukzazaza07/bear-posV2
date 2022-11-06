<?php

$jsonData = json_decode($_POST["jsonData"]);
$openShiftBy = $jsonData->loginBy;
$companyId = $jsonData->companyId;
$branchId = $jsonData->branchId;
$lineToken = $jsonData->lineToken;
$lineMessage = $jsonData->lineMessage;

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://notify-api.line.me/api/notify',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array("message" =>$lineMessage),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $lineToken
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
