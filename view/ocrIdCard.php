<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.aiforthai.in.th/ocr-id-front-iapp',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('uploadfile'=> new CURLFILE('/C:/Users/frukz/Downloads/textt.jpg')),
  CURLOPT_HTTPHEADER => array(
    'apikey: lL2ti9PvJKFZ055xI6YFrMytKqjfFk8E',
    'Content-Type: multipart/form-data',
    'Cookie: session=.eJyrVopPy0kszkgtVrKKrlZSKAFSSrmpxcWJ6alKOkp--QppmTmpCgWJRSVKsbU6oypQVMTWAgBtwmb7.YQvkCQ.R1mAuuJ_iq3Ed4Dy67AFjF-uqEU'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
