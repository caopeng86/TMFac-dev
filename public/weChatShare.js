function ajax(){
    var ajaxData = {
        type:arguments[0].type || "GET",
        url:arguments[0].url || "",
        async:arguments[0].async || "true",
        data:arguments[0].data || null,
        dataType:arguments[0].dataType || "text",
        contentType:arguments[0].contentType || "application/x-www-form-urlencoded",
        beforeSend:arguments[0].beforeSend || function(){},
        success:arguments[0].success || function(){},
        error:arguments[0].error || function(){}
    }
    ajaxData.beforeSend()
    var xhr = createxmlHttpRequest();
    xhr.responseType=ajaxData.dataType;
    xhr.open(ajaxData.type,ajaxData.url,ajaxData.async);
    xhr.setRequestHeader("Content-Type",ajaxData.contentType);
    xhr.send(convertData(ajaxData.data));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if(xhr.status == 200){
                ajaxData.success(xhr.response)
            }else{
                ajaxData.error()
            }
        }
    }
}

function createxmlHttpRequest() {
    if (window.ActiveXObject) {
        return new ActiveXObject("Microsoft.XMLHTTP");
    } else if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    }
}

function convertData(data){
    if( typeof data === 'object' ){
        var convertResult = "" ;
        for(var c in data){
            convertResult+= c + "=" + data[c] + "&";
        }
        convertResult=convertResult.substring(0,convertResult.length-1)
        return convertResult;
    }else{
        return data;
    }
}
ajax({
    type:"POST",
    url:"/member/wechatpublic/getSignPackage",
    dataType:"json",
    data:{url:encodeURIComponent(window.location.href)},
    beforeSend:function(){
        //some js code
    },
    success:function(data){
       if(data.code==200){
         wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: data.data.appId, // 必填，公众号的唯一标识
            timestamp: data.data.timestamp, // 必填，生成签名的时间戳
            nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
            signature: data.data.signature,// 必填，签名
            jsApiList: [
                'translateVoice',
                'updateAppMessageShareData',
                'updateTimelineShareData'
            ] // 必填，需要使用的JS接口列表
        });
       }
  
    },
    error:function(){

    }
})

function weChatShareConfig(){
    var title=document.title==""?" ":document.title;
    var desc=" ";
    var link=window.location.href;
    var imgurl=window.location.href.split("//")[0]+"//"+window.location.host+"/public/uploads/logo.png";
    if(arguments.length==1){
        imgurl=arguments[0]
    }else if(arguments.length==2){
        title=arguments[0];
        imgurl=arguments[1]
    }else if(arguments.length==3){
        title=arguments[0];
        link=arguments[1];
        imgurl=arguments[2];
    }else if(arguments.length==4){
        title=arguments[0];
        desc=arguments[1];
        link=arguments[2];
        imgurl=arguments[3];
    }
    wx.ready(function(){
        wx.updateAppMessageShareData({
            title: title, // 分享标题
            desc:  desc, // 分享描述
            link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: imgurl, // 分享图标
            success: function () {
                // 设置成功
            }
        });
        wx.updateTimelineShareData({
            title:  title, // 分享标题
            link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl:imgurl, // 分享图标
            success: function () {
                // 设置成功
            }
        });
    });
}