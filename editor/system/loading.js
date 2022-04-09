var loading = function(){
    if(typeof alt.modules.loading !== 'undefined')
        return;

    alt.modules.loading = angular.module('alt-loading', [])
        .factory('$loading', ['$log', '$rootScope', '$q',  function($log, $rootScope, $q) {
            return {
                deferred: null,
                silent:false,
                show: function(){
                    if(!this.silent) $rootScope.$loading.isshow = true;
                },
                close: function(){
                    $rootScope.$loading.isshow = false;
                },
                error: function(){
                    $rootScope.$loading.isshow = false;
                }
            };
        }])
        .run(['$log', '$timeout', '$rootScope', '$loading', function($log, $timeout, $rootScope, $loading){
            $rootScope.$loading = {};
        }]);

    alt.module('alt-loading', alt.modules.loading);
};

if(typeof define !== 'undefined') {
    define([], function () {
        loading();
    });
}else{
    loading();
}
