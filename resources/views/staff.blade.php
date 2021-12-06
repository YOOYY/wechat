@extends('share.base')

@section('content')
            <h2 class="ui header">客服列表</h2>
            <button class="ui primary basic button create">添加客服</button>
            <a class="ui primary basic button" href="https://mp.weixin.qq.com/cgi-bin/loginpage" target="blank">邀请成为客服</a>
            <button class="ui primary basic button send">发送消息</button>
            <button class="ui primary basic button state">获取客户会话状态</button>

            <table class="striped ui celled table">
                <thead>
                    <tr>
                        <th>ID</th>                        
                        <th>帐号</th>
                        <th>头像</th>
                        <th>昵称</th>
                        <th>微信号</th>
                        <th>在线</th>
                        <th>绑定邀请微信号</th>
                        <th>邀请过期时间</th>
                        <th>邀请状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($data as &$val){
                print <<<EOT
                <tr>
                    <td>{$val->kf_id}</td>
                    <td>{$val->kf_account}</td>
                    <td><img src='{$val->kf_headimgurl}'></td>
                    <td>{$val->kf_nick}</td>            
                    <td>{$val->kf_wx}</td>
                    <td>{$val->online}</td>
                    <td>{$val->invite_wx}</td>
                    <td>{$val->invite_expire_time}</td>
                    <td>{$val->invite_status}</td>
                    <td>
                        <button class="ui negative basic button update" kf_account='{$val->kf_account}' kf_nick='{$val->kf_nick}'>修改客服</button>
                        <button class="ui negative basic button delete" kf_account='{$val->kf_account}'>删除客服</button>
                        <a class="ui negative basic button" href="/staff/sessionlists?kf_account={$val->kf_account}">会话列表</a>
                        <button class="ui negative basic button uploadimg" kf_account="{$val->kf_account}">上传头像</button>
                    </td>
                </tr>
EOT;

                }
                ?>
                </tbody>
                <tfoot>
                        <tr><th colspan="19"><div class="zxf_pagediv"></div></th></tr>
                </tfoot>
            </table>

            <div class="ui basic modal" id='panel'>
                <div class="header">添加/修改客服</div>
                <div class="content">
                    <h4>客服账号</h4>
                    <p>完整客服帐号，格式为：帐号前缀@公众号微信号，帐号前缀最多10个字符，必须是英文、数字字符或者下划线，后缀为公众号微信号，长度不超过30个字符</p>
                    <div class="ui input">
                            <input type="text" placeholder="客服账号" id='kf_account'>
                    </div>
                    <div class="ui input">
                            <input type="text" placeholder="客服昵称(最长16字)" id='kf_nick'>
                    </div>
                </div>
                <div class="actions">
                    <div class="ui red basic cancel inverted button">
                    <i class="remove icon"></i>
                    否
                    </div>
                    <div class="ui green ok inverted button subbtn">
                    <i class="checkmark icon"></i>
                    是
                    </div>
                </div>
            </div>

            <div class="ui basic modal" id='sendPanel'>
                <div class="header">发送消息</div>
                <div class="content">
                        <div class="ui input"><input type="text" placeholder="用户openId" id='openId'></div>
                        <div class="ui input"><input type="text" placeholder="指定客服名称(可为空)" id='send_account'></div>
                        <div class="ui input">
                            <select class="ui dropdown" id="message">
                                <option value="">消息名称</option>
                                <?php
                                    foreach($message as $id => $name){
                                        echo '<option value="'.$id.'">'.$name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                </div>
                <div class="actions">
                    <div class="ui red basic cancel inverted button">
                    <i class="remove icon"></i>
                    否
                    </div>
                    <div class="ui green ok inverted button sendbtn">
                    <i class="checkmark icon"></i>
                    是
                    </div>
                </div>
            </div>

            <div class="ui basic modal" id='statePanel'>
                    <div class="header">查询状态</div>
                    <div class="content">
                            <div class="ui input"><input type="text" placeholder="用户openId" id='openIdstate'></div>
                    </div>
                    <div class="actions">
                        <div class="ui red basic cancel inverted button">
                        <i class="remove icon"></i>
                        否
                        </div>
                        <div class="ui green ok inverted button statebtn">
                        <i class="checkmark icon"></i>
                        是
                        </div>
                    </div>
                </div>

            <div class="ui basic modal" id='deletePanel'>
                <div class="header">是否删除?</div>
                <div class="actions">
                    <div class="ui red basic cancel inverted button">
                    <i class="remove icon"></i>
                    否
                    </div>
                    <div class="ui green ok inverted button deletebtn">
                    <i class="checkmark icon"></i>
                    是
                    </div>
                </div>
            </div>

            <div class="ui negative basic button" style="position:relative;display:none">
                    <form action="avatar" method="post" enctype="multipart/form-data" id="myForm">
                        <input type="file" name="file" id="file">
                        <input type="hidden" name="kf_account" id="kf_headimgurl">
                    </form>
                <div id='path2'>上传头像</div>
            </div>
        @endsection

        @section('js')
        <script>

        $(document).ready(function(){
            var action = 'create',kf_id=0,kf_account='';

            //新建
            $(".create").click(function(){
                $('#kf_account').val('');
                $('#kf_nick').val('');
                $('#panel').modal('show');
                $('#kf_account').show();                
                action = 'create';
            });

            //更新
            $(".update").click(function(){
                $('#panel').modal('show');
                $('#kf_account').val($(this).attr('kf_account'));
                $('#kf_nick').val($(this).attr('kf_nick'));
                $('#kf_account').hide();
                action = 'update';
            });

            $(".subbtn").click(function(){
                var kf_account = $('#kf_account').val();
                var kf_nick = $('#kf_nick').val();

                if(action == 'create'){
                    url = 'create';
                }else{
                    url = 'update';
                }
                if(kf_account == '' || kf_nick == ''){
                    alert('请填写必要参数!');
                    return false;
                }
                post(url,
                    {
                        kf_account:kf_account,
                        kf_nick:kf_nick
                    },
                    function(data){
                        window.location.reload()
                    });
            });

            //删除
            $(".delete").click(function(){
                $('#deletePanel').modal('show');
                kf_account = $(this).attr('kf_account');
            });

            $(".deletebtn").click(function(){
                $.post("delete",
                    {
                        kf_account:kf_account
                    },
                    function(data,status){
                        window.location.reload()
                    });
            });

            //上传头像
            $(".uploadimg").click(function(){
                kf_account = $(this).attr('kf_account');
                $("#file").trigger("click");
            })

            var uploading = false, maxSize = 5;

            $("#file").on("change", function(){
                var size = $(this).size;
                var filesize = (size / 1024 / 1024).toFixed(2);
                if(filesize >= maxSize){
                    alert('图片过大!');
                }
                $("#kf_headimgurl").val(kf_account);
                if(uploading){
                    alert("文件正在上传中，请稍候");
                    return false;
                }
                $.ajax({
                    url: "avatar",
                    type: 'POST',
                    cache: false,
                    data: new FormData($('#myForm')[0]),
                    processData: false,
                    contentType: false,
                    dataType:"json",
                    beforeSend: function(){
                        uploading = true;
                    },
                    success : function(data) {
                        if (data.errcode == 0) {
                            window.location.reload()
                        } else {
                            alert("上传出错!");
                        }
                        uploading = false;
                    }
                });
            });

            //发送
            $(".send").click(function(){
                $('#message').val('');
                $('#openId').val('');
                $('#send_account').val('');
                $('#sendPanel').modal('show');
            });

            $(".sendbtn").click(function(){
                var kf_account = $('#send_account').val();
                var message = $('#message').val();
                var openId = $('#openId').val();
                if(message == '' || openId == ''){
                    alert('请填写必要参数!');
                    return false;
                }
                post("send",
                    {
                        kf_account:kf_account,
                        openId:openId,
                        message:message
                    },
                    function(data){
                        alert('发送成功!');
                    });
            });

            //查询状态
            $(".state").click(function(){
                $('#openIdstate').val('');
                $('#statePanel').modal('show');
            });

            $(".statebtn").click(function(){
                var openid = $('#openIdstate').val();
                if(openid == ''){
                    alert('请填写必要参数!');
                    return;
                }
                post("state",
                    {
                        openid:openid
                    },
                    function(data){
                        var createtime;
                        if(data.createtime == 0){
                            createtime = '无';
                        }else{
                            createtime = timestampToTime(createtime);                            
                        }
                        alert('会话接入的时间:'+createtime+'\n'+'正在接待的客服:'+data.kf_account);
                    });
            });

            //翻页
            $(".zxf_pagediv").createPage({
                pageNum: <?php echo $page;?>,
                current: <?php echo $current;?>,
                backfun: function(e) {
                    window.location.href="lists?start="+e.current;
                }
            });

            function post(url,data,success){
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function(data){
                        console.log(data);
                        if(data.errcode==0){
                            success(data.errmsg);
                        }else{
                            alert(data.errmsg);
                        }
                    },
                    error:function(){
                        alert('网络错误!');
                    }
                });
            }

            function timestampToTime(timestamp) {
                var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
                var Y = date.getFullYear() + '-';
                var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
                var D = date.getDate() + ' ';
                var h = date.getHours() + ':';
                var m = date.getMinutes() + ':';
                var s = date.getSeconds();
                return Y+M+D+h+m+s;
            }

            $('.ui.dropdown').dropdown();
        });
$(function() {
    window.WxImgTemp = '';
    var article = '.table'; //定位，其下所以的img都会做处理
    var showWxImg = function(jQele) { //jQele 为 jquery 对象
        var url = jQele.attr('src'),
            time = new Date().getTime(),
            frameid = 'wxImg_' + time;
        window.WxImgTemp = '<img id="img" style="width:50px" height="50px" src=\'' + url + '?' + time + '\' />\
                <script>window.onload = function() {\
                parent.document.getElementById(\'' + frameid + "').height = document.getElementById('img').height+'px';}<//script>";
        var iframe = '<iframe id="' + frameid + '" \
                              src="javascript:parent.WxImgTemp;" \
                              frameBorder="0" scrolling="no" width="50px" height="50px" style="padding:0;margin:0"></iframe>';
        jQele.after(iframe).remove()
    };
    $(article + ' img').each(function(index, element) {
        showWxImg($(this))
    })
    window.WxImgTemp = '';
})
        </script>
@endsection