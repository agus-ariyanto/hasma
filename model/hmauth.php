<?php 
class HmAuth extends Model{
    protected $alias='auth';
    protected $columns=array(
        'nama'=>'VARCHAR(128)',
        'email'=>'VARCHAR(128)',
        'pwd'=>'VARCHAR(128)',
        'grup_id'=>'INT DEFAULT 1',
        'token'=>'TEXT',
    );
/*
default untuk deployment hanya satu akun admin
password admin = d033e22ae348aeb5660fc2140aec35850c4da997
*/    
    protected $firstdata=array(
        array(
            'id'=>'1',
            'nama'=>'admin hasma',
            'email'=>'admin@email.com',
            'pwd'=>'d033e22ae348aeb5660fc2140aec35850c4da997',
        ),
    );
}