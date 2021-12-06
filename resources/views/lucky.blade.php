@extends('share.base')

@section('content')
            <h2 class="ui header">红包列表</h2>
            <div class="ui input">
                <input type="text" placeholder="订单号" id='searchName'>
            </div> 
            <button class="ui primary basic button" id='query'>查询红包</button>
            <button class="ui primary basic button" id='create'>新建红包</button>
            <table class="striped ui celled table">
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>发送方名称</th>
                        <th>种子用户</th>
                        <th>红包数量</th>
                        <th>红包总额</th>
                        <th>祝福语</th>
                        <th>活动名称</th>
                        <th>活动备注</th>
                        <th>红包类型</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($data as $val){
                    print <<<EOT
                <tr>
                    <td>{$val->id}</td>
                    <td>{$val->send_name}</td>
                    <td>{$val->re_openid}</td>
                    <td>{$val->total_num}</td>
                    <td>{$val->total_amount}</td>
                    <td>{$val->wishing}</td>
                    <td>{$val->act_name}</td>
                    <td>{$val->remark}</td>
                    <td>{$val->type}</td>
                </tr>
EOT;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr><th colspan="19"><div class="zxf_pagediv"></div></th></tr>                    
                </tfoot>
            </table>

            <div class="ui basic modal" id='luckypanel'>
                <div class="header">新建红包</div>
                <div class="content">
                    <div class="ui input"><input type="text" placeholder="发送者姓名" id='send_name'></div>
                    <div class="ui input"><input type="text" placeholder="种子openID" id='re_openid'></div>
                    <div class="ui input"><input type="text" placeholder="普通为1,裂变不小于3" id='total_num'></div>
                    <div class="ui input"><input type="text" placeholder="红包总金额(元)" id='total_amount'></div>
                    <div class="ui input"><input type="text" placeholder="祝福语" id='wishing'></div>
                    <div class="ui input"><input type="text" placeholder="活动名称" id='act_name'></div>
                    <div class="ui input"><input type="text" placeholder="活动备注" id='remark'></div>
                    <select class="ui dropdown" id="type">
                        <option value="">红包类型</option>
                        <option value="normal">普通红包</option>
                        <option value="group">裂变红包</option>
                    </select>
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
@endsection

@section('js')
<script>
    $(document).ready(function(){
        //新建
        var index = 0;
        $("#create").click(function(){
            $('#luckypanel').modal('show');
        });

        $(".createbtn").click(function(){
            var send_name = $('#send_name').val();
            var re_openid = $('#re_openid').val();
            var total_num = $('#total_num').val();
            var total_amount = ($('#total_amount').val())*100;
            var wishing = $('#wishing').val();
            var act_name = $('#act_name').val();
            var remark = $('#remark').val();
            var type = $('#type').val();
            $.post("/lucky/create",
                {
                    send_name:send_name,
                    re_openid:re_openid,
                    total_num:total_num,
                    total_amount:total_amount,
                    wishing:wishing,
                    act_name:act_name,
                    remark:remark,
                    type:type
                },
                function(data,status){
                    window.location.reload()
                });
        });

        $(".query").click(function(){
            var searchName = $('#searchName').val();
            $.post("/lucky/query",
                {
                    mchBillNo:searchName,
                },
                function(data,status){
                    alert(data);
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

        $('.ui.dropdown').dropdown();            
    });
</script>
@endsection