<?php

class Login extends Api{
    function __construct(){
        parent::__construct();
        $this->params=new Params;
        $this->addModel('auth');
    }

    function index(){
        $this->auth->andWhere('email',$this->params->key('email'));
        $this->auth->andWhere('pwd',sha1($this->params->key('password')));
        $res=$this->auth->select();
        // login failed 
        if(empty(count($res))){
            $this->data(array(
                'success'=>false,
                'message'=>'Email or Password doesn\'t match',
            ));
            return ;
        }
        // login success
        $token=$res[0]['token'];
        if(empty($token)) $token=$this->createToken($res[0]);
        $this->data(array(
            'success'=>true,
            'userdata'=>array(
                'id'=>$res[0]['id'],
                'grup_id'=>$res[0]['grup_id'],
            ),
            'token'=>$token,
        ));
    }
    
    function check(){
        $this->render(false);
        $header=getallheaders();
        if(empty($header['Authorization'])){
            $this->status(403);
            return false;
        }
        $token=str_replace('Bearer ','',$header['Authorization']);
        $this->auth->andWhere('token',trim($token));
        $this->auth->limit(1);
        $res=$this->auth->select();
        
        if(count($res)>0) {
            $auth=$res[0];
            unset($auth['pwd']);
            unset($auth['token']);
            return $auth;
        }
        $this->status(403);
        return false;
    }
    
    
    function createToken($data){
        global $jwt;
        $d=array(
            'id'=>$data['id'],
            'grup_id'=>$data['grup_id'],
        );
        $token=JWT::encode($d,$jwt['key'],$jwt['alg']);
        $this->auth->colVal('token',$token);
        $this->auth->save($data['id']);
        return $token;
    } 
    // dummy reload login
    function ok(){
        $qry=$this->auth->testQry();
        $this->data($qry);
    }
    
    
    
}