<?php 
/*
    tipe :  1 text-image
            2 text
            3 image
            4 url-link
 */
class HmItem extends Model{ 
    protected $alias='item';
    protected $columns=array(
        'content_id'=>'INT',
        'tipe'=>'INT DEFAULT 1',
        'note'=>'TEXT',
        'image'=>'VARCHAR(128)',
    );
}