<?php 
class HmPost extends Model{
    protected $alias='post';
    protected $columns=array(
        'title'=>'VARCHAR(64)',
        'navbar_id'=>'INT',
        'profile_id'=>'INT',
        'post_id'=>'INT',
        'template'=>'VARCHAR(128)',
        'image'=>'VARCHAR(128)',
        'excerpt'=>'TEXT',
        'content'=>'TEXT',
        'tanggal'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    );
    public $join='navbar,profile';
    public $child='post';
}