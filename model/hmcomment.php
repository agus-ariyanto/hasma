<?php 
class HmComment extends Model{
    protected $alias='comment';
    protected $columns=array(
        'post_id'=>'INT',
        'comment_id'=>'INT DEFAULT 0',
        'name'=>'VARCHAR(128)',
        'email'=>'VARCHAR(128)',
        'city'=>'VARCHAR(128)',
        'content'=>'TEXT',
        'tanggal'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    );
    public $join='post';
    public $child='comment';
}