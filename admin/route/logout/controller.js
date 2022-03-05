define([], function(){ return ['$scope','$auth',
    function($scope,$auth){
    /* component */
    $auth.logout(true);
    window.location.reload();
    /* end controller */
    }]
});