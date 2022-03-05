<?php 
class HmNavbar extends Model{
    protected $alias='navbar';
    protected $columns=array(
        'navbar_id'=>'INT DEFAULT 0',
        'title'=>'VARCHAR(24)',
        'icon'=>'VARCHAR(64)',
    );
}