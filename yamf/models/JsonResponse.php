<?php

require_once 'Response.php';

class JsonResponse extends Response {
    public $data;
    public $jsonEncodeOptions;
    
    public function __construct($data, $jsonEncodeOptions = 0) {
        parent::__construct();
        $this->data = $data;
        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public function output($app) {
        parent::output($app);
        echo json_encode($this->data, $this->jsonEncodeOptions);
    }
}

?>