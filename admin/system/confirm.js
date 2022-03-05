var confirm = function(){
    if(typeof alt.modules.confirm !== 'undefined')
        return;

    alt.modules.confirm = angular.module('alt-confirm', [])
        .factory('$confirm', ['$rootScope',  function($rootScope) {
            return {
                doSave:function(){
                    return true;
                },
                open: function(t,x){
                    $rootScope.$confirm.data={
                        text:x,
                        title:t
                    };
                    $rootScope.$confirm.active=true;
                },
                close: function(){
                    $rootScope.$confirm.active = false;
                },
                save: function(){
                    if(this.doSave()) this.close();
                }
            };
        }])
        .run(['$rootScope', '$confirm', function($rootScope, $confirm){
            $rootScope.$confirm = {};
        }]);

    alt.module('alt-confirm', alt.modules.confirm);
};

if(typeof define !== 'undefined') {
    define([], function () {
        confirm();
    });
}else{
    confirm();
}
