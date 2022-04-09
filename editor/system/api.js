define(['system/index'], function(){
  alt.factory('Api', ['System',function(System){
    var a = System('admin');
    // untuk request get
    a.Get=function(url,data){
        var d=data||{};
        var p=$.param(d);
        url='/api.php?u='+url;
        return this.connect(url+'&'+p,{},{method:'GET'});
    }
    // untuk request post
    a.Post=function(url,data){
        url='/api.php?u='+url;
        return this.connect(url,data);
    }
    a.Put=function(url,id,data){
        url='/api.php?u='+url+'/'+id;
        return this.connect(url,data,{method:'PUT'});
    }
    a.Delete=function(url,id){
        url='/api.php?u='+url+'/'+id;
        return this.connect(url,{},{method:'DELETE'});
    }
    a.Upload=function(url,data){
        url='/api.php?u='+url;
        return this.connect(url,data,{ismultipart:true});
    }

    return a;

    }]);
});
