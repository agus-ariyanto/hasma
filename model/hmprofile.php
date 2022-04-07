<?php 
class HmProfile extends Model{
    protected $alias='profile';
    protected $table='hmauth';
    protected $profile=array(
        'nama'=>'VARCHAR(128)',
        'email'=>'VARCHAR(128)',
    );
}