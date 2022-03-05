<?php
class Base{
    protected $_render=true;
    function __construct(){
        global $query;
        /* init session,post dan get */
        $this->query=$query;
        // $this->Post=new Post;
        // $this->Get=new Get;
        $this->_status=200;
        $this->tpl=array();
    }
    function addtemplate($value){
        array_push($this->tpl,$value);
    }

    /* akhir merender format json variabel $result*/
    function __destruct(){
        if($this->_status>299) $this->_render=false;
        foreach($this->tpl as $value) 
            include ROOT_DIR.DS.'html'.DS.$value;
        http_response_code($this->_status);
    }

    function data($val){
        $this->_result=$val;
    }

    function render($value=null){
        $this->_render=$value==1;
    }

    function status($code){
        $this->_status=$code;
    }
    
    function addModel($model){
        global $prefix;
        $tbl=ucfirst(strtolower($prefix)).ucfirst(strtolower($model));
        if(!class_exists($tbl)) return false;
        if(isset($this->$model)) return true;
        $this->$model=new $tbl;
        return true;
    }
    
    
}
