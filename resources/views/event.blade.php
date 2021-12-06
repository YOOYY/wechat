@extends('share.base')

@section('content')
    <h2 class="ui header">回复消息列表</h2>
    <div>
        <button class="ui primary basic button" id='create'>创建回复消息</button>
    </div>
    <p>
        1.当用户发送文本、图片、视频、图文、地理位置这五种消息时，开发者只能回复1条图文消息；其余场景最多可回复8条图文消息<br>
        2.添加文本消息时,事件类型可为空.
    </p>
    <table class="striped ui celled table">
        <thead>
            <tr>
                <th>接受消息类型</th>
                <th>关键词</th>
                <th>回复消息</th>
                <th>操作</th>

            </tr>
        </thead>
        <tbody>
<?php
    foreach($info as &$val){

        switch ($val->type) {
            case 'text':
                $type = '文本消息';
                break;
            case 'event':
                $type = '事件消息';
                break;
            case 'image':
                $type = '图片消息';
                break;
            case 'voice':
                $type = '音频消息';
                break;
            case 'video':
                $type = '视频消息';
                break;
            case 'location':
                $type = '位置消息';
                break;
            case 'link':
                $type = '链接消息';
                break;
            case 'other':
                $type = '其他消息';
                break;
            default:
                $type = '未知消息';
                break;
        }
        if($val->type == 'event' && $val->eventtype == 'subscribe'){
            $type = '订阅消息';
        }
        if($val->type == 'text' && $val->eventtype == 'default'){
            $type = '默认文本消息';
        }
                print <<<EOT
                <tr>
                <td>{$type}</td>
                <td>{$val->keyword}</td>
                <td>{$val->name}</td>
                <td><button class="ui primary basic button update" uid='{$val->id}' keyword='{$val->keyword}' message='{$val->message}'>修改</a></button>
                    <button class="ui primary basic button delShow" uid='{$val->id}'>删除</a></button></td>
                </tr>
EOT;
    }
?>
                </tbody>
                <tfoot>
                    <tr><th colspan="19"><div class="zxf_pagediv"></div></th></tr>                    
                </tfoot>
            </table>
        {{-- 更新 --}}
        <div class="ui basic modal" id="panel">
            <div class="header">新建/修改事件</div>
            <div class="content">
                <div class="ui input" id="keywordbox"><input type="text" placeholder="关键词" id='keyword'><input type="text" style="display:none" id='type'></div>
                
                <div class="ui input">
                    <select class="ui dropdown" id="message">
                        <option value="">回复消息名称</option>
                        <?php
                            foreach($message as $name => $id){
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
                <div class="ui green ok inverted button" id='subbtn'>
                <i class="checkmark icon"></i>
                是
                </div>
            </div>
        </div>

        {{-- 删除 --}}
        <div class="ui basic modal del">
            <div class="header">是否删除?</div>

            <div class="actions">
                <div class="ui red basic cancel inverted button">
                <i class="remove icon"></i>
                否
                </div>
                <div class="ui green ok inverted button delBtn">
                <i class="checkmark icon"></i>
                是
                </div>
            </div>
        </div>
        @endsection

@section('js')

<script>
    $(function(){
        $('.ui.dropdown').dropdown();
        
        var id='',action = 'create';        
        //创建
        $("#create").click(function(){
            action = 'create';
            $("#keyword").val('');
            $("#message").dropdown('clear');
            $("#type").val('text');
            $("#keywordbox").show();
            $('#panel').modal('show');
        });

        //更新
        $(".update").click(function(){
            id = $(this).attr('uid');
            keyword = $(this).attr('keyword');
            message = $(this).attr('message');
            action = 'update';
            $("#keyword").val(keyword);
            $('#message').dropdown('set selected',message)
            $('#panel').modal('show');
            if(id<9){
                $("#keywordbox").hide();
            }else{
                $("#keywordbox").show();
            }
        });

        $("#subbtn").click(function(){
            var message = $('#message').val(),
                keyword = $('#keyword').val(),
                type = $("#type").val();

            if(action == 'create'){
                if(message == '' || keyword == ''){
                    alert('请填写必要参数!');
                    return;
                }
                post(
                    "create",
                    {
                        message:message,
                        keyword:keyword,
                        type:type
                    },
                    function(data){
                        window.location.reload();
                    }
                );
            }else{
                var data = {};
                if(id<9){
                    data = {
                        id:id,
                        message:message
                    }
                }else{
                    if(message == '' || keyword == ''){
                        alert('请填写必要参数!');
                        return;
                    }
                    data = {
                        id:id,
                        message:message,
                        keyword:keyword
                    }
                }
                post(
                    "update",
                    data,
                    function(res){
                        window.location.reload();
                    }
                );
            }
        });

        //删除
        $(".delShow").click(function(){
            id = $(this).attr('uid');
            if(id<9){
                alert('不可删除!');
                return;
            }
            $('.del').modal('show');
        });

        $(".delBtn").click(function(){
            post(
                "delete",
                {
                    id:id
                },
                function(data){
                    window.location.reload();
                }
            );
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
    })
</script>
@endsection