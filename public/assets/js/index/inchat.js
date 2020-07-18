/**
 * Created by chenrui on 2017/7/10.
 */

var e={'羊驼':'emo_01','神马':'emo_02','浮云':'emo_03','给力':'emo_04','围观':'emo_05','威武':'emo_06','熊猫':'emo_07','兔子':'emo_08','奥特曼':'emo_09','囧':'emo_10','互粉':'emo_11','礼物':'emo_12','微笑':'emo_13','嘻嘻':'emo_14','哈哈':'emo_15','可爱':'emo_16','可怜':'emo_17','抠鼻':'emo_18','吃惊':'emo_19','害羞':'emo_20','调皮':'emo_21','闭嘴':'emo_22','鄙视':'emo_23','爱你':'emo_24','流泪':'emo_25','偷笑':'emo_26','亲亲':'emo_27','生病':'emo_28','太开心':'emo_29','白眼':'emo_30','右哼哼':'emo_31','左哼哼':'emo_32','嘘':'emo_33','衰':'emo_34','委屈':'emo_35','呕吐':'emo_36','打哈欠':'emo_37','抱抱':'emo_38','怒':'emo_39','问号':'emo_40','馋':'emo_41','拜拜':'emo_42','思考':'emo_43','汗':'emo_44','打呼':'emo_45','睡':'emo_46','钱':'emo_47','失望':'emo_48','酷':'emo_49','好色':'emo_50','生气':'emo_51','鼓掌':'emo_52','晕':'emo_53','悲伤':'emo_54','抓狂':'emo_55','黑线':'emo_56','阴险':'emo_57','怒骂':'emo_58','心':'emo_59','伤心':'emo_60'};

var faceon = function () {
    $(".wl_faces_main").empty();
    var str =""
    str +='<ul>';
    str +='<li><img title="羊驼" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_01.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="神马" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_02.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="浮云" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_03.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="给力" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_04.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="围观" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_05.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="威武" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_06.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="熊猫" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_07.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="兔子" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_08.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="奥特曼" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_09.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="囧" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_10.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="互粉" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_11.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="礼物" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_12.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="微笑" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_13.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="嘻嘻" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_14.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="哈哈" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_15.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="可爱" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_16.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="可怜" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_17.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="抠鼻" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_18.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="吃惊" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_19.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="害羞" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_20.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="调皮" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_21.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="闭嘴" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_22.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="鄙视" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_23.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="爱你" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_24.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="流泪" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_25.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="偷笑" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_26.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="亲亲" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_27.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="生病" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_28.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="太开心" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_29.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="白眼" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_30.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="右哼哼" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_31.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="左哼哼" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_32.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="嘘" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_33.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="衰" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_34.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="委屈" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_35.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="呕吐" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_36.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="打哈欠" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_37.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="抱抱" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_38.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="怒" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_39.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="问号" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_40.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="馋" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_41.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="拜拜" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_42.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="思考" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_43.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="汗" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_44.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="打呼" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_45.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="睡" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_46.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="钱" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_47.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="失望" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_48.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="酷" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_49.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="好色" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_50.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="生气" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_51.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="鼓掌" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_52.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="晕" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_53.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="悲伤" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_54.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="抓狂" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_55.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="黑线" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_56.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="阴险" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_57.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="怒骂" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_58.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="心" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_59.gif onclick="emoj(this)"/></li>';
    str +='<li><img title="伤心" src='+HJWEB_ROOT_URL+'/upload/emoji/emo_60.gif onclick="emoj(this)"/></li>';
    str +="</ul>";
    $(".wl_faces_main").append(str);
    $(".tool_box").toggle();
    var e = window.event || arguments.callee.caller.arguments[0];
    e.stopPropagation();
};

$('body').click(function(){
    $(".tool_box").hide();
});

var emoj=function (obj) {
    var a=  $(obj).attr("title");
    var str=$("#text_in").val();
    var reg = new RegExp( '<' , "g" )
        str =str.replace(reg,'&lt;');

    var reg2 = new RegExp( '>' , "g" )     

        str =str.replace(reg2,'&gt;'); 
    var b = "";
    b += str+" face["+a+"]";
    $("#text_in").val(b);
    $(".tool_box").hide()

    $("#send-btn").show();
    $("#file").hide();
}

function put() {

    var value = $('input[name="upload"]').val();
    var index1=value.lastIndexOf(".");
    var index2=value.length;
    var suffix=value.substring(index1+1,index2);
    var debugs =suffix.toLowerCase();

    if (debugs == "jpg" || debugs == "gif" ||debugs == "png" ||debugs == "jpeg") {

        $("#picture").ajaxSubmit({
            url:HJWEB_ROOT_URL+'/admin/event/upload',
            type: "post",
            dataType:'json',
            data:{visiter_id:visiter_id,business_id: business_id, avatar: pic,record: record,service_id:service_id},
            success: function (res) {
                if(res.code == 0){

                    var msg =res.data;
                    var time;

                    if($.cookie("itime") == ""){
                        var myDate = new Date();
                            time = myDate.getHours()+":"+myDate.getMinutes();
                        var timestamp = Date.parse(new Date());
                        $.cookie("itime",timestamp/1000);
                    
                    }else{

                        var timestamp = Date.parse(new Date());
                        var lasttime =$.cookie("itime");
                        if((timestamp/1000 - lasttime) >30){
                            var myDate =new Date(timestamp);
                            time = myDate.getHours()+":"+myDate.getMinutes();
                        }else{
                            time ="";
                        }

                        $.cookie("itime",timestamp/1000);
                    }

                    var str = '';
                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                    str += '<div class="" style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle cu_pic" src="' + pic + '" width="40px" height="40px" style="border-radius:10px;"></div>';
                    str += "<div class='outer-right'><div class='customer' style='padding:0;border-radius:0;max-height:100px'>";
                    str += "<div class='chat-right_triangle'>" + "</div>";
                    str += "<pre>" + msg + "</pre>";
                    str += "</div></div>";
                    str += "</li>";

                    $(".conversation").append(str);
                    var div = document.getElementById("wrap");
                    div.scrollTop = div.scrollHeight;
                    $("img[src*='upload/images']").parent().parent('.customer').css({
                        padding: '0',borderRadius: '0',maxHeight:'100px'
                    });
                    $("img[src*='upload/images']").parent().parent('.service').css({
                        padding: '0',borderRadius: '0',maxHeight:'100px'
                    });
                    setTimeout(function(){
                        $('.chatmsg').css({
                            height: 'auto'
                        });
                    },0)
                }else{
                    layer.msg(res.msg,{icon:2});
                }

            }
        });

    } else {

        layer.msg("请选择图片", {icon: 2});
    }
}

// 文件上传
function putfile() {

    var value = $('input[name="folder"]').val();
    var sarr = value.split('\\');
    var name = sarr[sarr.length - 1];

    var arr = value.split(".");
    var debugs =arr[1].toLowerCase();
    if ( debugs == "js" ||  debugs == "css" ||  debugs == "html" ||  debugs == "php") {
        layer.msg("不支持该格式的文件", {icon: 2});
    } else {
        var myDate = new Date();
        var time =  myDate.getHours()+":"+myDate.getMinutes();
      
        $("#file").ajaxSubmit({
            url:HJWEB_ROOT_URL+'/admin/event/uploadfile',
            type: 'post',
            dataType:'json',
            data:{visiter_id:visiter_id,business_id: business_id, avatar: pic,record: record,service_id:service_id},
            success: function (res) {
                if(res.code == 0){
                    var str = '';
                    str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
                    str += '<div class="" style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle cu_pic" src="' + pic + '" width="40px" height="40px" style="border-radius:10px;"></div>';
                    str += "<div class='outer-right'><div class='customer'>";
                    str += "<div class='chat-right_triangle'>" + "</div>";
                    str += "<pre>";
                    str += "<div><a href='" + res.data + "' style='display: inline-block;text-align: center;min-width: 70px;text-decoration: none;' download='" + name + "'><i class='layui-icon' style='font-size: 60px;'>&#xe61e;</i><br>" + name + "</a></div>";
                    str += "</pre>";
                    str += "</div></div>";
                    str += "</li>";

                    $(".conversation").append(str);
                    var div = document.getElementById("wrap");
                    div.scrollTop = div.scrollHeight;
                    setTimeout(function(){
                        $('.chatmsg').css({
                            height: 'auto'
                        });
                    },0)
                    var msg = "<div><a href='" + res.data + "' style='display: inline-block;text-align: center;min-width: 70px;text-decoration: none;' download='" + name + "'><i class='layui-icon' style='font-size: 60px;'>&#xe61e;</i><br>" + name + "</a></div>";
                    var se = $('#services').text();
                    if(se){
                        var sid =$.cookie('services');
                    }

                 
                }else{
                    layer.msg(res.msg,{icon:2});
                }

            }
        });

    }
}


function getbig(obj) {


    var text = $(obj).attr('src');

    var img = new Image(); 

    img.src = $(obj).attr('src');
    var nWidth = img.width;
    var nHeight = img.height;

    var rate=nWidth/nHeight;
    
    var maxwidth =window.innerWidth;
    var maxheight=window.innerHeight;

    var size;

   if((nHeight-maxheight) > 0 || (nWidth-maxwidth) >0 ){
       
        var widths,heights;
        heights=maxheight-100;
        widths=heights*rate;  
        size=[widths+'px',heights+'px'];
    }else{
      
        size=[(nWidth-100)+'px',nHeight+'px'];
    }

    layer.open({
        type: 1,
        title: false,
        closeBtn: 1,
        area: size,
        skin: 'layui-layer-nobg', //没有背景色
        shadeClose: true,
        content: "<img src='" + text + "' style='width:100%;height:100%;'>"
    });
}


function getdata(){

    var showtime="";
     if($.cookie("services")){
         var se = $.cookie("services");
     }else{
        var se =0;
     }
   
    var curentdata =new Date();
    var time =curentdata.toLocaleDateString();
    var cmin =curentdata.getMinutes();

        if($.cookie("cid") != "" ){
            var cid =$.cookie("cid");
        }else{
            var cid ="";
        }

        $.ajax({
            url:HJWEB_ROOT_URL+"/admin/event/chatdata",
            type: "post",
            data: {hid:cid,vid:visiter_id,business_id:business_id,service_id:se},
            dataType:'json',
            success: function (res) {
                //添加 最近的 聊天 记录
                if(res.code == 0){
                var str = '';
                var mindata = null
                if(res.data.length >0){
                    mindata = res.data[0].cid;
                } else {
                    mindata = null;
                }
                $.each(res.data, function (k, v) {
                    if (v.cid < mindata) {
                        mindata = v.cid;
                    }

                    if(getdata.puttime){

                        if((v.timestamp -getdata.puttime) > 60){
                            var myDate = new Date(v.timestamp*1000);
                            var puttime =myDate.toLocaleDateString();
                            let year = myDate.getFullYear();
                            let month = myDate.getMonth()+1;
                            let date = myDate.getDate();
                            let hours = myDate.getHours();
                            let minutes = myDate.getMinutes();
                            if(hours < 10 ) {
                                minutes = '0'+minutes.toString();
                            }
                            if(minutes < 10 ) {
                                minutes = '0'+minutes.toString();
                            }
                            
                            if(puttime == time){
                                showtime =hours+":"+minutes;
                            }else{
                                showtime =year+"-"+month+"-"+date+" "+hours+":"+minutes;
                            }

                        }else{
                            showtime="";
                        }

                    }else{

                        var myDate = new Date(v.timestamp*1000);
                        var puttime =myDate.toLocaleDateString();
                        var myDate = new Date(v.timestamp*1000);
                        var puttime =myDate.toLocaleDateString();
                        let year = myDate.getFullYear();
                        let month = myDate.getMonth()+1;
                        let date = myDate.getDate();
                        let hours = myDate.getHours();
                        let minutes = myDate.getMinutes();
                        if(hours < 10 ) {
                            minutes = '0'+minutes.toString();
                        }
                        if(minutes < 10 ) {
                            minutes = '0'+minutes.toString();
                        }
                        
                        if(puttime == time){
                            showtime =hours+":"+minutes;
                        }else{
                            showtime =year+"-"+month+"-"+date+" "+hours+":"+minutes;
                        }
                    }
                    getdata.puttime = v.timestamp;

                    if (v.direction == 'to_service') {

                        str += '<li class="chatmsg"><div class="showtime">' + showtime + '</div>';
                        str += '<div class="" style="position: absolute;right: 2px;"><img class="my-circle" src="' + v.avatar + '" width="40px" height="40px" style="border-radius:10px;"></div>';
                        str += "<div class='outer-right'><div class='customer'>";
                        str += "<div class='chat-right_triangle'>" + "</div>";
                        str += "<pre>" + v.content + "</pre>";
                        str += "</div></div>";
                        str += "</li>";

                    } else {
                            str += '<li class="chatmsg"><div class="showtime">' + showtime + '</div><div style="position: absolute;left:3px;">';
                            str += '<img  class="my-circle  se_pic" src="' + v.avatar + '" width="40px" height="40px"></div>';
                            str += "<div class='outer-left'><div class='service'>";
                            str += "<div class='left_triangle'>" + "</div>";
                            str += "<pre>" + v.content + "</pre>";
                            str += "</div></div>";
                            str += "</li>";
                        
                    }
                });

                var div = document.getElementById("wrap");
                if($.cookie("cid") == ""){

                    $(".conversation").append(str);

                    if(div){
                        $("img").load(function(){
                            div.scrollTop = div.scrollHeight;
                        });
                    }
                }else{
                    $(".conversation").prepend(str);
                    if(res.data.length <= 2){
                        $("#top_div").remove();
                        $(".conversation").prepend("<div id='top_div' class='showtime'>已没有数据</div>");
                        if(div){
                            div.scrollTop =0;
                        }
                    }else {
                        if(div){
                            div.scrollTop = div.scrollHeight / 3.3;
                        }
                    }

                }
                $("img[src*='upload/images']").parent().parent('.customer').css({
                    padding: '0',borderRadius: '0',maxHeight:'100px'
                });
                $("img[src*='upload/images']").parent().parent('.service').css({
                    padding: '0',borderRadius: '0',maxHeight:'100px'
                });
                $("img[src*='data:image/']").parent().parent('.customer').css({
                    padding: '0',borderRadius: '0',maxHeight:'100px'
                });
                $("img[src*='data:image/']").parent().parent('.service').css({
                    padding: '0',borderRadius: '0',maxHeight:'100px'
                })
                setTimeout(function(){
                    $('.chatmsg').css({
                        height: 'auto'
                    });
                },0)
                if(res.data.length >2){
                    $.cookie("cid",mindata);
                }
              }
            }
        });
    }


   //获取tab
            function gettab(business_id){
               
               $.ajax({
                 url:HJWEB_ROOT_URL+'/admin/event/gettablist',
                 type:'post',
                 data:{business_id:business_id},
                 success:function(res){
                    if(res.code == 0){
                        var tab='';
                        var str='';
                        $.each(res.data,function(k,v){
                             tab+='<li>'+v.title+'</li>';
                                
                             str+=' <div class="layui-tab-item" style="width: 100%;overflow-y: auto;"><div class="markdown-body " ">'+v.content+'</div></div>';
                        });
                        $("#tablist").append(tab);
                        $("#tabcontent").append(str);

                     
                    }
                 }
               });
            }


var types=function(){
    if($.cookie('type') == 1){
     //快捷键
document.getElementById("text_in").onkeydown = function (e) {
    e = e || window.event;

     if (e.ctrlKey && e.keyCode == 13) {
        $("#text_in").append("<div><br/></div>");
        var o = document.getElementById("text_in").lastChild;            
        var textbox = document.getElementById('text_in');
        var sel = window.getSelection();
        var range = document.createRange();
        range.selectNodeContents(textbox);
        range.collapse(false);
        if(o){
         range.setEndAfter(o);//
         range.setStartAfter(o);// 
        }
     
        sel.removeAllRanges();
        sel.addRange(range);

     }        

    if(!e.ctrlKey && e.keyCode == 13){
      var a=$('#text_in').val();
    
      var str=a.replace(/(^\s*)|(\s*$)/g,"");
      if(!str){  
        layer.msg('内容不能为空',{icon:3});
        $('#text_in').val('');
        return false;
       }
       
       send();
       e.returnValue = false;
       return false;
    }
};

}else{

    document.getElementById("text_in").onkeydown = function (e) {
    e = e || window.event;
    if (e.ctrlKey && e.keyCode == 13) {
        if ($('#text_in').val() == "" || $.cookie("service") == '' ) {
            layer.msg('请输入信息');
        } else {
            send();
        }
    }
  }
  
 }


}



// 通知 客服

var init = function () {
    types();
    $.cookie("cid",'');
    wolive_connect();
    getquestion(business_id);
    gettab(business_id);
    
    hintstate =$.cookie('state');
    if(hintstate == 'undefinded'){
        hintstate ="on";
    }

    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/event/notice",
        type: 'post',
        data: {visiter_id:visiter_id, visiter_name: visiter, business_id: business_id, from_url: record, avatar: pic,groupid:cid,special:special},
        success: function (res) {
  
            if(res.code == 0){

                var data =res.data;
                $("#img_head").attr('src',data.avatar);
                $("#services").text(data.nick_name);

                $("#services").attr('data',data.service_id);

        service_id =data.service_id;
                $.cookie("services",data.service_id);
                getdata();

                if(data.state == 'online'){
                    $("#img_head").removeClass("icon_gray");
                }else{
                    $("#img_head").addClass("icon_gray");
                }
                
                // 问候语
                var msg = '';
                    msg += '<li class="chatmsg_no"><div style="position: absolute;top:0px;left:0px;">';
                    msg += '<img  class="my-circle" src="' + data.avatar + '" width="46px" height="46px" style="border-radius:10px;"></div>';
                    msg += "<div class='outer-left'><div class='service'>";
                    msg += "<div class='left_triangle'>" + "</div>";
                    msg += "<pre>" + data.content + "</pre>";
                    msg += "</div></div>";
                    msg += "</li>";
                    $(".conversation").append(msg);
                    var div = document.getElementById("wrap");
                    $("img[src*='upload/images']").parent().parent('.customer').css({
                        padding: '0',borderRadius: '0',maxHeight:'100px'
                    });
                    $("img[src*='upload/images']").parent().parent('.service').css({
                        padding: '0',borderRadius: '0',maxHeight:'100px'
                    });
                    div.scrollTop = $('.conversation')[0].scrollHeight;
                    $(".chatmsg").css({
                        height: 'auto'
                    });
                    $('.chatmsg_no').height($('.chatmsg_no').find('.service')[0].offsetHeight)
                   
            }else if(res.code == 1){

                layer.msg(res.msg,{icon:2});

            }else if(res.code == 2){
                $(".chatmsg").remove();
                $("#img_head").attr("src",HJWEB_ROOT_URL+"/assets/images/index/workerman_logo.png");
                $("#services").text("");
             
                // 告知客服在排队
                var msg='';
                msg+='<li class="chatmsg_notice"><div style="position: absolute;left:3px;">';
                msg += '<img  class="my-circle" src="' + HJWEB_ROOT_URL +'/assets/images/index/workerman_logo.png" width="40px" height="40px" style="border-radius:10px;"></div>';
                msg += "<div class='outer-left'><div class='service'>";
                msg += "<div class='left_triangle'>" + "</div>";
                msg+="<pre>通知 ： 现在还有"+res.data+" 人在排队，请等待 ....</pre>";
                msg+="</div></div>";
                msg+="</li>";


                $(".conversation").append(msg);

                $.cookie("services",'');

            }else if(res.code == 3){
                
                layer.msg(res.msg,{icon:2,end:function(){
                      window.location.href = url + "/index/message?business_id=" + business_id;
                }});
                $.cookie("services",'');

            }else if(res.code == 4){
                var data =res.data;
                $("#img_head").attr('src',data.avatar);
                $("#services").text(data.nick_name);
                $("#img_head").addClass("icon_gray");
                
                layer.open({
                    title:'提示框',
                    area: ['300px', '180px'],
                    content:'该客服离线中，是否转接其他客服？',
                    btn:['是','否'],
                    yes:function(){
                        $.ajax({
                            url:HJWEB_ROOT_URL+'/admin/event/getchangekefu',
                            type:'post',
                            data:{visiter_id:visiter_id,business_id:business_id},
                            success:function(res){
                                if(res.code == 0){

                                 layer.msg('转接中....',{icon:3,end:function(){
                                    location.reload();
                                 }});
                              }
                           }
                        });
                    },
                    btn2:function(){
                        layer.close();
                    }
                });
            }
        }
    });


    $.cookie("itime","");
}

window.onload = init();


function getquestion(business_id){
    $.ajax({
       url:HJWEB_ROOT_URL+'/admin/event/getquestion',
       type:'post',
       data:{business_id:business_id},
       success:function(res){
          if(res.code == 0){
              $.each(res.data,function(k,v){
                    var a = JSON.stringify(v);
                  $("#question_list").append('<div style="width:100%;margin-bottom:10px; cursor: pointer;" onclick="getanswer('+v.qid+',`'+v.question+'`)">'+v.question+'</div>');
                  if (v.keyword != '') {
                      $("#question_key_list").append('<div class="keyword-item swiper-slide" onclick="getanswer('+v.qid+',`'+v.question+'`)">'+v.keyword+'</div>');
                  }
              });
              if($('.keyword-item').length > 0) {
                $('.keyword').show();
                  $('#wrap').find('.conversation').css({
                      marginBottom: '35px',
                  });
              }
              let listWidth = $('#question_key_list').width();
              let allWidth = $('#question_key_list')[0].scrollWidth;
              if(listWidth < allWidth) {
                $('.toggle-right').show();
              }
          }
       }
    });
  
}

function getanswer(id,question){
    var str = '';
    str += '<li class="chatmsg"><div class="showtime"></div>';
    str += '<div  style="position: absolute;top: 26px;right: 2px;"><img class="my-circle"  src="' + pic + '" width="40px" height="40px" style="border-radius:10px;"></div>';
    str += "<div class='outer-right'><div class='customer'>";
    str += "<div class='chat-right_triangle'>" + "</div>";
    str += "<pre>" + question + "</pre>";
    str += "</div></div>";
    str += "</li>";
    $(".conversation").append(str);
    $.ajax({
        url:HJWEB_ROOT_URL+'/admin/event/getanswer',
        type:'post',
        data:{qid:id,service_id:service_id,visiter_id:visiter_id},
        success:function(res){

            if(res.code == 0){


             }else{
                layer.msg(res.msg);
             }

        }
    });
}
// 获取排队的数量
function getnums(id){
    var value ="";
    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/event/getwaitnum",
        type:"post",
        async: false,
        data:{business_id:id},
        success:function(res){
            value =res;
        }
    });
    return value;
}
// 发送消息

var send = function () {
  
    //获取 游客id
    var msg = $("#text_in").val();
    var reg = new RegExp( '<' , "g" )
    var msg2 =msg.replace(reg,'&lt;');

    var reg2 = new RegExp( '>' , "g" )     
        msg2 =msg2.replace(reg2,'&gt;'); 
        msg2 =msg2.replace('http://','');
     msg2 =msg2.replace('https://','');

     msg2=msg2.replace(/[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]/g,function (i) {
         
       return 'http://'+i;
    });    


    msg2=msg2.replace(/(https?|http|ftp|file):\/\/[-A-Za-z0-9+&@#/%?=~_|!:,.;]+[-A-Za-z0-9+&@#/%=~_|]/g,function (i) {
       
          a=i.replace('http://','');
        return '<a href="'+i+'" target="_blank">'+a+'</a>';
    });    


    if(msg2.indexOf("face[")!=-1){

       msg2=msg2.replace(/face\[([^\s\[\]]+?)\]/g,function (i) {
         var a = i.replace(/^face/g, "");
             a=a.replace('[','');
             a=a.replace(']','');  
         return '<img src="'+HJWEB_ROOT_URL+'/upload/emoji/'+e[a]+'.gif" />'
      });

    }
    
  
    if (msg == '' || $.cookie("service") == '') {
        layer.msg('请输入信息');
    } else {
        var time;

        if($.cookie("itime") == ""){
            var myDate = new Date();
                time = myDate.getHours()+":"+myDate.getMinutes();
            var timestamp = Date.parse(new Date());
            $.cookie("itime",timestamp/1000);
        
        }else{

            var timestamp = Date.parse(new Date());
            var lasttime =$.cookie("itime");
            if((timestamp/1000 - lasttime) >30){
                var myDate =new Date(timestamp);
                time = myDate.getHours()+":"+myDate.getMinutes();
            }else{
                time ="";
            }

            $.cookie("itime",timestamp/1000);
        }

        var str = '';
        str += '<li class="chatmsg"><div class="showtime">' + time + '</div>';
        str += '<div class="" style="position: absolute;top: 26px;right: 2px;"><img  class="my-circle cu_pic" src="' + pic + '" width="40px" height="40px" style="border-radius:10px;"></div>';
        str += "<div class='outer-right'><div class='customer'>";
        str += "<div class='chat-right_triangle'>" + "</div>";
        str += "<pre>" + msg2 + "</pre>";
        str += "</div></div>";
        str += "</li>";

        $(".conversation").append(str);
        $("#text_in").val('');
        $("#file").show();
        $("#send-btn").hide();
        var div = document.getElementById("wrap");
        div.scrollTop = $('.conversation')[0].scrollHeight;
        $(".chatmsg").css({
            height: 'auto'
        });

        $.ajax({
            url:HJWEB_ROOT_URL+"/admin/event/chat",
            type: "post",
            data: {visiter_id:visiter_id,content: msg2,business_id: business_id, avatar: pic,record: record,service_id:service_id},
            dataType:'json',
            success:function(res){
                if(res.code != 0){
                    layer.msg(res.msg,{icon:2});
                }
            }
        });
    }
}
