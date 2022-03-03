<?php

class DbSQL {
    /*
     * status koneksi sekarang adalah std Class  myslqi
     */
    protected $_handle;
    /* error */
    protected $_error;

    /*
     * query disimpan sebelum dilaksanakan untuk memudahkan debug
     */
    protected $_testqry;
    protected $_testmode=false;
    public $num_rows=0;

    protected function connect(){
        /*
         * variabel untuk konfigurasi Mysql diambilkan
         * dari ./cfg/db.php
         */
        global $db;

        if(!isset($this->_handle))  $this->_handle = new mysqli($db['host'],$db['user'],$db['pwd'],$db['name']) ;
        $con=$this->_handle;
        if($con->connect_errno){
            $this->_error=$con->connect_error;
            return false;
        }
        return $con;
    }

    protected function disconnect(){
        $con=$this->connect();
        if($con!==false) $con->close();
        return 1;
    }


    function query($val){

        $this->_testqry=$val;
        if($this->_testmode) return $val;
        $con=$this->connect();
        if($con===false) return 0;
        $result=$con->query($this->_testqry);
        $this->_error=$con->error;
        if($result===false) return 0;

        /* insert mengembalikan id terakhir */
        if (stripos($val, 'insert')!==false) return $con->insert_id;

        /*
         * select mengembalikan row record
         */
        if (stripos($val, 'select')!==false){
            $array=array();
            while ($row=$result->fetch_assoc()) {
                $temp=array();
                foreach($row as $key=>$value){

                    /*$val=is_null($value) ? "\"\"":$value;*/
                    //$temp[$key]=$value;
                    $temp[$key]=mb_convert_encoding($value, "UTF-8");
                }
                array_push($array, $temp);
            }
            $this->num_rows=$result->num_rows;
            return $array;
        }

        /*
         * update dan delete mengembalikan affected row
         */
        if (stripos($val, 'update')!==false || stripos($val, 'delete')!==false ) return $con->affected_rows;

        /*
         * create, drop dan lainnya hanya dieksekusi
         */
        return 1;
    }

    function utf8ize( $mixed ) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
    }

    function is_connect(){
        return $this->connect()!==false;
    }

    /* membersihkan nilai postingan dengan mysql_real_escape_str */
    function sanitize($val){
        $con=$this->connect();
        return '\''.$con->real_escape_string($val).'\'';
    }

    /* menampilkan error yang telah disimpan */
    function getError(){
        return $this->_error;
    }

    function testQry(){
        return $this->_testqry;
    }
    function testMode($value=true){
        $this->_testmode=$value;
    }


  /* akhir kelas DbSQL */
}
