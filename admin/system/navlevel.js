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
                open: function(){
                    $rootScope.$navlevel.active=true;
                },
                close: function(){
                    $rootScope.$navlevel.active=false;
                    $rootScope.$navlevel.leftactive=false;
                },
                isadmin:function(idx){
                    if($auth.userdata.grup_id>1) return $auth.logout(true);
                    $rootScope.$navlevel.items=[
                        {icon:'home',title:'Home',url:'a'},
                    ];
                    idx=idx||0;
                    $rootScope.$navlevel.index=$rootScope.$navlevel.items[idx];
                    this.open();
                },
                isowner:function(idx){
                    if($auth.userdata.grup_id>2){
                        $message.info('Anda harus login sebagai pemilik');
                        return $auth.logout(true);
                    }
                    $rootScope.$navlevel.items=[
                        {icon:'home',title:'Home',url:'o'},
                        {icon:'people-arrows',title:'Mekanik',url:'o/mec'},
                        {icon:'cogs',title:'Sparepart',url:'o/spt'},
                        {icon:'user-tie',title:'Customer',url:'o/cust'},
                        {icon:'warehouse',title:'Bengkel',url:'o/wks'},
                        {icon:'tasks',title:'Pool',url:'o/pool'},
                        {icon:'truck',title:'Kendaraan',url:'o/vhc'}
                    ];
                    idx=idx||0;
                    $rootScope.$navlevel.index=$rootScope.$navlevel.items[idx];
                    this.open();
                },
                ismechanic:function(idx){
                    if($auth.userdata.grup_id>3){
                        $message.info('Anda harus login sebagai mekanik');
                        return $auth.logout(true);
                    }
                    $rootScope.$navlevel.items=[
                        {icon:'home',title:'Home',url:'m'},
                    ];
                    idx=idx||0;
                    $rootScope.$navlevel.index=$rootScope.$navlevel.items[idx];
                    this.open();
                },
                iscustomer:function(idx){
                    if($auth.userdata.grup_id>3){
                        $message.info('Anda harus login sebagai partner');
                        return $auth.logout(true);
                    }
                    $rootScope.$navlevel.items=[
                        {icon:'home',title:'Home',url:'c'},
                    ];
                    idx=idx||0;
                    $rootScope.$navlevel.index=$rootScope.$navlevel.items[idx];
                    this.open();
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
