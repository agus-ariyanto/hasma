/*
untuk tampilannya lihat
ui/component/navlevel/view.html
loader komponen diatas ng-view,
file index.html
*/

var navlevel = function(){
    if(typeof alt.modules.navlevel !== 'undefined')
        return;
    alt.modules.navlevel = angular.module('alt-navlevel', [])
        .factory('$navlevel', ['$rootScope','$auth','$message',  function($rootScope,$auth,$message) {
            return {
                close: function(){
                    $rootScope.$navlevel.active=false;
                    $rootScope.$navlevel.leftactive=false;
                },
                open:function(idx){
                    if($auth.userdata.grup_id>2){
                        $message.info('Anda harus login sebagai pemilik');
                        return $auth.logout(true);
                    }
                    $rootScope.$navlevel.items=[
                        {icon:'home',title:'Home',url:'home'},
                        {icon:'server',title:'Navbar',url:'navbar'},
                        {icon:'th-large',title:'Content',url:'content'},
                        {icon:'commenting',title:'Comment',url:'comment'}
                    ];
                    idx=idx||0;
                    $rootScope.$navlevel.index=$rootScope.$navlevel.items[idx];
                    $rootScope.$navlevel.active=true;
                }
            };
        }])
        .run(['$rootScope', '$navlevel', function($rootScope, $navlevel){
            $rootScope.$navlevel={};
        }]);

    alt.module('alt-navlevel', alt.modules.navlevel);
};

if(typeof define !== 'undefined') {
    define([], function () {
        navlevel();
    });
}else{
    navlevel();
}
