<?php
class Params{
    protected $vars=array();

    function __construct(){
        $this->clear();
        $this->isGet=false;
        $this->isPost=false;
        $this->isPut=false;
        $this->isDelete=false;
        $this->parsing();
    }
    function parsing(){
        $r=strtoupper($_SERVER['REQUEST_METHOD']);
        if($r=='POST') {
            $this->isPost=true;
            $this->vars=$_POST;
            return $r;
        }

        if($r=='PUT'){
            $this->isPut=true;
            parse_str(file_get_contents("php://input"),$this->vars);
            return $r;
        }
        if($r=='GET') {
            $this->isGet=true;
            $this->vars=$_GET;
            return $r;
        }

        $this->isDelete=true;
        return 'DELETE';
    }


    function key($key){
        if(!isset($this->vars[$key])) return false;
        return $this->vars[$key];
    }
    function set($key,$value){
        $this->vars[$key]=$value;
    }
    function del($key){
        unset($this->vars[$key]);
    }
    function clear(){
        $this->vars=array();
    }
    function all(){
        return $this->vars;
    }

}
