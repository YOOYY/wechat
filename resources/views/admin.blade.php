@extends('share.base')

@section('content')
            <h2 class="ui header">管理员列表</h2>
            <div>
                <button class="ui primary basic button" id='create'>添加账户</button>
                <a class="ui primary basic button" href="/auth/logout">退出账户</a>
            </div>
            <table class="striped ui celled table">
                <thead>
                    <tr>
                        <th>用户名</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>备注</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($data as $val){
                    $created_at = date('Y-m-d G:i:s',$val->created_at);
                    print <<<EOT
                <tr>
                    <td>{$val->name}</td>
                    <td>{$created_at}</td>
                    <td>{$val->updated_at}</td>
                    <td>{$val->note}</td>
                    <td>
                        <button class="ui negative basic button update" id="{$val->id}" name="{$val->name}" note="{$val->note}" password="{$val->password}">修改</button>
                        <button class="ui negative basic button delete" id="{$val->id}">删除</button>
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
                <div class="header">添加/修改账户</div>
                <div class="content">
                        <div class="ui input">
                                <input type="text" placeholder="用户名" id='name' class='check'>
                        </div>
                        <div class="ui input">
                            <input type="text" placeholder="密码" id='password' class='check'>
                        </div>
                        <div class="ui input">
                            <input type="text" placeholder="备注" id='note'>
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
        @endsection

        @section('js')
        <script>
            $(document).ready(function(){
                var action = 'create',id=0;

                //新建
                $("#create").click(function(){
                    $('#name').val('');
                    $('#password').val('');
                    $('#note').val('');
                    $('#panel').modal('show');
                    action = 'create';
                });

                //更新
                $(".update").click(function(){
                    $('#name').val($(this).attr('name'));
                    $('#note').val($(this).attr('note'));
                    $('#panel').modal('show');
                    id = $(this).attr('id');
                    action = 'update';
                });

                $(".subbtn").click(function(){
                    var name = $('#name').val(),
                        password = $('#password').val(),
                        note = $('#note').val();
                    if(!check()){
                            return;
                    }
                    if(action == 'create'){
                        $.post("create",
                        {
                            name:name,
                            password:password,
                            note:note
                        },
                        function(data,status){
                            window.location.reload()
                        });
                    }else{
                        post("update",
                        {
                            id:id,
                            name:name,
                            password:password,
                            note:note
                        },
                        function(data){
                            window.location.reload()
                        });
                    }
                });

                //删除
                $(".delete").click(function(){
                    id = $(this).attr('id');
                    $('#deletePanel').modal('show');
                });

                $(".deletebtn").click(function(){
                    post("delete",
                        {
                            id:id
                        },
                        function(data){
                            window.location.reload()
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

                //检查必要参数
                function check(){
                    var arr = $(".check").toArray(),flag = true;
                    for(var i = 0;i<arr.length;i++){
                        if(arr[i].value == ''){
                            flag = false;
                        }
                    };
                    if(flag == false){
                        alert('请填写必要参数!');
                    }
                    return flag;
                }
            });
        </script>
@endsection