define(['ui/system/api','ui/system/helper'], function(){ return ['$scope','Api','Helper', function($scope,Api,Helper){

        $scope.data={
            this_month:'',
            this_year:'',
            month:'',
            month_name:'',
            year:'',
            current:true
        }

        $scope.init=function(val){
            Api.Get('today',{format:'Y-m-d'})
            .then(function(res){
                var s=res.data.split('-');
                var m=parseInt(s[1]),y=parseInt(s[0]);
                $scope.data={
                    this_month:m,
                    this_year:y,
                    month:m,
                    year:y,
                    current:true
                }
                $scope.to(0);
            });
        }
        $scope.reset=function(){
            $scope.data.year=$scope.data.this_year;
            $scope.data.month=$scope.data.this_month;
            $scope.to(0);
        }

        $scope.parseData=function(){
            $scope.data.month_name=Helper.getBulanName($scope.data.month);
            var a=$scope.data.month<10?'0'+$scope.data.month:''+$scope.data.month;
            $scope.data.query={like:$scope.data.year+'-'+a+'*'};
        }

        $scope.to=function(val){
            $scope.data.month=$scope.data.month+val;
            if($scope.data.month<1) {
                $scope.data.month=12;
                $scope.data.year=$scope.data.year-1;
            }
            if($scope.data.month>12) {
                $scope.data.month=1;
                $scope.data.year=$scope.data.year+1;
            }
            $scope.data.current=$scope.data.month==$scope.data.this_month
                                &&$scope.data.year==$scope.data.this_year;
            $scope.parseData();
            $scope.save();
        }
/*
        overwrite parent
*/
        $scope.save=function(){
            return;
        }
        
        $scope.search_text='';
        $scope.searching=false;
        $scope.init();

/*end controller*/
        }];
});
