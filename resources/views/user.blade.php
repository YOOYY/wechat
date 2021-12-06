@extends('share.base')

@section('content')
    <h2 class="ui header">用户列表</h2>
    <div>
            <select class="ui dropdown" id="tagId">
                <option value="">粉丝标签名</option>
                <?php
                    foreach($tags as $id => $name){
                        echo '<option value="'.$id.'">'.$name.'</option>';
                    }
                ?>
            </select>
            <div class="ui input">
                <input type="text" placeholder="用户昵称" id='searchName'>
            </div> 
            <button class="ui primary basic button" id='userlist'>查询列表</button>
            <button class="ui primary basic button" id='batchtag'>批量打标签</button>
            <button class="ui primary basic button" id='batchUntag'>批量取消标签</button>
            <button class="ui primary basic button" id='addblack'>加入黑名单</button>
            <button class="ui primary basic button" id='removeblack'>移除黑名单</button>
    </div>
    <table class="striped ui celled table">
        <thead>
            <tr>
                <th></th>
                <th>openid</th>
                <th>昵称</th>
                <th>性别</th>
                <th>城市</th>
                <th>国家</th>
                <th>省份</th>
                <th>语言</th>
                <th>用户头像</th>
                <th>最后关注时间</th>
                <th>unionid</th>
                <th>备注</th>
                <th>分组ID</th>
                <th>标签ID</th>
                <th>关注来源</th>
                <th>场景</th>
                <th>描述</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($info as &$val){
        $val->subscribe_time = date('Y-m-d',$val->subscribe_time);
        switch ($val->subscribe_scene) {
            case 'ADD_SCENE_SEARCH':
                $subscribe_scene = '公众号搜索';
                break;
            case 'ADD_SCENE_ACCOUNT_MIGRATION':
                $subscribe_scene = '公众号迁移';
                break;
            case 'ADD_SCENE_PROFILE_CARD':
            $subscribe_scene = '名片分享';
            break;
            case 'ADD_SCENE_QR_CODE':
            $subscribe_scene = '扫描二维码';
            break;
            case 'ADD_SCENEPROFILE LINK':
            $subscribe_scene = '图文页内名称点击';
            break;
            case 'ADD_SCENE_PROFILE_ITEM':
            $subscribe_scene = '图文页右上角菜单';
            break;
            case 'ADD_SCENE_PAID':
            $subscribe_scene = '支付后关注';
            break;
            case 'ADD_SCENE_OTHERS':
            $subscribe_scene = '其他';
            break;
            default:
                $subscribe_scene = '未知';
                break;
        }
        switch ($val->groupid) {
            case '':
                $groupid = '';
                break;
            case 0:
                $groupid = '未分组';
                break;
            case 1:
                $groupid = '黑名单';
                break;
            case 2:
                $groupid = '星标组';
                break;
            default:
                $groupid = array_search($val->groupid,$tags);
                break;
        }
        switch ($val->sex) {
            case 1:
                $sex = '男';
                break;
            case 2:
                $sex = '女';
                break;
            default:
                $sex = '未知';
                break;
        }
        switch ($val->language) {
            case 'zh_CN':
                $language = '简体中文';
                break;
            default:
                $language = $val->language;
                break;
        }
                print <<<EOT
                <tr>
                <td>
                    <div class="ui checkbox">
                    <input type="checkbox" name="info" value='{$val->openid}'>
                    <label></label>
                    </div>
                </td>
                <td>{$val->openid}</td>
                <td>{$val->nickname}</td>
                <td>$sex</td>
                <td>{$val->city}</td>
                <td>{$val->country}</td>
                <td>{$val->province}</td>
                <td>$language</td>
                <td><img src="{$val->headimgurl}" style='width:36px;height:36px'></td>
                <td>{$val->subscribe_time}</td>
                <td></td>
                <td>{$val->remark}</td>
                <td>{$groupid}</td>
                <td>
EOT;
$res = explode(',',$val->tagid_list);
$tag = array_flip($res);
$res = array_intersect_key($tags,$tag);

foreach($res as $index => $v){
    print <<<EOT
    <a class="ui orange label">{$v}</a>
EOT;
}
print <<<EOT
                </td>
                <td>$subscribe_scene</td>
                <td>{$val->qr_scene}</td>
                <td>{$val->qr_scene_str}</td>
                <td><button class="ui primary basic button remarkShow" openid={$val->openid}>修改备注</button></td>
                </tr>
EOT;
    }
?>
                </tbody>
                <tfoot>
                    <tr><th colspan="19"><div class="zxf_pagediv"></div></th></tr>
                </tfoot>
            </table>

        <div class="ui basic modal">
            <div class="header">修改备注</div>
            <div class="content">
            <div class="ui input">
                <input type="text" id='remarkContent' placeholder="备注...">
            </div>
            </div>
            <div class="actions">
                <div class="ui red basic cancel inverted button">
                <i class="remove icon"></i>
                否
                </div>
                <div class="ui green ok inverted button remarkBtn">
                <i class="checkmark icon"></i>
                是
                </div>
            </div>
        </div>
        @endsection

@section('js')

<script>
    $(function(){
        var openId = '';
        //修改备注
        $(".remarkShow").click(function(){
            openId = $(this).attr('openid');
            $('.ui.basic.modal').modal('show');
        });

        $(".remarkBtn").click(function(){
            remark = $('#remarkContent').val();
            
            post(
                "remark",
                {
                    openId:openId,
                    remark:remark
                },
                function(data){
                    window.location.reload();
                }
            );
        });

        //查询列表
        $("#userlist").click(function(){
            var tagid = $('#tagId').val();
            var name = $('#searchName').val();
            window.location.href = 'lists?tagid='+tagid+'&name='+name;
        })

        //批量打标签
        $("#batchtag").click(function(){
            var tagId = $('#tagId').val();
            if(tagId == ''){
                alert('标签名不能为空');
                return;
            }
            var openIds = checkArr();
            if(openIds.length==0){
                alert('你还没有选择任何内容!');
                return;
            }

            post(
                "batchtag",
                {
                    tagId:tagId,
                    openIds:openIds
                },
                function(data){
                    window.location.reload();
                }
            );
        })

        //批量取消标签
        $("#batchUntag").click(function(){
            var tagId = $('#tagId').val();
            if(tagId == ''){
                alert('标签名不能为空');
                return;
            }

            var openIds = checkArr();
            if(openIds.length==0){
                alert('你还没有选择任何内容!');
                return;
            }

            post(
                "batchuntag",
                {
                    tagId:tagId,
                    openIds:openIds
                },
                function(data){
                    window.location.reload();
                }
            );
        })

        //批量拉黑
        $("#addblack").click(function(){
            var openIds = checkArr();
            if(openIds.length==0){
                alert('你还没有选择任何内容!');
                return;
            }

            post(
                "addblack",
                {
                    openIds:openIds
                },
                function(data){
                    window.location.reload();
                }
            );
        })

        //批量拉白
        $("#removeblack").click(function(){
            var openIds = checkArr();
            if(openIds.length==0){
                alert('你还没有选择任何内容!');
                return;
            }

            post(
                "removeblack",
                {
                    openIds:openIds
                },
                function(data){
                    window.location.reload();
                }
            );
        })

        function post(url,data,success){
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(res){
                    if(res.errcode == 0){
                        success(res.errmsg);
                    }else{
                        alert(res.errmsg);
                    }
                },
                error:function(){
                    alert('网络错误!');
                }
            });
        }

        //翻页
        $(".zxf_pagediv").createPage({
            pageNum: <?php echo $page;?>,
            current: <?php echo $current;?>,
            backfun: function(e) {
                start = e.current;
                var url = window.location.href;
                if(url.search('[?]')==-1){
                    url = url + '?start=' + start;
                }else if(url.search('start=')==-1){
                    url = url + '&start=' + start;
                }else{
                    url = url.replace(/start=[^&]*&?/,'start='+start+'&');
                }
                window.location.href=url;
            }
        });

        //获取复选框数组
        function checkArr(){
            var checkArr = [];  
            $('input[name="info"]:checked').each(function(){    
                checkArr.push($(this).val());  
            });
            return checkArr;
        }

        $('.ui.dropdown').dropdown();
    })
</script>
@endsection