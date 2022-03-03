<?php


/* Kelas dasar Model
 * turunan dari kelas DbSQL
 */



class Model extends DbSQL{
    /* properti nama tabel */
    protected $_table;

    /* properti tabel left join */
    protected $_jointbl=array();

    /* properti kolom tabel left join */
    protected $_joincols=array();

    /* properti where kondisi and */
    protected $_and = array();

    /* properti where kondisi or */
    protected $_or = array();

    /* properti orderby */
    protected $_orderby;

    /* properti groupby */
    protected $_groupby;

    /* property offset untuk paginasi */
    protected $_offset;

    /* properti limit untuk paginasi */
    protected $_limit;

    protected $autoinc=true;

    protected $_db;

    public $join='';
    public $child='';
    /* konstruktor model */
    function __construct(){
        global $db;
        global $prefix;
        if($this->is_connect()){
            if(!empty($this->table))
                $this->table=strtolower($prefix).str_replace(strtolower($prefix),'',strtolower($this->table));
            $this->_table = empty($this->table) ? strtolower(get_class($this)) : $this->table;
            $this->reset();
            $this->createTable();
            if(isset($this->firstrecord)){
                $this->firstdata=$this->firstrecord;
            }
            if(isset($this->firstdata)) $this->createfirstData();
        }
    }

    function tableName(){
        return $this->_table;
    }

    /* metod untuk membuat table */
    function createTable(){

        $array=array();
        /* kolom pertama id untuk index primary key */
        $s='id BIGINT PRIMARY KEY';
        if($this->autoinc) $s='id BIGINT AUTO_INCREMENT PRIMARY KEY';

        array_push($array,$s);
        /* nama masing-masing kolom dan tipe kolom */
        foreach($this->columns as $key => $val){
           array_push($array,$key.' '.$val);
        }
        $fields=implode(',',$array);
        $table=$this->_table;

/* heredoc query membuat tabel */
$qry=<<<QRY
CREATE TABLE IF NOT EXISTS $table(
$fields
)ENGINE=InnoDb
DEFAULT CHARSET=utf8
QRY;
        return $this->query($qry);
    }


    /* metod untuk memperbarui tabel hanya
     * dipakai saat develop
     */
    protected function resetTable(){
        $s='DROP TABLE '.$this->_table ;
        $this->query($s);
        return $this->createTable();
    }

    protected function createFirstData(){
        if(!isset($this->firstdata)) return;
        $this->select();
        $c=$this->countRec();
        if($c>0) return;
        foreach($this->firstdata as $val){
            foreach($val as $_key => $_val){
                $this->colVal($_key,$_val);
            }
            $this->save('',1);
        }
    }
    /* metod mengembalikan nilai
     * awal semua properti */
    function reset(){
        global $db;

        $this->_limit=$db['offset'];
        /* offset */
        $this->_offset=0;
        /* kosongkan semua properti */
        $this->_jointbl=array();
        $this->_joincols=array();
        $this->_and = array();
        $this->_or = array();
        /* default orderby adalah id descending */
        $s=$this->_table.'.id DESC';
        $this->_orderby =$s;
        $this->_groupby = '';
        /* properti kolom id */
        $this->id='';
        /* kosongkan properti nama kolom-kolomnya */
        foreach($this->columns as $key=>$val){
            $this->$key = '';
        }
    }

    function tableAlias(){
      return empty($this->alias)? $this->_table:$this->alias;
    }
    /*
     *tambahan metod untuk leftjoin 15-4-2017
     */

     function join($model,$joincols='',$mastercol='',$detailcol=''){
       $alias=$model->tableAlias();
       $columns=$model->colNames();
       if(!empty($joincols)){
           $jc=explode(',',$joincols);
           $columns=array();
           foreach($jc as $key=>$value){
               $columns[$value]='';
           }
       }
       /* default lefjoin ( mastercol dan detailcol kosong )
        * detail on master.detail_id=detail.id
        */
       if(empty($mastercol)) $mastercol=$alias.'_id';
       if(empty($detailcol)) $detailcol='id';


       $s='LEFT JOIN '.$model->tableName().' ON '.
       $this->_table.'.'.$mastercol.'='.
       $model->tableName().'.'.$detailcol;
       array_push($this->_jointbl,$s);
       foreach($columns as $key=>$val){
         /*
         * untuk menghindari
         * ambigu
         * nama kolom memakai alias
         * tabel.kolom AS tabel_kolom
         */

         $s=$model->tableName().'.'.$key.' AS '.$alias.'_'.$key;
         array_push($this->_joincols,$s);
       }

     }
     /* secondary join */
     function secJoin($master,$detail,$detailjoincols='', $mastercol='',$detailcol=''){
       $aliasdetail=$detail->tableAlias();
       $coldetail=$detail->colNames();

       if(!empty($detailjoincols)){
           $djc=explode(',',$detailjoincols);
           $coldetail=array();
           foreach($djc as $key=>$value){
               $coldetail[$value]='';
            }
        }

       /* default lefjoin ( mastercol dan detailcol kosong )
        * detail on master.detail_id=detail.id
        */
       if(empty($mastercol)) $mastercol=$aliasdetail.'_id';
       if(empty($detailcol)) $detailcol='id';


       $s='LEFT JOIN '.$detail->tableName().' ON '.
       $master->tableName().'.'.$mastercol.'='.
       $detail->tableName().'.'.$detailcol;
       array_push($this->_jointbl,$s);

       foreach($coldetail as $key=>$val){
         /*
         * untuk menghindari
         * ambigu
         * nama kolom memakai alias
         * tabel.kolom AS tabel_kolom
         */

         $s=$detail->tableName().'.'.$key.' AS '. $aliasdetail.'_'.$key;
         array_push($this->_joincols,$s);
       }
     }




  /* metod memasukkan nilai untuk WHERE .. AND */
   function andWhere($col,$val,$con='='){
      /* memeriksa bila kolom memakai nama tabel */
      $array=explode('.',$col);
      $s=(count($array)<2)?
        $this->_table.'.'.$col : $col;
      $s.=' '.$con.' '.$this->sanitize($val);
      array_push($this->_and,$s);
   }
  /* metod memasukkan nilai untuk WHERE .. AND */
   function andWhereFunction($col,$val,$con='='){
      /* memeriksa bila kolom memakai nama tabel */
      $array=explode('.',$col);
      $s=(count($array)<2)?
        $this->_table.'.'.$col : $col;
      $s.=' '.$con.' '.$val;
      array_push($this->_and,$s);
   }
   function andQry($custom_qry){
       array_push($this->_and,$custom_qry);
   }
   function orQry($custom_qry){
       array_push($this->_or,$custom_qry);
   }


   /* metod memasukkan nilai untuk WHERE .. OR */
   function orWhere($col,$val,$con='='){
      $array=explode('.',$col);
      $s=(count($array)<2)? $this->_table.'.'.$col : $col;
      $s.=' '.$con.' '.$this->sanitize($val);
      array_push($this->_or,$s);
   }
   function orWhereFunction($col,$val,$con='='){
      $array=explode('.',$col);
      $s=(count($array)<2)? $this->_table.'.'.$col : $col;
      $s.=' '.$con.' '.$val;
      array_push($this->_or,$s);
   }

   /* metod untuk memasukkan GROUP BY */
   function groupBy($col){
    $array=explode(',',$col);
    foreach($array as $key=>$val) $array[$key]=$this->_table.'.'.$val;
    $this->_groupby=implode(',',$array);
   }

   /* metod untuk memasukkan ORDER BY
    * default adalah descending */
   function orderBy($fields,$desc=1){
    $array=explode(',',$fields);
    foreach($array as $key=>$val) $array[$key]=$this->_table.'.'.$val;
    $s=implode(',',$array);

    /* descending atau ascending
     * lihat argumen desc */
    $s .= $desc==1?' DESC':'';
    $this->_orderby=$s;
   }

   /* metod untuk menampilkan random record */
   function byRandom(){
     $this->_orderby = ' RAND()';
   }

   /* metod memasukkan limit */
   function limit($val){
     $this->_limit=$val;
   }
   /* metod memasukkan offset */
   function curPage($val){
     $val=$val-1;

     /* memastikan bahwa nilai val paling
        kecil adalah 0 */
     if($val<0){
       $val=0;
     }
     $this->_offset=
      ($val) * $this->_limit;
   }
   /* menentukan offset sendiri */
   function from($value){
      $this->_offset=$value;
   }

   /* metod untuk query SELECT */
   function select($rec_id=null){
        /*
         * periksa argumen rec_id sebagai id,
         * bila kosong, masukkan
         * dari properti id
         */
        $id=$rec_id;
        if(empty($id)) $id=$this->id;

        /* kolom-index nya */
        $s=$this->_table.'.id  AS id';
        $array=array($s);

        /* kolom-kolomnya yang lain */
        foreach($this->columns as $key=>$val){
            /* kolom menghindari ambigu alias tabel.kolom AS kolom */
            $s=$this->_table.'.'.$key.' AS '.$key;
            array_push($array,$s);
        }

        /* kolom tabel join bila ada */
        if(!empty($this->_joincols)){
            foreach($this->_joincols as $val) array_push($array,$val);
        }

        /* gabung semua kolom-kolomnya */
        $fields=implode(',',$array);

        /* nama tabel */
        $array=array($this->_table);

        /* nama tabel join */
        foreach($this->_jointbl as $val)  array_push($array,$val);


        /* gabung semua tabelnya */
        $table=implode(' ',$array);

        /* select id */
        if(!empty($id)){
            $where=$this->_table.'.id = '.$id;
$qry = <<<QRY
SELECT $fields FROM $table
WHERE $where LIMIT 1
QRY;
            $this->reset();
            $res=$this->query($qry);
            return $res[0];
        }

        /* limit dan where melihat argumen id
        * bila id kosong tampilkan
        * berdasar limit dan offset
        */
        $limit=$this->_offset.','.$this->_limit;

        /* kondisi-kondisi where */
        $array=array();
        $where='';
        /*
         * bila properti nama kolom diberi nilai,
         * masukkan sebagai where
         */
        foreach($this->columns as $key=>$val){
            if(!empty($this->$key)){
                $s=$this->_table.'.'.$key.' = '.$this->$key;
                array_push($array,$s);
            }
        }

        /* bila _and punya nilai tambahkan dalam where */
        foreach($this->_and as $val) array_push($array,$val);

        /* gabung where kondisi AND */
        $and=implode(' AND ',$array);

        /* bila or diberi nilai tambahkan kedalam $where */
        $or=implode(' OR ',$this->_or);

        /* gabungkan or dan and */
        $array=array();
        if(!empty($and)) array_push($array,$and);

        if(!empty($or)) array_push($array,$or);

        if(!empty($array)) $where=implode(' OR ',$array);

        /* where masih kosong tambahkan 1=1 */
        if(empty($where)) $where=' 1=1 ';
        /* group by dan orderby */
        $groupby=empty($this->_groupby)?'': ' GROUP BY '.$this->_groupby ;
        $orderby=empty($this->_orderby)?'': ' ORDER BY '.$this->_orderby;

/* heredoc membentuk query select */
$qry=<<<QRY
SELECT $fields FROM $table
WHERE $where  $groupby  $orderby
LIMIT $limit
QRY;
        $this->reset();
        $_table=$this->_table;

/* menyimpan query untuk total record */
$this->_countqry=<<<QRY
SELECT count( $_table.id ) AS id FROM $table
WHERE $where
QRY;
        /* mengirim query */
        return $this->query($qry);
    }

    function savePost($post){
    /*
    tambahan 13-07-2021
    fix id on savePost()
    dulu return dobel sanitize
    menyebabkan error untuk update
    */
        $id='';
        if(!empty($post['id'])) $id=$post['id'];
        $this->add($post);
        return $this->save($id);
    }

    /* metod untuk memasukkan nilai post
     * kedalam kolom-kolom tabelnya */
    function add($post){
        $array=$this->colNames();
        foreach($array as $key=>$val){
            /*
             * masukkan nilai post yang sesuai
             * dengan properti kolom dan tidak kosong
             */
            if(isset($post[$key]) && $post[$key] != '')
                $this->$key=$this->sanitize($post[$key]);
        }
     }

    /* metod memasukkan nilai perkolom */
    function colVal($col,$val){
        /* masukkan nilai sesuai properti kolomnya */
        $this->$col=$this->sanitize($val);
    }

    /* metod untuk insert dan update  */
    function save($rec_id=null,$with_ignore=0){
        /*
         * periksa argumen rec_id sebagai id,
         * bila kosong, masukkan dari properti id
         */
        $id=$rec_id;
        if(empty($id)) $id=$this->id;

        /* nama tabelnya */
        $table=$this->_table;

        /* id masih kosong membentuk query insert */
        if(empty($id)){
            /* memasukkan nilai masing-masing kolom */
            $ar_key=array();
            $ar_val=array();
            foreach( $this->columns as $key => $val ){
                if($this->$key != ''){
                    array_push($ar_key,$key);
                    array_push($ar_val,$this->$key);
                }
            }
            $fields=implode(',',$ar_key);
            $values=implode(',',$ar_val);


$qry=<<<QRY
INSERT INTO $table( $fields )
VALUE( $values )
QRY;
            $this->reset();
            return $this->query($qry);
        }
        /* id masih kosong membentuk query insert */
        if(!empty($id)&&$with_ignore!=0){
          /* memasukkan nilai masing-masing kolom */
          $ar_key=array();
          $ar_val=array();
          array_push($ar_key,'id');
          array_push($ar_val,$id);
          foreach( $this->columns as $key => $val ){
              if($this->$key != ''){
                  array_push($ar_key,$key);
                  array_push($ar_val,$this->$key);
              }
          }

          $fields=implode(',',$ar_key);
          $values=implode(',',$ar_val);



$qry=<<<QRY
INSERT IGNORE INTO $table( $fields )
VALUE( $values )
QRY;
            $this->reset();
            return $this->query($qry);
        }




        /* id mempunyai nilai, membentuk query update */
        $array=array();
        foreach( $this->columns as $key => $val ){
            if($this->$key != '') array_push($array,$key.'='.$this->$key);
        }
        $fieldvals=implode(',',$array);
        /* membentuk query update */

$qry=<<<QRY
UPDATE $table SET $fieldvals
WHERE id=$id LIMIT 1
QRY;

        $this->reset();
        /* return affeted $row */
        $res=$this->query($qry);
        /*
         * update gagal
         */
        return $id;
    }

    /*
     * metod untuk menghapus
     * berdasar argumen rec_id atau id
     */
    function delete($rec_id=null){
        /* periksa argumen rec_id sebagai
        * id,  bila kosong, masukkan
        * dari properti id */
        $id=$rec_id;
        if(empty($id)) $id=$this->id;

        /* nama tabelnya */
        $table=$this->_table;

        /* memastikan bahwa id tidak kosong
         * untuk melaksanakan query delete */
        if(!empty($id)){

  /* menjalankan query hapus */
$qry=<<<QRY
 DELETE FROM $table
 WHERE id = '$id' LIMIT 1
QRY;
            return $this->query($qry);
        }

        $array=array();
        /* kolom-kolomnya yang lain */
        foreach($this->columns as $key=>$val){
            if(!empty($this->$key)){
                $s=$key.' = '.$this->sanitize($this->$key);
                array_push($array,$s);
            }
        }
        /* andWhere */
        foreach($this->_and as $val) array_push($array,$val);

        $s='';
        if(!empty($array)) $s=implode(' AND ',$array);

        /* kondisi masih kosong tidak melakukan apa-apa */
        if(empty($s)) return 0;

$qry=<<<QRY
  DELETE FROM $table
  WHERE $s
QRY;
        return $this->query($qry);
    }

    /* menghitung total record */
    function countRec($where=null){
        /*
         *periksa argumen where
         * argumen where kosong
         * ambil  dari select
         */
        if(empty($where)){
            $array=$this->query($this->_countqry);
            return $array[0]['id'];
        }

        /*
         * bila where diberi nilai
         * buat query count
         */
        $table=$this->_table;

$qry=<<<QRY
SELECT count(id) as id FROM $table
WHERE $where
QRY;
        $array=$this->query($qry);
        return $array[0]['id'];
    }

    /*
     * metod untuk mengambil nama-nama kolom turunannya,
     * berguna untuk leftjoin atau menampilkan form kosong
     */
    function colNames(){
        $array=array();
        /* ditambah kolom id */
        $array['id']='';
        /* ambilkan dari properti $columns */
        foreach($this->columns as $key=>$val) $array[$key]='';

        foreach($this->_joincols as $key){
            list($_val,$_key)=explode('AS',str_replace(' ','',$key));
            $array[$_key]='';
        }
        return $array;
    }



    function selectFunction($sqlfunction,$fromtable=false){
        $s='SELECT '. $sqlfunction .' AS f ';
        if($fromtable) $s.='FROM '.$this->_table.' LIMIT 1' ;
        $res=$this->query($s);
        return $res[0]['f'];
    }

    function deleteWhere($key,$value,$expr=' = '){
        $s='DELETE FROM '.$this->tableName().' WHERE '.$key.$expr.$this->sanitize($value);
        return $this->query($s);
    }

    function updateWhere($key,$value,$new_value){
        $s='UPDATE '.$this->tableName().' SET '.$key.' = '.$this->sanitize($new_value).
        ' WHERE '.$key.' = '.$this->sanitize($value) ;
        return  $this->query($s);
    }

    function clearRec($id,$cols,$val_null=true){
        $columns=array();
        foreach($cols as $value) $columns[] = $val_null ? $value."=null" : $value."='0'" ;
        $s='UPDATE '.$this->tableName().' SET '.implode(',',$columns).
        ' WHERE id = '.$id;
        return  $this->query($s);
    }

/*    function joinTables(){
        $result=array();
        $prefix=ucfirst(str_replace($this->tableAlias(),'',$this->tableNames()));
        foreach($this->columns as $key=>$value){
            if(strpos('_id',$key)!==false)
                $result[]=$prefix.ucfirst(str_replace('_id','',$key));
        }
        return $result;
    }

    function childTables(){
        $result=array();
        $prefix=ucfirst(str_replace($this->tableAlias(),'',$this->tableNames()));
        foreach($this->child as $value)
            $result[]=$prefix.ucfirst($value);
        return $result;
    }
*/

    function createWhere(){
        foreach($this->columns as $key=>$val){
            if(!empty($this->$key)){
                $s=$this->_table.'.'.$key.' = '.$this->$key;
                array_push($array,$s);
            }
        }

        /* bila _and punya nilai tambahkan dalam where */
        foreach($this->_and as $val) array_push($array,$val);

        /* gabung where kondisi AND */
        $and=implode(' AND ',$array);

        /* bila or diberi nilai tambahkan kedalam $where */
        $or=implode(' OR ',$this->_or);

        /* gabungkan or dan and */
        $array=array();
        if(!empty($and)) array_push($array,$and);
        if(!empty($or)) array_push($array,$or);

        $where= implode(' OR ',$array);
        $this->reset();
        return $where;
    }


    function maxid(){
        $qry='SELECT max(id)  as id FROM '.$this->tableName();
        return $this->query($qry)[0]['id'];
    }




    /* destruktor memutuskan
    * hubungan dengan server MySQL */
    function __destruct(){
      $this->disconnect();
    }


 /* akhir kelas Model */
}
