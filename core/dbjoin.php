<?php
class DbJoin extends DbSQL{
    function __construct(){
        $this->connected=$this->connect()!==false;
    }

    function model($model){
        global $prefix;
        return ucfirst(strtolower($prefix)).ucfirst(strtolower($model));
    }

    function init($model){
        $class=$this->model($model);
        $table=new $class;
        $tableName=$table->tableName();
        $tableAlias=$table->tableAlias();
        $columns=array();
        $statementjoin=array();
        foreach($table->colNames() as $key=>$value) $columns[$key]=$tableAlias.'.'.$key;
        $join=array();
        if(!empty($table->join)){
            $var=explode(',',$table->join);
            foreach ($var as $value) {
                $class=$this->model($value);
                $jointable=new $class;
                $name=$jointable->tableName();
                $alias=$jointable->tableAlias();
                $cols=$jointable->colNames();
                unset($cols['id']);
                foreach($cols as $_key => $_val) {
                    $s=$alias.'_'.$_key;
                    $columns[$s]=$alias.'.'.$_key;
                }
                $join[]=$jointable;
            }
        }
         $init = new stdClass;
         $init->table=$table;
         $init->columns=$columns;
         $init->join=$join;
        return $init;

    }
    function querySelect($model,$params,$optand='',$id=''){
        global $db;
        /*
        gt : greater than
        gte : greater than or equal
        lt : less than
        lte : less than or equal
        */
        $oprs=array(
            'like'=>'like',
            'equal'=>'=',
            'notequal'=>'<>',
            'in'=>'in',
            'notin'=>'not in',
            'lt'=>'<',
            'lte'=>'<=',
            'gt'=>'>',
            'gte'=>'>=',
            'is'=>'is',
            'isnot'=>'is not',
            'bw'=>'between',
        );

        //if(!empty($params['id'])&&is_numeric($params['id'])) $id=$params['id'];
        $init=$this->init($model);
        $statementjoin=array();
        foreach($init->join as $value)
        $statementjoin[]=' LEFT JOIN '.$value->tableName().' '.$value->tableAlias().' ON '.$init->table->tableAlias().'.'.$value->tableAlias().'_id = '.$value->tableAlias().'.id';

        foreach ($init->columns as $key => $value) $cols[]=$value.' AS '.$key;

        if(!empty($id)) return  'SELECT '.implode(',',$cols).' FROM '.$init->table->tableName().
                ' '.$init->table->tableAlias(). implode(' ',$statementjoin).' WHERE '.
                $init->table->tableAlias().
                '.id = '.$this->sanitize($id).'  ';

         //$where='1=1';
         $qr=array();
         foreach($init->columns as $key=>$value){
             foreach($params as $paramkey=>$paramvalue){
                 if($key==$paramkey){
                     foreach($oprs as $opr=>$opvalue){
                         if(isset($params[$key][$opr])){
                             /*null mendapat perlakuan khusus*/
                             $stat=trim($params[$key][$opr]);
                             if(strtolower($stat)!='null'){
                                 $a=explode('+',$stat);
                                 $b=array();
                                 foreach ($a as $i) $b[]=$this->sanitize( str_replace('*','%',$i) );
                                 $stat=implode(' AND ',$b);
                             }
                             /*in mendapat perlakuan khusus*/
                             if($opr=='in'||$opr=='notin'){
                                 // stat direset lagi ke nilai awal
                                 $stat=trim($params[$key][$opr]);
                                 $a=explode(',',$params[$key][$opr]);
                                 $b=array();
                                 foreach ($a as $i) $b[]=$this->sanitize($i);
                                 $stat='('.implode(',',$b).')';
                             }
                             // range untuk kolom sama belum nemu

                             $qr[]=$value.' '.$opvalue.' '.$stat;
                         }
                     }
                 }
             }


         }

         $glue=empty($params['and']) ? 'OR':'AND';
         $where = count($qr)>0 ? implode(' '.$glue.' ',$qr) : ' 1=1 ';
         if(!empty($optand)) $where ='(' . $where. ') AND '.$optand;

         $lim=isset($params['limit']) ?  $params['limit'] : $db['offset'];
         $of=isset($params['offset']) ? $params['offset']:'0';
         $of=isset($params['page']) ? ($params['page'] - 1) * $lim : $of;
         $order=empty($params['order']) ? '':' ORDER BY '.$params['order'];
         $group=empty($params['group']) ? '':' GROUP BY '.$params['group'];
         $limit = ' LIMIT '.$of.','.$lim;
         $result['limit']=$lim;
         $result['offset']=$of;
         $result['page']=isset($params['page']) ? $params['page'] : 0;

        $result['qry'] = 'SELECT '.implode(',',$cols).' FROM '.$init->table->tableName().
                         ' '.$init->table->tableAlias().implode(' ',$statementjoin).
                         ' WHERE '.$where.$order.$group.$limit;
        $result['countqry']= 'SELECT count(*) AS c '.' FROM '.
                              $init->table->tableName().' '.$init->table->tableAlias().
                              implode(' ',$statementjoin).' WHERE '.$where;
        return $result;
    }

    function queryInsert($model,$params){
        $class=$this->model($model);
        $class=new $class;

        $colkeys=array();
        $colvals=array();
        foreach($class->colNames() as $key => $value)
        foreach ($params as $parkey => $parvalue) {
            if($key==$parkey) {
                $colkeys[]=$parkey;
                $colvals[]=$this->sanitize($parvalue);
            }
        }
        return  'INSERT IGNORE INTO '.$class->tableName().
        ' ('.implode(',',$colkeys).
        ')VALUES('.implode(',',$colvals).')';
    }

    function queryUpdate($model,$params,$id){
         $class=$this->model($model);
         $class=new $class;

         $cols=array();
         foreach($class->colNames() as $key => $value)
             foreach ($params as $parkey => $parvalue) {
                 if($key==$parkey)
                     $cols[]= $parkey.' = ' . $this->sanitize($parvalue);
             }
         return  'UPDATE '.$class->tableName().
                 ' SET '.implode(',',$cols).
                 ' WHERE id = '.$this->sanitize($id);
    }



    function select($model,$params,$optand=''){
        $result=$this->querySelect($model,$params->all(),$optand);
        $result['count']=$this->query($result['countqry'])[0]['c'];
        if($params->key('count')!=false) return $result;
        $result['data']=$this->query($result['qry']);
        if(DEV_MODE!==1) {
            unset($result['qry']);
            unset($result['countqry']);
        }
        return $result;
    }

    function id($model,$id=''){
        $qry=$this->querySelect($model,array(),'',$id);
        $result=$this->query($qry);
        if(count($result)>0){
            $record=$result[0];
            $tbname=$this->model($model);
            $table=new $tbname;
            if(empty($table->child)) return $record;
            $array=explode(',',$table->child);
            foreach($array as $value){
                $tbchild=$this->model($value);
                if(class_exists($tbchild)){
                    $child=new $tbchild;
                    $child->andWhere($model.'_id',$id);
                    $record[$value]=$child->select();
                }
            }
            return $record;
        }
        return 0;
    }

    function insert($model,$params){
        $qry=$this->queryInsert($model,$params->all());
        $id=$this->query($qry);
        if(empty($id)) return 0;
        $table=$this->model($model);
        $model=new $table;
        return $model->select($id);
    }
    function update($model,$params,$id){
        $params->del('id');
        $qry=$this->queryUpdate($model,$params->all(),$id);
        // return $qry;
        $res=$this->query($qry);
        // return $res;
        // if(empty($res)) return 0;
        $table=$this->model($model);
        $model=new $table;
        return $model->select($id);
    }

    function maxid($model){
        $s=$this->model($model);
        $model=new $s;
        $qry='SELECT max(id+1) as id FROM '.$model->tableName();
        return $this->query($qry)[0]['id'];
    }





}
