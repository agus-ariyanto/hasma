<?php
class DbCustom extends DbSQL{

/*
untuk table yang diturunka dari model lebih baik
pakai dbjoin(core/dbjoin.php) atau langsung lihat kelas Model (core/model.php);

aturan memakai class Model dan DbJoin
id          : auto incremen, primary key
alias_id    : foreign key tabel join dan tabel child
nama tabel menurut property $this->table atau nama model
nama model adalah prefix dan nama panggilan di controllernya

sedaangkan class ini untuk custom tabel yang tidak
memakai aturan model maupun dbjoin

catatan ...!!!
model harus berdiri sendiri, hanya mempunyai properti
index harus ditentukan di property,
create tabel memakai dump.

contoh untuk tabel karyawan;


class Karyawan{
    public $alias='nama_alias';
    public $columns=array(
        'ky'=>'INT Primary Key',
        'nama'=>'VARCHAR(128)',
        'kolomjoin_1'=>'INT',
        'kolomjoin_2'=>'VARCHAR(128)',
    );

    public $join=array(
        'modeljoin_1'=>'kolomjoin_1',
        'modeljoin_2'=>'kolomjoin_2',
    );
    public $child=array(
        'tabelchild_1'=>'ky',
    );

}

*/

    function __construct(){
        $this->connected=$this->connect()!==false;
    }
    protected function primary($columns){
        foreach($columns as $key=>$value){
            if(strpos(strtolower($value),'primary')!==false) return $key;
        }
        return false;
    }

    protected function initTable($model){
        $class=ucfirst(strtolower($model));
        $table=new $class;
        $tableName=empty($table->table) ? strtolower($model):strtolower($table->table);
        $tableAlias=empty($table->alias) ? strtolower($model):strtolower($table->alias);
        $table->table=$tableName;
        $table->alias=$tableAlias;
        return $table;
    }

    protected function parseParams($columns,$params){
        global $db;
        $oprs=array(
            'like'=>'like',
            'equal'=>'equal',
            'notequal'=>'<>',
            'in'=>'in',
            'lt'=>'<',
            'lte'=>'<=',
            'gt'=>'>',
            'gte'=>'>='
        );
        $glue=empty($params['and'])?'OR':'AND';

          //$where='1=1';
        $qr=array();
        foreach($columns as $key=>$value){
            foreach($params as $paramkey=>$paramvalue){
                if($key==$paramkey){
                    foreach($oprs as $opr=>$opvalue){
                        if(isset($params[$key][$opr])){
                            $stat=$this->sanitize(trim(str_replace('*','%',$params[$key][$opr])));
                            if($opr=='in'){
                                $a=explode(',',$params[$key][$opr]);
                                $b=array();
                                foreach ($a as $i) $b[]=$this->sanitize($i);
                                $stat='('.implode(',',$b).')';
                            }
                            $qr[]=$value.' '.$opvalue.' '.$stat;
                        }
                    }
                }
            }
        }


        $result['where']=count($qr)>0?implode(' '.$glue.' ',$qr):' 1=1 ';
        $result['order']=empty($params['order']) ? '':' ORDER BY '.$params['order'];
        $result['group']=empty($params['group']) ? '':' GROUP BY '.$params['group'];

        $lim=empty($params['limit']) ? $db['offset'] : $params['limit'];
        $of=isset($params['offset']) ? $params['offset']:'0';
        $result['limit']=' LIMIT '.$of.','.$lim;
        return $result;
    }

    function querySelectJoin($model,$params){
        $table=$this->initTable($model);

        $columns=array();
        foreach($table->columns as $key=>$value)
            $columns[$key]=$table->table.'.'.$key.' AS '.$key;

        $statementjoin=array();
        if(!empty($table->join)){
            $tablecolumns=array_keys($table->columns);
            foreach($table->join as $key=>$value){
                $jointable=$this->initTable($key);

                foreach($jointable->columns as $joinkey=>$joinvalue){
                    $s=$jointable->alias.'_'.$joinkey;
                    $columns[$s]=$jointable->table.'.'.$joinkey.' AS '.$s;
                }
                foreach($tablecolumns as $val)
                   if($val==$value) $statementjoin[]=' LEFT JOIN '.$jointable->table.' '.$jointable->alias.
                                                   ' ON '.$table->table.'.'.$value.'='.$jointable->table.'.'.$value;
            }
        }

        $result=$this->parseParams($columns,$params);

        $result['qry']= 'SELECT '.implode(',',array_values($columns)).' FROM '.$table->table.' '.$table->alias.
                        implode(' ',$statementjoin).' WHERE '.$result['where'].$result['order'].$result['group'].$result['limit'];
        $result['countqry']='SELECT count(*) AS c '.' FROM '.$table->table.' '.$table->alias.
                        implode(' ',$statementjoin).' WHERE '.$where;
        return $result;
    }

    function querySelect($model,$params){
        $table=$this->initTable($model);
        /* fixed tablename and alias */
        $result= $this->parseParams($table->columns,$params);
        $result['qry']='SELECT '.implode(',',array_keys($table->columns)).' FROM '.$table->table.
                        " WHERE ".$result['where'].$result['order'].$result['group'].$result['limit'];
        $result['countqry']='SELECT count(*) FROM '.$table->table.
                        " WHERE ".$result['where'];
        return $result;
    }

    function queryInsert($model,$params){
        $table=$this->initTable($model);
        $columns=array();
        $values=array();
        foreach($table->columns as $key=>$value){
            foreach ($params as $paramkey => $paramvalue) {
                if($key==$paramkey){
                    $columns[]=$key;
                    $values[]=$this->sanitize($paramvalue);
                }
            }
        }
        return  'INSERT INTO '.$table->table.'('.implode(',',$columns).
                ')VALUES('.implode(',',$values).')';
    }

    function queryUpdate($model,$params){
        $table=$this->initTable($model);
        $columns=array();
        $primary=$this->primary($table->columns);
        if($primary==false) return false;
        $where='';
        foreach($table->columns as $key=>$value){
            foreach ($params as $paramkey => $paramvalue) {
                if($key==$paramkey){
                    if($key==$primary)
                        $where=$key.'='.$this->sanitize($paramvalue);
                    else $columns[]=$key.'='.$this->sanitize($paramvalue);
                }
            }
        }
        return  'UPDATE '.$table->table.' SET '.implode(',',$columns).' WHERE '.$where.' LIMIT 1';
    }

    function queryDelete($model,$params){
        $table=$this->initTable($model);
        $primary=$this->primary($table->columns);
        if($primary==false) return false;
        $where='';
        foreach($table->columns as $key=>$value){
            foreach ($params as $paramkey => $paramvalue) {
                if($key==$paramkey && $key==$primary){
                    $where=$key.'='.$this->sanitize($paramvalue);
                }
            }
        }
        if(empty($where)) return false;
        return  'DELETE FROM '.$table->table.' WHERE '.$where.' LIMIT 1';
    }
}
