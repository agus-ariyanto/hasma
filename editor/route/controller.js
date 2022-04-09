define(['system/api'], function(){ return ['$scope','$navlevel','$auth','Api',
    function($scope,$navlevel,$auth,Api){
    /* component */
    $navlevel.close();
    $scope.data={email:'',password:''}
   
    $scope.login=function(){
        Api.Post('login',$scope.data)
        .then(function(res){
            if(res.data.success){
                $auth.login(res.data.token);
                $auth.setUserData(res.data.userdata);
                window.location.reload();
            }
        });
    }
    $scope.init=function(){
        if($auth.islogin()) window.location=alt.baseUrl+'home';
    }
    $scope.init();
    /* end controller */
    }]
});