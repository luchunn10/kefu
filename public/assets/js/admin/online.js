



function getnow(data) {
    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/set/getchatnow",
        type:"post",
        data:{sdata:data},
        dataType:'json',
        success:function (res) {

            var a="";
            if(res.code == 0){
               getchat();
            }
        }
    });
}


//储存 频道
var chaarr = new Array();

//初始化 监听
var getonline = function () {
    getchat();
    $.cookie("time","");
    $(".conversation").empty();
};

window.onload = getonline();

// 获取访客状态
function getstatus(cha) {
    $.ajax({
        url:HJWEB_ROOT_URL+'/admin/set/getstatus',
        type:'post',
        data:{channel:cha},
        dataType:'json',
        success:function(res){
            if(res.code ==0){
                if(res.data){
                    $("#last_login_time").text(res.data.timestamp);
                    $("#login_times").text(res.data.login_times);
                    $("#name").text(res.data.name);
                    $("#tel").text(res.data.tel);
                  if(res.data.state == 'online'){
                    $("#v_state").text("在线");
                  }else{
                    $("#v_state").text("离线");
                  }
                }
            }
        }
    });
}

// 正在聊天的队列表
function getchat() {
    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/set/getchats",
        success: function (res) {
            let url = document.location.toString();
            let visiter_id;
            if(url.indexOf('=') > -1) {
                var arrUrl = url.split("=");
　　　　             visiter_id = arrUrl[1];
                $.ajax({
                    url:HJWEB_ROOT_URL+"/admin/custom/opencs",
                    type: 'post',
                    data: {
                        visiter_id: visiter_id,
                    },
                    success: function (res) {
                        
                    }
                })
            }
            if (res.code == 0) {
                $("#chat_list").empty();
                var sdata = $.cookie('cu_com');
                if (sdata) {
                    var json = $.parseJSON(sdata);
                    var debug = json.visiter_id;
                } else {
                    var debug = "";
                }
                var data = res.data;
                var a = '';
                let name;
                $.each(data, function (k, v) {
                   
                    var str = JSON.stringify(v);
                    chat_data['visiter'+v.vid] =v;
                    if(v.name || v.tel) {
                        name = "<span class='c_name'><span class='c_tag'>已留信息</span><span>" + v.visiter_name + "</span></span>";
                    }else {
                        name = "<span class='c_name'>" + v.visiter_name + "</span>";
                    }
                    if (debug == v.visiter_id) {

                        $(".chatbox").removeClass('hide');
                        $(".no_chats").addClass('hide');
                       if (v.state == 'online') {
                            a += '<div id="v' + v.channel + '" class="visiter onclick" onmouseover="showcut(this)" onmouseout="hidecut(this)" ><i class="layui-icon myicon hide" title="删除" style="font_weight:blod" onclick="cut(' + "'" + v.visiter_id + "'" + ')">&#x1006;</i><span id="c' + v.channel  + '" class="notice-icon hide"></span>';
                            a += "<div class='visit_content' onclick='choose(" +v.vid+ ")'><img class='am-radius v-avatar' id='img" +v.channel + "' src='" + v.avatar + "' width='50px'>"+name+"<span class='c_time'>" + v.timestamp + "</span><div id='msg" +v.channel  + "' class='newmsg'>"+v.content+"</div>";
                            a += '</div></div>';
                        } else {
                            a += '<div id="v' + v.channel + '" class="visiter onclick" onmouseover="showcut(this)" onmouseout="hidecut(this)"><i class="layui-icon myicon hide" title="删除" style="font_weight:blod" onclick="cut(' + "'" + v.visiter_id + "'" + ')">&#x1006;</i><span id="c' +v.channel + '" class="notice-icon hide"></span>';
                            a += "<div class='visit_content' onclick='choose(" +v.vid+ ")'><img class='am-radius v-avatar icon_gray' id='img" + v.channel  + "' src='" + v.avatar + "' width='50px'>"+name+"<span class='c_time'>" + v.timestamp + "</span><div id='msg" +v.channel  + "' class='newmsg'>"+v.content+"</div>";
                            a += '</div></div>';
                        }

                    } else {
                        if(v.count == 0){

                            if (v.state == 'online') {
                                a += '<div id="v' + v.channel + '" class="visiter" onmouseover="showcut(this)" onmouseout="hidecut(this)"><i class="layui-icon myicon hide" title="删除" style="font_weight:blod" onclick="cut(' + "'" + v.visiter_id + "'" + ')">&#x1006;</i><span id="c' +v.channel + '" class="notice-icon hide"></span>';
                                a += "<div class='visit_content' onclick='choose(" +v.vid+ ")'><img class='am-radius v-avatar' id='img" + v.channel + "' src='" + v.avatar + "'  width='50px'>"+name+"<span class='c_time'>" + v.timestamp + "</span><div id='msg" + v.channel + "' class='newmsg'>"+v.content+"</div>";
                                a += '</div></div>';
                            } else {
                                a += '<div  id="v' + v.channel + '" class="visiter" onmouseover="showcut(this)" onmouseout="hidecut(this)"><i class="layui-icon myicon hide" title="删除" style="font_weight:blod" onclick="cut(' + "'" + v.visiter_id + "'" + ')">&#x1006;</i><span id="c' + v.channel + '" class="notice-icon hide"></span>';
                                a += "<div class='visit_content' onclick='choose(" +v.vid+ ")'><img class='am-radius v-avatar icon_gray' id='img" + v.channel + "' src='" + v.avatar + "'  width='50px'>"+name+"<span class='c_time'>" + v.timestamp + "</span><div id='msg" + v.channel + "' class='newmsg'>"+v.content+"</div>";
                                a += '</div></div>';
                            }

                        }else{
                            if (v.count > 99) {
                                v.count = "99+";
                            }
                            if (v.state == 'online') {
                                a += '<div id="v' + v.channel + '" class="visiter" onmouseover="showcut(this)" onmouseout="hidecut(this)"><i class="layui-icon myicon hide" title="删除" style="font_weight:blod" onclick="cut(' + "'" + v.visiter_id + "'" + ')">&#x1006;</i><span id="c' +v.channel + '" class="notice-icon">'+v.count+'</span>';
                                a += "<div class='visit_content' onclick='choose(" +v.vid+ ")'><img class='am-radius v-avatar' id='img" + v.channel + "' src='" + v.avatar + "'  width='50px'>"+name+"<span class='c_time'>" + v.timestamp + "</span><div id='msg" + v.channel + "' class='newmsg'>"+v.content+"</div>";
                                a += '</div></div>';
                            } else {
                                a += '<div  id="v' + v.channel + '" class="visiter" onmouseover="showcut(this)" onmouseout="hidecut(this)"><i class="layui-icon myicon hide" title="删除" style="font_weight:blod" onclick="cut(' + "'" + v.visiter_id + "'" + ')">&#x1006;</i><span id="c' + v.channel + '" class="notice-icon">'+v.count+'</span>';
                                a += "<div class='visit_content' onclick='choose(" +v.vid+ ")'><img class='am-radius v-avatar icon_gray' id='img" + v.channel + "' src='" + v.avatar + "'  width='50px'>"+name+"<span class='c_time'>" + v.timestamp + "</span><div id='msg" + v.channel + "' class='newmsg'>"+v.content+"</div>";
                                a += '</div></div>';
                            }

                        }
                        
                     
                    }
                  
                });
                $("#chat_list").append(a);
            } else {
                $("#chat_list").empty();
                $(".chatbox").addClass('hide');
                $(".no_chats").removeClass('hide');
                $.cookie('cu_com', "");
            }
            var count = res.all_unread_count;
            if(count > 0) {
                if(count > 99) {
                    count = '99+'
                }
                if($("#layout-west")[0].offsetWidth == 180) {
                    $(".notices").removeClass('hide')
                }else if($("#layout-west")[0].offsetWidth == 80) {
                    $(".notices-icon").removeClass('hide')
                }
                $(".notices").text(count)
            }else if(count == 0) {
                $(".notices").text('')
                $(".notices").addClass('hide')
                $(".notices-icon").addClass('hide')
            }
        },
        complete:function(){
            choose_lock = false;
        }
    });
}

function showcut(obj){
    $(obj).children('i').removeClass('hide');
}

function hidecut(obj){
    $(obj).children('i').addClass('hide');
}

//获取队列的实时数据
function getwait() {

    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/set/getwait",
        dataType:'json',
        success: function (res) {

            if (res.code == 0) {
              
                $("#wait_list").empty();
                $("#waitnum").addClass('hide');
                if (!res.data.length) {
                    return;
                }
                var a = "";
                $.each(res.data, function (k, v) {

                    if(v.state == "online"){
                        a += '<div class="waiter">';
                        a += '<img id="img'+v.visiter_id+'" class="am-radius w-avatar v-avatar" src="' + v.avatar + '" width="50px" height="50px"><span class="wait_name">' + v.visiter_name + '</span>';
                        a += "<div class='newmsg'>"+v.groupname+"</div>";
                        a += '<i class="mygeticon " title="认领" onclick="get(' + "'" + v.visiter_id + "'" + ')"></i></div>';
                    }else{
                        a += '<div class="waiter">';
                        a += '<img id="img'+v.visiter_id+'"  class="am-radius w-avatar v-avatar icon_gray"  src="' + v.avatar + '" width="50px" height="50px"><span class="wait_name">' + v.visiter_name + '</span>';
                        a += "<div class='newmsg'>"+v.groupname+"</div>";
                        a += '<i class="mygeticon " title="认领" onclick="get(' + "'" + v.visiter_id + "'" + ')"></i></div>';
                    }
                });
                $("#wait_list").append(a);

                $("#notices-icon").removeClass('hide');
                $("#waitnum").removeClass('hide');
                $("#waitnum").text(res.num);
                document.title ="【有客户等待】"+myTitle;


            } else {

                document.title =myTitle;
            }
        }
    });

}


//获取黑名单
function getblacklist() {

    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/set/getblackdata",
        dataType:'json',
        success: function (res) {

            if (res.code == 0) {
              
                $("#black_list").empty();
                var data = res.data;
                var a = "";
                $.each(data, function (k, v) {

                    a += '<div class="visiter"><img class="am-radius v-avatar" src="' + v.avatar + '">';
                    a += ' <span style="font-size: 14px;color: #555555;line-height: 80px;margin-left: 82px">' + v.visiter_name + '</span><div style="position:absolute;right:0;top:30px;cursor: pointer;" onclick="recovery(' + "'" + v.visiter_id + "'" + ')"><img src="'+HJWEB_ROOT_URL+'/assets/images/admin/B/delete.png"></img></div></div>';
                });

                $("#black_list").append(a);
            } else {

                $("#black_list").empty();
            }
        }
    });
}




//获取ip的详细信息
var getip = function (cip) {
    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/set/getipinfo",
        type: "get",
        data: {
            ip: cip
        },
        dataType:'json',
        success: function (res) {

            if(res.code == 0){
                var data = res.data;
                var str = "";
                str += data[0] + " 、";
                str += data[1] + " 、";
                str += data[2];
                $(".iparea").text(str);
            }
           
        }
    })
};

//标记已看消息
function getwatch(cha) {
    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/set/getwatch",
        type: "post",
        data: {visiter_id: cha}
    });
}

//获取最近历史消息
function getdata(cha) {

    var avatver;
    var sdata = $.cookie("cu_com");
    if (sdata) {
        var jsondata = $.parseJSON(sdata);
        avatver = jsondata.avatar;
    }
    var showtime;
    var curentdata =new Date();
    var time =curentdata.toLocaleDateString();
    var cmin =curentdata.getMinutes();
    if($.cookie("hid") != "" ){
        var cid =$.cookie("hid");
    }else{
        var cid ="";
    }
  
    $.ajax({
        url:HJWEB_ROOT_URL+"/admin/set/chatdata",
        type: "post",
        data: {
            visiter_id: cha,hid:cid
        },
        dataType:'json',
        success: function (res) {
            // alert(res);
            if (res.code == 0) {
                getwatch(cha);
                var se = $("#chatmsg_submit").attr("name");
                var str = "";
                var data = res.data;
                var user = res.user;
                var mindata = null
                if(res.data.length >0){
                    mindata = data[0].cid;
                } else {
                    mindata = null;
                }

                var pic = $("#se_avatar").attr('src');

                str += '<div class="chatbox-name"><div class="chatbox-info">';
                str += '<div style="float:left;width:auto;margin-right:5px">'+user.visiter_name+'</div>';
                str += '<div class="group-list">';
                for(let i = 0;i < user.group_name_array.length;i++) {
                    str += '<span class="group-item">'+user.group_name_array[i]+'</span>'
                }
                str +='</div></div></div>';
                $.each(data, function (k, v) {
                    if (v.cid < mindata) {
                        console.log(v.cid);
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
                                minutes = minutes.toString();
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
                            
                            showtime = "";
                        }

                    }else{

                        var myDate = new Date(v.timestamp*1000);
                        var puttime =myDate.toLocaleDateString();
                        if(puttime == time){
                            showtime =myDate.getHours()+":"+myDate.getMinutes();

                        }else{

                            showtime =myDate.getFullYear()+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate()+" "+myDate.getHours()+":"+myDate.getMinutes();
                        }


                    }

                    getdata.puttime = v.timestamp;

                
                    
                    if (v.direction == 'to_visiter') {
                        str += '<li class="chatmsg"><div class="showtime">'+showtime+'</div>';
                        str += '<div class="" style="position: absolute;top: 26px;right: 0;"><img  class="my-circle cu_pic" src="' + pic + '" width="46px" height="46px"></div>';
                        str += "<div class='outer-right'><div class='service'>";
                        str += "<div class='chat-right_triangle'>" + "</div>";
                        str += "<pre>" + v.content + "</pre>";
                        str += "</div></div>";
                        str += "</li>";
                    } else{
                        str += '<div class="chatbox-name"><div class="chatbox-info">';
                        str += '<div style="float:left;width:auto;margin-right:5px">'+user.visiter_name+'</div>';
                        str += '<div class="group-list">';
                        for(let i = 0;i < user.group_name_array.length;i++) {
                            str += '<span class="group-item">'+user.group_name_array[i]+'</span>'
                        }
                        str +='</div></div></div>';
                        str += '<li class="chatmsg"><div class="showtime">' +showtime+ '</div><div class="" style="position: absolute;left:0;">';
                        str += '<img class="my-circle  se_pic" src="' + avatver + '" width="46px" height="46px"></div>';
                        str += "<div class='outer-left'><div class='customer'>";
                        str += "<div class='chat-right_triangle'>" + "</div>";
                        str += "<pre>" + v.content + "</pre>";
                        str += "</div></div>";
                        str += "</li>";
                    }

                });

                var div = document.getElementById("wrap");
                if($.cookie("hid") == ""){
                 
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

                    }else{
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
                },100)
                if(res.data.length >0){
                    $.cookie("hid",mindata);

                }

            }
        }
    });
}
