<?php
date_default_timezone_set("Asia/Bangkok");
// tasklist /v /fi "STATUS eq running"
// ImageName PID SessionName Session#    MemUsage  Status          UserName     CPUTime WindowTitle  
exec('tasklist /v /fi "STATUS eq running"', $outPut, $result);
// echo "<pre>";
// print_r($outPut);
// echo "</pre>";
// exit;
for ($i = 0; $i < count($outPut); $i++) {
    //Something to write to txt log
    $log  = $outPut[$i] . PHP_EOL;
    //Save string to log, use FILE_APPEND to append.
    file_put_contents('./log_Alert_' . date("d.m.Y-H.i.s") . '.log', $log, FILE_APPEND);
}