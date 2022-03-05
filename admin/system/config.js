alt.application = 'MMCS';
alt.title = 'MMCS :: MAINTENANCE MANAGEMENT AND CONTROLLING SYSTEM';
alt.description = 'Application for manage and controll automobile workshop';
alt.version = '0.1.1';
alt.environment = 'development';

alt.serverUrl = '../';

var d=Date.today().toString('yyyy.MM.dd');
alt.urlArgs = '_v=' + alt.version+'&t='+d;
alt.routeFolder = '../admin/route';
alt.componentFolder = '../admin/component';
alt.defaultRoute = '';
alt.secure = {};

/* module disini */
alt.module('ngSanitize');
alt.module('datePicker');
// alt.module('720kb.tooltips');
// alt.module('ngImgCrop');
// set window title
document.title = alt.title;

alt.run(['$rootScope','$auth',
 function($rootScope,$auth){
      $rootScope.$auth = $auth;
      $rootScope.$on('$routeChangeStart', function(event, currRoute, prevRoute){
       if(currRoute.params.altaction!=alt.defaultRoute){
           if(!$auth.islogin()) window.location.href = alt.baseUrl + alt.defaultRoute;
       }

     });
  }]);