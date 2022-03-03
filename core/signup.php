<?php 
class Signup extends Login{
    function checkauth(){
        $this->auth->andWhere('email',$this->params->key('email'));
        $this->auth->limit(1);
        $res=$this->auth->select();
        if(empty(count($res))){
            $this->data(array(
                'success'=>false,
                'message'=>'email belum terdaftar',
            ));
            return false;
        } 
        $this->data(array(
            'success'=>true,
            'id'=>$res[0]['id'],
        ));
        return $res[0]['id'];
    }
    function checkverif(){
        $this->verif->andWhere('email',$this->params->key('email'));
        $this->verif->limit(1);
        $res=$this->verif->select();    
        if(empty(count($res))) {
            $this->data(array(
                'success'=>false,
                'message'=>'Alamat email belum terdaftar',
            ));
            return false;
        }
        $this->data(array(
            'success'=>true,
            'id'=>$res[0]['id'],
            'pin'=>$res[0]['pin'],
        ));
        return $res[0]['id'];
    }

    function save(){
        /* update for password reset */
        if(!empty($this->params->key('id'))){
            $result=$this->db->update('auth',$this->params,$this->params->key('id'));
            $this->data(array(
                'success'=>true,
                'id'=>$result['id'],
            ));
            return;
        }

        /* new check account first */
        // $params=new Params;
        // $params->clear();
        // $params->set('email',$this->params->key('email'));
        // $params->set('limit',1);
        // $res=$this->db->select('auth',$params);
        $email=$this->params->key('email');
        $this->params->set('email',array(
            'equal'=>$email,
        ));
        $id=$this->checkauth();
        /* account found, update */
        if($id!==false){
            $result=$this->db->update('auth',$this->params,$id);
            $this->data(array(
                'success'=>true,
                'id'=>$id,
            ));
            return;
        }
        /* account not found insert */
        
        /* add data from verif */
        $id=$this->checkverif();
        
        $res=$this->verif->select($id);
        foreach($res as $key=>$value) $this->params->set($key,$value);
        $this->params->del('id');
        $res=$this->db->insert('auth',$this->params);
        $this->data(array(
            'success'=>true,
            'id'=>$res['id'],
        ));
    }
    
    
    /* send/resend then insert/update verif */
    function sendpin(){
        if(empty($this->params->key('email'))){
            $this->data(array(
                'success'=>false,
                'error'=>'Alamat email kosong',
            ));
            return ;
        }
        $id=$this->checkverif();
        if($id===false){
                if(empty($this->params->key('profile_id'))){
                $this->data(array(
                    'success'=>false,
                    'error'=>'Alamat email belum terdaftar, kontak admin',
                ));
                return ;
            }
        }
        $subject='MMCS - Pin Untuk Verifikasi Alamat Email';
        $email=$this->params->key('email');
        $pin=$this->verif->pin();
        $body='<!DOCTYPE html>'.
        '<html lang="en">'.
            '<body style="padding:10px;">'.
                '<h3>Verifikasi Alamat Email</h3>'.
                '<p style="text-align:justify;text-justify:inter-word">Dibawah ini adalah verifikasi untuk memeriksa alamat email anda, '.
                    'salin dan isikan ke input pin saat melakukan pendaftaran akun atau pergantian profil alamat email</p>'.            
                    '<p>PIN</p>'.
                    '<p>'.
                        '<span style="padding:10px 20px;background-color:#cacaca">'.$pin.'</span>'.
                    '</p>'.
                    '<p style="color:#777;margin-top:30px">'.
                        '<small>Email ini dikirimkan secara otomatis dan tidak perlu anda balas</small><br>'.
                        'Admin <strong>MMCS</strong>'.
                    '</p>'.
            '</body>'.
        '</html>';
        if($this->sendmail($email,$subject,$body)){
            // $id=$this->checkverif();

            /* no verif, create one, */
            if($id===false){
                $this->verif->colVal('email',$email);
                $this->verif->colVal('grup_id',$this->params->key('grup_id'));
                $this->verif->colVal('profile_id',$this->params->key('profile_id'));
                $this->verif->colVal('pin',$pin);
                $id=$this->verif->save();
                $this->data(array(
                    'success'=>true,
                    'id'=>$id,
                    'pin'=>$pin,
                ));
                return ;
            }

            /* account exists, user signup : resend request pin */ 
           
            $this->verif->colVal('pin',$pin);
            $this->verif->save($id);
            $this->data(array(
                'success'=>true,
                'id'=>$id,
            ));
            return;
        }
        /* fail sendmail */
        // $this->verif->failed='failed + 1';
        // $this->verif->save($id);
        $this->data(array(
            'success'=>false,
            'error'=>'Alamat email tidak valid',
        ));
    }


    // sendpin for reset password
    /* update verif then sendmail */
    function sendresetpin(){
        if(empty($this->params->key('email'))){
            $this->data(array(
                'success'=>false,
                'error'=>'Alamat email kosong',
            ));
            return ;
        }

        $id=$this->checkverif();
        

        // email notfound
        if($id===false){
            $this->data(array(
                'success'=>false,
                'error'=>'Alamat email belum terdaftar',
            ));
            return ;
        }
        // generate new pin
        $pin=$this->pin();
        $this->verif->colVal('pin',$pin);
        $this->verif->save($id);

        // sendmail reset pin
        $subject='MMCS - Pin Untuk Verifikasi Reset Password';
        $email=$this->params->key('email');
        $body='<!DOCTYPE html>'.
            '<html lang="en">'.
                '<body style="padding:10px;">'.
                    '<h3>Verifikasi Reset Password</h3>'.
                    '<p style="text-align:justify;text-justify:inter-word">Ini adalah verifikasi untuk memastikan bahwa anda adalah'.
                        'pemilik akun yang akan mereset password <br> '.
                        'salin dan isikan ke input pin saat melakukan reset password</p>'.            
                        '<p>PIN</p>'.
                        '<p>'.
                            '<span style="padding:10px 20px;background-color:#cacaca">'.$pin.'</span>'.
                        '</p>'.
                        '<p style="color:#777;margin-top:30px">'.
                            '<small>Email ini dikirimkan secara otomatis dan tidak perlu anda balas</small><br>'.
                            'Admin <strong>MMCS</strong>'.
                        '</p>'.
                '</body>'.
            '</html>';
        //  sendmail success   
         if($this->sendmail($email,$subject,$body)){
             $this->data(array(
                'success'=>true,
                'id'=>$id,
             ));
             return ;
        }
        /* sendmail fail */
        $this->data(array(
            'success'=>false,
            'error'=>'Alamat email tidak valid',
        ));  
        
    }
    
    /* end controller */
}