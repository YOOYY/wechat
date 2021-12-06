@extends('share.base')

@section('content')
            <h2 class="ui header">用户标签列表</h2>
            <div>
                <button class="ui primary basic button" id='create'>添加标签</button>                
            </div>
            <table class="striped ui celled table">
                <thead>
                    <tr>
                        <th>名称</th>
                        <th>数量</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($data as $val){
                    print <<<EOT
                <tr>
                    <td>{$val->name}</td>
                    <td>{$val->count}</td>
                    <td>
EOT;
                if($val->id>3){
                    echo '<button class="ui negative basic button update" tagId="'.$val->id.'" content="'.$val->name.'">修改标签</button>';
                    echo '<button class="ui negative basic button delete" tagId="'.$val->id.'" content="'.$val->name.'">删除标签</button>';
                }
                print <<<EOT
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
                <div class="header">标签名</div>
                <div class="content">
                        <div class="ui input">
                                <input type="text" placeholder="标签名" id='content' class="check">
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
            var action = 'create',tagId='';

            //新建
            $("#create").click(function(){
                $('#content').val('');
                $('#panel').modal('show');
                action = 'create';
            });

            //更新
            $(".update").click(function(){
                $('#content').val($(this).attr('content'));
                $('#panel').modal('show');
                tagId = $(this).attr('tagId');                
                action = 'update';
            });

            $(".subbtn").click(function(){
                var name = $('#content').val();
                if(!check()){
                    return;
                }
                if(action == 'create'){
                    post("create",
                    {
                        name:name
                    },
                    function(data,status){
                        window.location.reload()
                    });
                }else{
                    post("update",
                    {
                        name:name,
                        tagId:tagId
                    },
                    function(data){
                        window.location.reload()
                    });
                }
            });

            //删除
            $(".delete").click(function(){
                tagId = $(this).attr('tagId');
                $('#deletePanel').modal('show');
            });

            $(".deletebtn").click(function(){
                post("delete",
                    {
                        tagId:tagId
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