<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>每日签到</title>
    <style>
        body{
            margin:0;
        }
        img{
            width:100%;
            vertical-align:top;
        }
        .cover{
            position:relative;
            overflow:hidden;
        }
        .cover .bg{
            position:relative;
            z-index:-1;
        }
        .content{
            position:absolute;
            left:0;top:0;bottom:0;right:0;
            overflow:hidden;
        }
        .flex{
            display:flex;
            justify-content:space-between
        }
        .flex div{
            width:31.7%;
            margin-bottom:3.4%;
            position:relative;
        }
        .flex .active:after{
            content: "";
            position:absolute;
            bottom: 0;
            right: 0;
            left:0;
            top:0;
            background:url("/img/sign/success.png") 95% 80%/26% 26% no-repeat;
        }
        .content img{
            width:100%;
        }
        .width{
            padding:0 15.1%;
        }
        .width div{
            width:45%;
        }
        #sign{
            width:36.66%;
            margin:3% auto 0 auto;
        }
    </style>
</head>
<body>
<img src="/img/sign/top.jpg" alt="">
<div class="cover">
    <div class="content">
            <div class="flex">
                <div class="<?php echo ($signday+0)>=1?'active':''; ?>">
                    <img src="/img/sign/gift1.png" alt="">
                </div>
                <div class="<?php echo ($signday+0)>=2?'active':''; ?>">
                    <img src="/img/sign/gift2.png" alt="">
                </div>
                <div class="<?php echo ($signday+0)>=3?'active':''; ?>">
                    <img src="/img/sign/gift3.png" alt="">
                </div>
            </div>
            <div class="flex width">
                <div class="<?php echo ($signday+0)>=4?'active':''; ?>">
                    <img src="/img/sign/gift4.png" alt="">
                </div>
                <div class="<?php echo ($signday+0)>=5?'active':''; ?>">
                    <img src="/img/sign/gift5.png" alt="">
                </div>
            </div>
            <div class="flex width">
                <div class="<?php echo ($signday+0)>=6?'active':''; ?>">
                    <img src="/img/sign/gift6.png" alt="">
                </div>
                <div class="<?php echo ($signday+0)>=7?'active':''; ?>">
                    <img src="/img/sign/gift7.png" alt="">
                </div>
            </div>
            <div id="sign">
                <?php 
                    if($used === 0){
                        echo '<img src="/img/sign/sign.png" alt="">';
                    }else{
                        echo '<img src="/img/sign/sign_active.png" alt="">';
                    };
                ?>
            </div>
    </div>
    <img src="/img/sign/middle.jpg" class="bg">
</div>
<img src="/img/sign/bottom.jpg" alt="">
<script src="{{URL::asset('js/jquery-3.3.1.min.js')}}"></script>
<script>
$(function(){
    var used = <?php echo $used ?>+0,playerid='<?php echo $playerid; ?>';
    $('#sign').click(function(){
        if(used === 1){
            return false;
        }
        if(!playerid){
            var result = confirm('绑定账号失败\n请先下载游戏并用微信号登录');
            if(result){
                location.href = 'http://m.52y.com';
                return;
            }else{
                return;
            }
        }
        used=1;
        var level = <?php echo ($signday+1) ?>;
        var url = '/sign/signed',data = {level:level},index = level-1;
            var moneylist = [588,888,1088,1688,1888,2888,3888];
            $.post(url,data,function(res){
                if(res.error == 0){
                    alert('签到奖励金币'+moneylist[index]+'已发放至您的游戏邮箱中');
                    $('#sign img').attr('src','/img/sign/sign_active.png');
                    $('.flex div').eq(index).addClass('active');
                }else{
                    alert('签到失败！'+res.errmsg);
                    used=0;
                }
            })
    })
})
</script>
</body>
</html>