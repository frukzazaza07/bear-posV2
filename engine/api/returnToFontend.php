<?php
class returnToFontend
{
    public $status = true;
    public $message = "";
    public $results = array();
    public $returnCode = 200;
    public function sendToFontend()
    {
        $returnFormat = [
            "status" => $this->status,
            "message" => $this->message,
            "results" => $this->results,
        ];
        http_response_code($this->returnCode);
        exit(json_encode($returnFormat));
    }
}
