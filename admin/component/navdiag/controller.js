define(['system/api'], function(){ return ['$scope','$message','Api',function($scope,$message,Api){
    $scope.active=false;
    $scope.saved=false;
    $scope.data={}
    $scope.open=function(data){
        $scope.data={
            icon:'fa-plus',
            title:''
        };
        if(data) $scope.data=data;
        $scope.saved=false;
        $scope.active=true;
    }
    $scope.save=function(){
        Api.Post('navbar/save',$scope.data)
        .then(function(res){
            if(res.data.id){
                $scope.data=res.data;
                $scope.saved=true;
                $scope.close();
                return;
            }
            $message.failed('Gagal mengirimkan data, Periksa Jaringan');
        });
    }
    $scope.close=function(){
        $scope.active=false;
    }
    
    }]
});
