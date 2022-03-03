<?php
class Image extends Base{
    protected $updir='upload';

/*
    inisial absolute direktori upload dan nama file
    diambil dari tanggal-[jam][menit][detik][milidetik]
*/

    function __construct(){
        parent::__construct();
        $this->dir=ROOT_DIR.DS.$this->updir;
        $date=new DateTime();
        $this->filename=$date->format('ymd-Hisv');
/*
        custom subfolder
        under /upload
*/
        if(!empty($_POST['subdir'])) {
            $this->updir=$this->updir.'/'.$_POST['subdir'];
            $this->dir=ROOT_DIR.DS.$this->updir.DS.$_POST['subdir'];
        }
    }
/*
    params
    POST multipart ?u=image/[file-type default jpg]
    atau
    POST urlencode
    {
        image: file image, (urlencode image-base64 encode)
        subdir:string (opsional subdir, subdir harus sudah dibuat)
    }
*/


     function index(){
         // handle upload file
         if(isset($_FILES['image'])){
             $this->upload();
             return;
         }
        // handle post image text (base 64)
         if(isset($_POST['image'])){
             $this->post();
             return;
         }
     }
/*
     protected function saveImage($imagedata){
      $file=date('Ymd-His').'.jpg';
      $img=str_replace('data:image/jpeg;base64,','',$imagedata);
      $data=base64_decode($img);
      file_put_contents(ROOT_DIR.DS.'upload'.DS.$file,$data);
      return 'api/upload/'.$file;
    }
*/
     // handle post image-text
     function post(){
         // $this->data($_POST);
         // return;
         $ext=empty($this->query[0])?'jpg':strtolower($this->query[0]);
         // if(!empty($_POST['updir'])) $this->filename=$_POST['subdir'].DS.;
         if(isset($_POST['image'])){
             $img=str_replace('data:image/'.$ext.';base64,','',$_POST['image']);
             $data=base64_decode($img);
             file_put_contents($this->dir.DS.$this->filename.'.'.$ext,$data);
             $this->data( array(
                 'success'=>true,
                 'image'=>'api/'.$this->updir.'/'.$this->filename.'.'.$ext,
             ));
             return;
         }
         $this->data(array(
             'success'=>false,
             'error'=>'error uploaded empty image',
         ));
     }

     // handle upload image file
     function upload(){
          if(isset($_FILES['image'])){
              $img=$_FILES['image'];
              $ext=strtolower(pathinfo($img['name'],PATHINFO_EXTENSION));
              if(in_array($ext,array('png','jpeg','jpg'))){
                  if(move_uploaded_file($img['tmp_name'],$this->dir.DS.$this->filename.'.'.$ext)){
                      $this->data(array(
                          'success'=>true,
                          'image'=>'api/'.$this->updir.'/'.$this->filename.'.'.$ext,
                      ));
                      return;
                  }
              }
          }
          $this->data(array(
                  'success'=>false,
                  'error'=>'error uploaded empty image',
          ));
      }

      // decode uploaded image file
      // menjadi imguri
      function textdata(){
          if(empty($this->query[0])){
               $this->data(array(
                   'success'=>false,
                   'error'=>'no file image to convert',
               ));
               return;
           }
          $file=$this->dir.DS.str_replace('_','-',$this->query[0]);
          if(!file_exists($file)){
              $this->data(array(
                  'success'=>false,
                  'error'=>'file not found',
              ));
              return;
          }
          $imageData = base64_encode(file_get_contents($file));
          $data='data:'.mime_content_type($file).';base64,'.$imageData;
          $this->data(array(
               'success'=>true,
               'image'=>$data,
          ));
     }
     /*
     function datauri($image,$mime='') {
     	$imguri='data: '.
                (function_exists('mime_content_type') ? mime_content_type($image) : $mime).
                ';base64,'.
                base64_encode(file_get_contents($image));
        return $imguri;
     }



     <?php
     if(isset($_POST["submit"]))
     {
       if(is_array($_FILES))
       {
       $file = $_FILES['img']['tmp_name'];
       $image_prop = getimagesize($file);
       $image_type = $image_prop[2];
       if( $image_type == IMAGETYPE_JPEG )
         {
         $image_resource_id = imagecreatefromjpeg($file);
         $layer = resize($image_id,$image_prop[0],$image_prop[1]);
         imagejpeg($layer,$_FILES['img']['name'] . "_thump.jpg");
         }
       elseif( $image_type == IMAGETYPE_GIF )
         {
         $image_id = imagecreatefromgif($file);
         $layer = resize($image_id,$image_prop[0],$image_prop[1]);
         imagegif($layer,$_FILES['img']['name'] . "_thump.gif");
         }
       elseif( $image_type == IMAGETYPE_PNG )
         {
         $image_id = imagecreatefrompng($file);
         $layer = resize($image_id,$image_prop[0],$image_prop[1]);
         imagepng($layer,$_FILES['img']['name'] . "_thump.png");
         }
       }
     }
     function resize($image_id,$width,$height)
     {
     $new_width =$width * 0.5;
     $new_height =$height * 0.5;
     $layer=imagecreatetruecolor($new_width,$new_height);
     imagecopyresampled($layer,$image_id,0,0,0,0,$new_width,$new_height, $width,$height);
     return $layer;
     }
     ?>


     */

}
