alt.modules.auth = angular.module('alt-auth', [])
    .factory('$auth', ['$log', function($log){
        // mengambil data token yang disimpan di lokal
        store.set(alt.application + '_token', store.get(alt.application + '_token') || '0');
        store.set(alt.application + '_user', store.get(alt.application + '_user') || {});
        store.set(alt.application + '_location', store.get(alt.application + '_location') || {});

        // nilai default token 0 bila belum login

        return {
            token: '0',
            userdata:{},
            location:{},
            autologin:false,
            setToken: function(token){
                this.token = token;
                store.set(alt.application + '_token', this.token);
            },
            setUserData: function(data){
                this.userdata = data;
                store.set(alt.application + '_user', this.userdata);
            },
            setLocation: function(data){
                this.location = data;
                store.set(alt.application + '_location', this.location);
            },
            login: function(data){
                this.setToken(data);
            },
            logout: function(){
                this.token = '0';
                store.set(alt.application + '_token', this.token);
                this.setUserData({});
                this.setLocation({});
                window.location.href=alt.baseUrl+alt.defaultRoute;
            },
            islogin: function(){
                return this.token != '0';
            }
        };
    }])
    .config(['$provide', '$httpProvider', function($provide, $httpProvider){
        $provide.factory('authHttpInterceptor', ['$auth', '$log', '$q', function($auth, $log, $q){
            return {
                request: function(config){
                    if($auth.islogin()) config.headers['Authorization']='Bearer '+$auth.token;
                    return config;
                }
            };
        }]);

        $httpProvider.interceptors.reverse().push('authHttpInterceptor');
        $httpProvider.interceptors.reverse();
    }])
    .run(['$auth', '$log', function($auth, $log){
        var token = store.get(alt.application + '_token');
        if(token) {
            $auth.login(token);
            $auth.setUserData(store.get(alt.application + '_user'));
            $auth.setLocation(store.get(alt.application + '_location'));
        }
    }]);

alt.module('alt-auth', alt.modules.auth);
