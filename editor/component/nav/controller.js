define([], function(){ return ['$scope','$auth', function($scope,$auth){
    $scope.active=true;
    $scope.leftactive=false;
    $scope.leftenable=true;
    $scope.grup_id=$auth.userdata.grup_id;
    $scope.logout=function(){
        $auth.logout();
    }
    $scope.items=[];
    $scope.to=function(val){
        $scope.leftactive=false;
        window.location.href=alt.baseUrl+val;
    }
/*end controller*/
        }];
});
