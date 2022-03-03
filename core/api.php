<?php 

class Api extends Base{
/* akhir merender format json variabel $result
    */
    function __destruct(){
        if($this->_status>299) $this->_render=false;
        if($this->_render) {
            header('Content-Type: application/json');
            echo json_encode($this->_result);
        }
        http_response_code($this->_status);
    }    
}