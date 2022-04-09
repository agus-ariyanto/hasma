define([], function(){ 
    return ['$scope',function($scope){
        $scope.active=false;
        $scope.saved=false;
        $scope.data=
        $scope.open=function(data){
            $scope.data=data||{title:'Konfirmasi',icon:'comment',content:'Konfirmasi'};
            $scope.saved=false;
        }
        $scope.close=function(){
            $scope.active=false;
        }
        $scope.save=function(){
            $scope.saved=true;
            $scope.close();
        }
    }]
});
