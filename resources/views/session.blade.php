@extends('share.base')

@section('content')
            <h2 class="ui header">会话列表</h2>
            <button class="ui primary basic button create">创建会话</button>
            <button class="ui primary basic button records">获取客服聊天记录</button>
            <table class="striped ui celled table">
                <thead>
                    <tr>
                        <th>创建时间</th>                        
                        <th>用户账号</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($data as $val){
                    print <<<EOT
                <tr>
                    <td>{$val['createtime']}</td>
                    <td>{$val['nickname']}</td>
                    <td>
                        <button class="ui negative basic button delete" openid='{$val['openid']}'>关闭会话</button>
                    </td>
                </tr>
EOT;

                }
                ?>
                </tbody>
            </table>

            <h3>未接入会话列表</h3>
            <table class="striped ui celled table">
                    <thead>
                        <tr>
                            <th>最后消息时间</th>                        
                            <th>用户id</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($waiter as &$val){
                        $val['latest_time'] = date('Y-m-d G:i:s',$val['latest_time']);
                        print <<<EOT
                    <tr>
                        <td>{$val['latest_time']}</td>
                        <td>{$val['openid']}</td>
                    </tr>
EOT;
                    }
                    ?>
                </tbody>
            </table>

            <h3>聊天记录</h3>
            <table class="striped ui celled table">
                <thead>
                    <tr>
                        <th>openid</th>                        
                        <th>消息类型</th>
                        <th>聊天记录</th>
                        <th>操作时间</th>
                        <th>客服账号</th>
                    </tr>
                </thead>
                <tbody id='parent'>
                </tbody>
            </table>

            <div class="ui basic modal" id='createPanel'>
                <div class="header">创建会话</div>
                <div class="content">
                    <div class="ui input">
                            <input type="text" placeholder="openid" id='openid'>
                    </div>
                    <input type="hidden" id='kf_account' value="<?php echo $kf_account ?>">
                </div>
                <div class="actions">
                    <div class="ui red basic cancel inverted button">
                    <i class="remove icon"></i>
                    否
                    </div>
                    <div class="ui green ok inverted button createbtn">
                    <i class="checkmark icon"></i>
                    是
                    </div>
                </div>
            </div>
            
            <div class="ui basic modal" id='recordsPanel'>
                    <div class="header">获取聊天记录</div>
                    <div class="content">
                        <div class="ui input"><input type="text" placeholder="起始时间(eg:2015-06-07)" id='startTime'></div>
                        <div class="ui input"><input type="text" placeholder="结束时间(eg:2015-06-08)" id='endTime'></div>
                        <div class="ui input"><input type="text" placeholder="从第几条开始获取" id='pageIndex'></div>
                        <div class="ui input"><input type="text" placeholder="拉取条数(最大20)" id='pageSize'></div>
                    </div>
                    <div class="actions">
                        <div class="ui red basic cancel inverted button">
                        <i class="remove icon"></i>
                        否
                        </div>
                        <div class="ui green ok inverted button recordsbtn">
                        <i class="checkmark icon"></i>
                        是
                        </div>
                    </div>
            </div>

            <div class="ui basic modal" id='deletePanel'>
                <div class="header">是否关闭?</div>
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
                    <form action="/staff/avatar" method="post" enctype="multipart/form-data" style='opacity:0;width:100%;height:100%;position:absolute;' id="myForm">
                        <input type="file" name="file" id="file" style="opacity:0;width:100%;height:100%;position:absolute;left:0;top:0;z-index:1;">
                        <input type="hidden" name="kf_account" id="kf_account">
                    </form>
                <div id='path2'>上传头像</div>
            </div>
        @endsection

        @section('js')
        <script>

        $(document).ready(function(){

            //新建
            var openid = '';
            var kf_account = $('#kf_account').val();
            $(".create").click(function(){
                $('#createPanel').modal('show');
            });

            $(".createbtn").click(function(){
                var openid = $('#openid').val();

                if(openid == ''){
                    alert('请填写必要参数!');
                    return;
                }
                
                post("sessioncreate",
                    {
                        openid:openid,
                        kf_account:kf_account
                    },
                    function(data,status){
                        window.location.reload()
                    });
            });

            //关闭
            $(".delete").click(function(){
                openid = $(this).attr('openid');
                $('#deletePanel').modal('show');
            });

            $(".deletebtn").click(function(){
                post("close",
                    {
                        kf_account:kf_account,
                        openid:openid
                    },
                    function(data,status){
                        window.location.reload()
                    });
            });

            //聊天记录
            $(".records").click(function(){
                $('#recordsPanel').modal('show');
            });

            $(".recordsbtn").click(function(){
                starttime = $('#startTime').val();
                endtime = $('#endTime').val();
                pageindex = $('#pageIndex').val();
                pagesize = $('#pageSize').val();
                if(starttime == '' || endtime == '' || pageindex == '' || pagesize == ''){
                    alert('请填写必要参数!');
                    return;
                }
                post("/staff/records",
                    {
                        starttime:starttime,
                        endtime:endtime,
                        pageindex:pageindex,
                        pagesize:pagesize
                    },
                    function(data){
                        var str = '';
                        data.forEach(element => {
                            var opercode = element.opercode == 2003? '客服接收消息':'客服发送信息';
                            var obj = '<tr>\
                                        <td>' + element.openid + '</td>\
                                        <td>' + opercode + '</td>\
                                        <td>' + element.text + '</td>\
                                        <td>' + element.time + '</td>\
                                        <td>' + element.worker + '</td>\
                                    </tr>'
                            str += obj;
                        });
                        $('#parent').append(str);
                    });
            });

            function post(url,data,success){
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    success: function(data){
                        //console.log(data);
                        if(data.errcode == 0){
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

        });
        </script>
@endsection