<?php

namespace App;

class ApiResponse {

    private $_error = false;
    private $_message = "";
    private $_data = [];

    public function setError($var) {
        $this->error = $var;
    }

    public function setErrorMessage($var) {
        $this->_error = true;
        $this->_message = $var;
    }

    public function setMessage($var) {
        $this->_message = $var;
    }

    public function setData($var) {
        $this->_data = $var;
    }

    public function getError() {
        return $this->_error;
    }

    public function getMessage() {
        return $this->_message;
    }

    public function getData() {
        return $this->_data;
    }

    public function getResponse() {
        return [
            "error" => $this->_error,
            "message" => $this->_message,
            "data" => $this->_data
        ];
    }

    public function getJsonResponse() {
        return json_encode($this->getResponse());
    }

}
