define([], function(){
    alt.factory('System', ['$log', '$api',  '$rootScope','$loading',  function($log, $api,  $rootScope,$loading ){
        return function(url){
            return $api(url, '', {
                connect: function(params){

                    $loading.show();
                },
                success: function(response){
                    $loading.close();
                },
                error: function(error, params, deferred, iscancelled){
                    if(iscancelled) return true;
                }
            });
        };
    }]);
});
