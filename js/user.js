function xs_privacy_accept() {
       var date = new Date();
        date.setTime(date.getTime()+28*24*60*60*1000);
        var str='xs_framework_privacy=accept;expires='+date.toGMTString()+';path=/'
        document.cookie = str;
        location.reload();
}
