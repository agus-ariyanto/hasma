var message = function(){
    if(typeof alt.modules.message !== 'undefined')
        return;

    alt.modules.message = angular.module('alt-message', [])
        .factory('$message', ['$rootScope',  function($rootScope) {
            return {
                success:function(txt){
                    txt=txt||'Berhasil mengirim data';
                    this.open({
                        icon:'fa-check',
                        text:txt,
                        title:'Sukses',
                        css:'text-success'
                    });
                },
                failed:function(txt){
                    txt=txt||'Gagal mengirim data';
                    this.open({
                        icon:'fa-ban',
                        text:txt,
                        title:'Gagal',
                        css:'text-danger'
                    });
                },
                error:function(txt){
                    txt=txt||'Check network';
                    this.open({
                        icon:'fa-info',
                        text:txt,
                        title:'Error',
                        css:'text-danger'
                    });
                },
                info:function(txt){
                    txt=txt||'Tidak ada info';
                    this.open({
                        icon:'fa-info',
                        text:txt,
                        title:'Info',
                        css:''
                    });
                },
                open: function(data){
                    $rootScope.$message.data=data;
                    $rootScope.$message.active=true;
                },
                close: function(){
                    $rootScope.$message.active = false;
                }
            };
        }])
        .run(['$rootScope', '$message', function($rootScope, $message){
            $rootScope.$message = {};
        }]);

    alt.module('alt-message', alt.modules.message);
};

if(typeof define !== 'undefined') {
    define([], function () {
        message();
    });
}else{
    message();
}
