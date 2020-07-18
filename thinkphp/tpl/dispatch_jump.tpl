{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>跳转提示</title>

  {load href="__libs__/layui/layui.js"/}
  {load href="__libs__/layui/css/layui.css"/}
   <style>
       a{
         color: green;
       }

       a:hover{
        color: red;
       }

       .system-message{
           width: 500px;
           height: 250px;
           border-radius: 16px;
           background: #fff;
           margin:120px auto;
           position: relative;
           box-shadow: rgba(15, 66, 76, 0.25) 0px 0px 24px 0px;
       }

       .system-message div span{
           overflow: hidden;
           white-space:nowrap;
           text-overflow:ellipsis;
       }

       #href{
           background: #3399ff;
           height: 26px;
           width: 75px;
           text-align: center;
           padding-top: 3px;
           margin: 0 10px;
           border-radius: 16px;
           color: white;
           display: inline-block;
       }
   </style>


</head>
<body>
    <div class="system-message">
        <div style="position: absolute;top: 60px;left: 50px;">
            <img src="__image__/admin/B/error.png" style="width: 150px;height: 120px;" alt="" />
        </div>
    <?php switch ($code) {?>
       <?php case 1:?>

       <div style="width:80%;position: absolute;left: 230px;top: 60px;">
           <span style="display: inline-block;width: 230px;height: 40px;position: absolute;line-height: 40px"><?php echo(strip_tags($msg));?></span>
       </div>

       <?php break;?>
       <?php case 0:?>

       <div style="width:80%;position: absolute;left: 230px;top: 60px;">
           <span style="display: inline-block;width: 230px;height: 40px;position: absolute;line-height: 40px"><?php echo(strip_tags($msg));?></span>
       </div>
       <?php break;?>
     <?php } ?>

       <div style="width: 80%;position: absolute;left: 230px;top: 115px;">
          页面将在<b id="wait"><?php echo($wait);?></b>s后跳转
       </div>

        <div style="width: 80%;position: absolute;left: 230px;top: 155px;">
            没有跳转？点击<a id="href" href="<?php echo($url);?>">直接跳转</a>
        </div>
    </div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
</body>
</html>
