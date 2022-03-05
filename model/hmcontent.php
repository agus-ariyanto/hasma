<?php 
class HmContent extends Model{
    protected $alias='content';
    protected $columns=array(
        'title'=>'VARCHAR(64)',
        'navbar_id'=>'INT',
        'profile_id'=>'INT',
        'template'=>'VARCHAR(128)',
        'image'=>'VARCHAR(128)',
        'excerpt'=>'TEXT',
        'tanggal'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    );
}