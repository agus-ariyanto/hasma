define(['system/api'], function(){ return ['$scope','$navlevel','$auth','Api',
    function($scope,$navlevel,$auth,Api){
    /* component */
    $navlevel.close();
    $scope.tab=0;
    $scope.totab=function(val){
        $scope.allclose();
        if(val==0) $scope.signindiag.open();
        if(val==1) $scope.signupdiag.open();
        if(val==2) $scope.resetdiag.open();
    }
    $scope.allclose=function(){
        $scope.signindiag.active=false;
        $scope.signupdiag.active=false;
        $scope.resetdiag.active=false;
    }
    $scope.signindiag={
        active:true,
        close:function(){
            if($scope.signindiag.saved) window.location.reload();
        }
    }
    $scope.signupdiag={
        active:false,
        close:function(){
            $scope.totab(0);
        }
    }
    $scope.resetdiag={
        active:false,
        close:function(){
            $scope.totab(0);
        }
    }
    
    $scope.to=function(){
        if($auth.userdata.grup_id==1) window.location.href=alt.baseUrl+'a';
        if($auth.userdata.grup_id==2) window.location.href=alt.baseUrl+'o';
        if($auth.userdata.grup_id==3) window.location.href=alt.baseUrl+'m';
        if($auth.userdata.grup_id==4) window.location.href=alt.baseUrl+'c';
    }   
    $scope.init=function(){
        if($auth.islogin()) $scope.to();
    }
    $scope.init();
    /* end controller */
    }]
});