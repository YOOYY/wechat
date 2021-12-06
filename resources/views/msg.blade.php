@extends('share.base')
@section('css')
    <link href="http://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/select.css" rel="stylesheet" type="text/css">
    <link href="/css/select-theme.css" rel="stylesheet" type="text/css">
    <style>
        table{
            word-break:break-all;
            word-wrap:break-word;
        }
        .contentContainer{
            max-height: 200px;
            overflow: hidden;
        }
        .select-mania{
            margin-top: 20px!important;
        }

        .select-mania-inner,.select-mania-group-text,.select-mania-item-text{
            color: black!important;
        }
        textarea{
            width: 900px;height: 370px;
            font-size: 18px!important;
            border-radius: 4px;
            padding:10px;
        }
        #append img{
            width: 200px;
            padding: 10px;
        }

        #append img:hover{
            opacity: 0.6;
        }

        .myactive{
            opacity: 0.6;
        }
    </style>
@endsection
@section('content')
<?php
    switch ($type) {
        case 'text':
            $table_header = '<th>名称</th><th>类型</th><th>内容</th><th>备注</th><th>操作</th>';
            break;
        case 'image':
            $table_header = '<th>名称</th><th>类型</th><th>素材名称</th><th>预览图</th><th>备注</th><th>操作</th>';
            break;
        case 'video':
            $table_header = '<th>名称</th><th>类型</th><th>标题</th><th>描述</th><th>素材名称</th><th>备注</th><th>操作</th>';
            break;
        case 'voice':
        case 'material':
            $table_header = '<th>名称</th><th>类型</th><th>素材名称</th><th>备注</th><th>操作</th>';
            break;
        case 'transfer':
            $table_header =  '<th>名称</th><th>类型</th><th>内容</th><th>备注</th><th>操作</th>';
            break;
        case 'news':
            $table_header =  '<th>名称</th><th>类型</th><th>标题</th><th>描述</th><th>图片链接</th><th>链接URL</th><th>备注</th><th>操作</th>';
            break;
        case 'mnews':
            $table_header =  '<th>名称</th><th>类型</th><th>第几篇</th><th>标题</th><th>描述</th><th>图片链接</th><th>链接URL</th><th>操作</th><th>备注</th>';
            break;
        default:
            $table_header = '<th>名称</th><th>类型</th><th>内容</th><th>备注</th><th>操作</th>';
            break;
    }
    $table_body = '';

    foreach($info as &$val){
        //临时素材
        if(isset($val->temporaryname)){
            $val->materialname = $val->temporaryname;
            $val->type = 'temp';
            $expires = ($val->created_at+259200)-time();
            $r = '';
            if($expires>=86400){
                $days=floor($expires/86400);
                $expires=$expires%86400;
                $r=$days.'天';
            }
            if($expires>=3600){
                $hours=floor($expires/3600);
                $expires=$expires%3600;
                $r.=$hours.'小时';
            }
            if($expires>=60){
                $minutes=floor($expires/60);
                $expires=$expires%60;
                $r.=$minutes.'分';
            }
            if($expires>0){
                $r.=$expires.'秒';
            }
            if($expires<=0){
                $r = '素材已失效';
            }
            $val->note = '失效时间:'.$r;
        }
        if(isset($val->state) && $val->state === 1){
            $val->note = '素材已冻结';
        }
        switch ($type) {
            case 'text':
                $table_body .= "<tr>
                                <td>{$val->name}</td>
                                <td>文本消息</td>
                                <td><div class='contentContainer'>{$val->content}</div></td>
                                <td>{$val->note}</td>
                                <td>
                                    <button class='ui primary basic button update' uid='{$val->id}' content='{$val->content}' note='{$val->note}' name='{$val->name}'>修改</a></button>
                                    <button class='ui primary basic button delete' uid='{$val->id}'>删除</a></button>
                                </td>
                            </tr>";
                break;
            case 'image':
                if(empty($val->temporaryname)){
                    if(!empty($val->materialpath)){
                        $val->pic = "<img src='/{$val->materialpath}' style='width:50px;height:50px;'>";
                    }else{
                        $val->pic = "<img src='{$val->pic}' class='transimg'>";
                    }
                }else{
                    if(!empty($val->temporarypath)){
                        $val->pic = "<img src='/{$val->temporarypath}' style='width:50px;height:50px;'>";
                    }else{
                        $val->pic = "";
                    }
                }
                $val->type = $val->type == 'temp'? '临时图片消息':'图片消息';
                if(isset($val->temporaryname) && empty($val->pic)){$val->pic = '';}
                $table_body .= "<tr>
                                <td>{$val->name}</td>
                                <td>{$val->type}</td>
                                <td>{$val->materialname}</td>
                                <td>{$val->pic}</td>
                                <td>{$val->note}</td>
                                <td>
                                <button class='ui primary basic button update' uid='{$val->id}' note='{$val->note}' name='{$val->name}'>修改</a></button>
                                <button class='ui primary basic button delete' uid='{$val->id}'>删除</a></button>
                            </td>
                        </tr>";
                break;
            case 'video':
                $val->type = $val->type == 'temp'? '临时视频消息':'视频消息';
                $table_body .= "<tr>
                    <td>{$val->name}</td>
                    <td>{$val->type}</td>
                    <td>{$val->title}</td>
                    <td>{$val->description}</td>
                    <td>{$val->materialname}</td>
                    <td>{$val->note}</td>
                    <td>
                    <button class='ui primary basic button update' uid='{$val->id}' note='{$val->note}' name='{$val->name}' description='{$val->description}' title='{$val->title}'>修改</a></button>
                    <button class='ui primary basic button delete' uid='{$val->id}'>删除</a></button>
                </td>
            </tr>";
                break;
            case 'voice':
                $val->type = $val->type == 'temp'? '临时音频消息':'音频消息';
                $table_body .= "<tr>
                                <td>{$val->name}</td>
                                <td>{$val->type}</td>
                                <td>{$val->materialname}</td>
                                <td>{$val->note}</td>
                                <td>
                                <button class='ui primary basic button update' uid='{$val->id}' note='{$val->note}' name='{$val->name}'>修改</a></button>
                                <button class='ui primary basic button delete' uid='{$val->id}'>删除</a></button>
                            </td>
                        </tr>";
                break;
            case 'material':
                $val->type = $val->type == 'temp'? '临时素材消息':'素材消息';
                $table_body .= "<tr>
                        <td>$val->name</td>
                        <td>{$val->type}</td>
                        <td>{$val->materialname}</td>
                        <td>{$val->note}</td>
                        <td>
                        <button class='ui primary basic button update' uid='{$val->id}' note='{$val->note}' name='{$val->name}'>修改</a></button>
                        <button class='ui primary basic button delete' uid='{$val->id}'>删除</a></button>
                    </td>
                </tr>";
                break;
            case 'transfer':
                if($val->content == ''){
                    $val->content = '不指定客服';
                }
                $table_body .= "<tr>
                                <td>$val->name</td>
                                <td>转发客服</td>
                                <td><div class='contentContainer'>{$val->content}</div></td>
                                <td>{$val->note}</td>
                                <td>
                                <button class='ui primary basic button update' uid='{$val->id}' note='{$val->note}' name='{$val->name}'>修改</a></button>
                                <button class='ui primary basic button delete' uid='{$val->id}'>删除</a></button>
                            </td>
                        </tr>";
                break;
            case 'news':
                $table_body .= "<tr>
                                <td>{$val->name}</td>
                                <td>图文消息</td>
                                <td>{$val->title}</td>
                                <td>{$val->description}</td>
                                <td>{$val->image}</td>
                                <td>{$val->url}</td>
                                <td>{$val->note}</td>
                                <td>
                                <button class='ui primary basic button update' uid='{$val->id}' note='{$val->note}' name='{$val->name}' description='{$val->description}' title='{$val->title}' image='{$val->image}' url='{$val->url}'>修改</a></button>
                                <button class='ui primary basic button delete' uid='{$val->id}'>删除</a></button>
                            </td>
                        </tr>";
                break;
            case 'mnews':
            // $val->content = array(array("title"=>11,"description"=>22,"image"=>33,"url"=>44),array("title"=>11,"description"=>22,"image"=>33,"url"=>44));
            // $val->content = json_encode($val->content);
            $newsArr = json_decode($val->content,true);
            $l = count($newsArr);
            foreach ($newsArr as $index => $v) {
                $i = $index+1;
                $table_body .= "<tr>";
                if($index == 0){
                    $table_body .= "<td rowspan='".$l."'>{$val->name}</td>
                    <td rowspan='".$l."'>多图文消息</td>";
                }
                $table_body .= "<td>{$i}</td>
                                <td>{$v['title']}</td>
                                <td>{$v['description']}</td>
                                <td>{$v['image']}</td>
                                <td>{$v['url']}</td>
                                <td>
                                <button class='ui primary basic button update' uid='{$val->id}' uindex='{$index}' newsArr='{$val->content}' name='{$val->name}' note='{$val->note}'>修改</a></button>
                                <button class='ui primary basic button deleteItem' uid='{$val->id}' uindex='{$index}' newsArr='{$val->content}'>删除</a></button>";
                if($i == $l){
                    $table_body .="<button class='ui primary basic button delete' uid='{$val->id}'>删除全部</a></button>";
                    if($i<8){
                        $table_body .="<button class='ui primary basic button' id='createItem' name='{$val->name}' note='{$val->note}' newsArr='{$val->content}' uid='{$val->id}'>新增</a></button>";
                    }
                }

                $table_body .="</td>";
                if($index == 0){
                    $table_body .= "<td rowspan='".$l."'>{$val->note}</td>";
                }
                $table_body .= "</tr>";
            }
                break;
            default:
                $table_body .= "<tr>
                            <td>{$val->name}</td>
                            <td>文本消息</td>
                            <td><div class='contentContainer'>{$val->content}</div></td>
                            <td>{$val->note}</td>
                            <td>
                            <button class='ui primary basic button update' uid='{$val->id}' content='{$val->content}' note='{$val->note}' name='{$val->name}'>修改</a></button>
                            <button class='ui primary basic button delete' uid='{$val->id}'>删除</a></button>
                        </td>
                    </tr>";
                break;
            }
    }
?>

    <h2 class="ui header">消息列表</h2>
    <div>
            <div class="ui input">
                <input type="text" placeholder="消息名称" id='searchName'>
            </div>
            <select class="ui dropdown" id="searchType">
                <option value="">消息类型</option>
                <option value="text">文本消息</option>
                <option value="image">图片消息</option>
                <option value="video">视频消息</option>
                <option value="voice">音频消息</option>
                <option value="news">图文消息</option>
                <option value="mnews">多图文消息</option>
                <option value="material">素材消息</option>
                <option value="transfer">客服消息</option>
            </select>
            <button class="ui primary basic button" id='search'>查询列表</button>
            <button class="ui primary basic button" id='create'>新增</button>
            <a class="ui primary basic button" href='edit'>文章内容编辑</a>
    </div>
    <table class="striped ui celled table">
        <thead><tr><?php echo $table_header; ?></tr></thead>
        <tbody><?php echo $table_body; ?></tbody>
        <tfoot>
            <tr><th colspan="19"><div class="zxf_pagediv"></div></th></tr>
        </tfoot>
    </table>

    <div class="ui basic modal" id='panel'>
        <div class="header">编辑回复消息</div>
        <div class="content">
                <div class="ui input"><input type="text" placeholder="消息名称" id="name" class="opt"></div>
                <div class="ui input"><input type="text" placeholder="备注" id="note" class="opt"></div>
                <div id="append" style="margin-top:20px"></div>
        </div>
        <div class="actions">
            <div class="ui red basic cancel inverted button">
            <i class="remove icon"></i>
            否
            </div>
            <div class="ui green ok inverted button subBtn">
            <i class="checkmark icon"></i>
            是
            </div>
        </div>
    </div>

    <div class="ui basic modal delPanel">
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
<script src="/js/select.js"></script>
<script>
    $(function(){
        var action='create',id=0,newsArr = [],index = 0;
        var type = '<?php echo $type;?>';
        $("#search").click(function(){
            var name = $('#searchName').val();
            var type = $('#searchType').val();
            window.location.href = 'lists?name='+name+'&type='+type;
        })

        $('#create').click(function(){
            action='create';
            $("#name").removeAttr("disabled");
            $("#note").removeAttr("disabled");
            reset();
            $('#panel').modal('show');
        })

        //图文消息用
        $('#createItem').click(function(){
            action='createItem';
            id=$(this).attr('uid');
            $('#name').val($(this).attr('name'));
            $('#note').val($(this).attr('note'));
            $('#name').attr('disabled','disabled');
            $('#note').attr('disabled','disabled');
            newsArr = JSON.parse($(this).attr('newsArr'));
            $('#panel').modal('show');
        })

        $('.delete').click(function(){
            id=$(this).attr('uid');
            $('.delPanel').modal('show');
        })

        //图文消息用
        $('.deleteItem').click(function(){
            action='deleteItem';
            id=$(this).attr('uid');
            newsArr = JSON.parse($(this).attr('newsArr'));
            index=$(this).attr('uindex');
            $('.delPanel').modal('show');
        })

        $('.delBtn').click(function(){
            if(action == 'deleteItem' && newsArr.length > 1){
                newsArr.splice(index,1);
                var arr = JSON.stringify(newsArr);
                post(
                    "update",
                    {
                        id:id,
                        type:type,
                        content:arr
                    },
                    function(data){
                        window.location.reload();
                    }
                );
            }else{
                post(
                    "delete",
                    {
                        id:id
                    },
                    function(data){
                        window.location.reload();
                    }
                );
            }
        })
        //初始化下拉框,必须前置
        $('.dropdown').dropdown();

        $('.update').click(function(){
            id=$(this).attr('uid');
            $('#name').val($(this).attr('name'));
            $('#note').val($(this).attr('note'));
            $("#name").removeAttr("disabled");
            $("#note").removeAttr("disabled");
            action='update';
            switch(type){
                case 'text':
                case 'transfer':
                    $('#content').val($(this).attr('content'));
                    break;
                case 'image':
                case 'voice':
                case 'material':
                    break;
                case 'video':
                    $('#title').val($(this).attr('title'));
                    $('#description').val($(this).attr('description'));
                    break;
                case 'news':
                    $('#title').val($(this).attr('title'));
                    $('#description').val($(this).attr('description'));
                    $('#thumb_url').val($(this).attr('image'));
                    $('#url').val($(this).attr('url'));
                    break;
                case 'mnews':
                    newsArr = JSON.parse($(this).attr('newsArr'));
                    index = $(this).attr('uindex');
                    var arr = newsArr[index];
                    $('#title').val(arr.title);
                    $('#description').val(arr.description);
                    $('#thumb_url').val(arr.image);
                    $('#url').val(arr.url);
                    break;
                default:
                    break;
            }
            $('#panel').modal('show');
        })

        $('#searchType').dropdown('set selected',type);
        switch(type){
                case 'text':
                    data = '<div class="ui input"><textarea placeholder="内容" id="content" class="opt"></textarea></div>';
                    $('#append').append(data);
                    break;
                case 'image':
                    data = '<input type="hidden" id="media_id" class="opt">\
                            <h3>永久素材</h3>';
                            //获取资源列表
                            post(
                                '/material/getlist',
                                {
                                    type:type
                                },
                                function(res){
                                    res['ever'].forEach((val,index,arrs)=>{
                                        var imgUrl = '';
                                        if(val.path){
                                            imgUrl = '/'+val.path;
                                        }else{
                                            imgUrl = val.url;
                                        }
                                        data += '<img src="'+ imgUrl +'" class="media_id" media_id ="'+val.media_id+'" title="'+ val.name +'" style="width:100px;height:100px;">';
                                    });
                                    data += '<h3>临时素材</h3>';
                                    res['temp'].forEach((val,index,arrs)=>{
                                        var imgUrl = '';
                                        if(val.path){
                                            imgUrl = '/'+val.path;
                                        }else{
                                            imgUrl = '';
                                        }
                                        data += '<img src="'+ imgUrl +'" class="media_id" media_id ="'+val.media_id+'" title="'+ val.name +'" style="width:100px;height:100px;">';
                                    });
                                    $('#append').append(data);
                                } 
                            )
                    break;
                case 'video':
                    data = '<div class="ui input"><input type="text" placeholder="标题" id="title" class="opt"></div>\
                            <div class="ui input"><input type="text" placeholder="描述" id="description" class="opt"></div>\
                            <select class="demo-1 opt" id="media_id">\
                                <optgroup label="永久素材">';
                                    post('/material/getlist',
                                        {
                                            type:'video'
                                        },
                                        function(res){
                                            res['ever'].forEach((val,index,arrs)=>{
                                                data += '<option value="'+val.media_id+'" title="'+val.title+'" description="'+val.description+'">'+val.name+'</option>';
                                            });
                                            data += '</optgroup>\
                                            <optgroup label="临时素材">';
                                                        res['temp'].forEach((val,index,arrs)=>{
                                                            data += '<option value="'+val.media_id+'" title="'+val.title+'" description="'+val.description+'" class="'+val.media_id+'">'+val.name+'</option>';
                                                        });
                                            data += '</optgroup>\
                                                    </select>';
                                            $('#append').append(data);
                                            $('.demo-1').selectMania({
                                                size: 'small', 
                                                themes: [], 
                                                placeholder: '选择视频',
                                                removable: true,
                                                search: true
                                            });
                                        //给添加元素绑定事件
                                        $("#append").on("click", ".select-mania-selected", function() {
                                            console.log($(this).attr('data-value'));
                                            $('#title').val($('.'+$(this).attr('data-value')).attr('title'));
                                            $('#description').val($('.select-mania-selected').attr('description'));
                                        });

                                    })
                    break;
                case 'voice':
                    data = '<select class="demo-1 opt" id="media_id">\
                                <optgroup label="永久素材">';
                                    post('/material/getlist',
                                        {
                                            type:'voice'
                                        },
                                        function(res){
                                            res['ever'].forEach((val,index,arrs)=>{
                                                data += '<option value="'+val.media_id+'" title="'+val.title+'" description="'+val.description+'">'+val.name+'</option>';
                                            });
                                data += '</optgroup>\
                                <optgroup label="临时素材">';
                                            res['temp'].forEach((val,index,arrs)=>{
                                                data += '<option value="'+val.media_id+'">'+val.name+'</option>';
                                            });
                                data += '</optgroup>\
                            </select>';
                            $('#append').append(data);
                            $('.demo-1').selectMania({
                                size: 'small', 
                                themes: [], 
                                placeholder: '选择音频',
                                removable: true,
                                search: true,
                            });
                                        })
                    break;
                case 'news':
                case 'mnews':
                    data = '<div class="ui input"><input type="text" placeholder="标题" id="title" class="opt"></div>\
                            <div class="ui input"><input type="text" placeholder="描述" id="description" class="opt"></div>\
                            <div class="ui input"><input type="text" placeholder="图片链接" id="thumb_url" class="opt"></div>\
                            <div class="ui input"><input type="text" placeholder="跳转URL" id="url" class="opt"></div>';
                            $('#append').append(data);                            
                    break;
                case 'material':
                    data = '<select class="demo-1 opt" id="media_id">\
                                <optgroup label="永久素材">';
                                    post('/material/getlist',
                                        {
                                        },
                                        function(res){
                                            res['ever'].forEach((val,index,arrs)=>{
                                                data += '<option value="'+val.media_id+'">'+val.name+'</option>';
                                            });
                                data += '</optgroup>\
                                <optgroup label="临时素材">';
                                            res['temp'].forEach((val,index,arrs)=>{
                                                data += '<option value="'+val.media_id+'">'+val.name+'</option>';
                                            });
                                data += '</optgroup>\
                            </select>';
                            $('#append').append(data);
                            $('.demo-1').selectMania({
                                size: 'small', 
                                themes: [], 
                                placeholder: '选择素材',
                                removable: true,
                                search: true,
                            });
                })
                    break;
                case 'transfer':
                    data = '<div class="ui input"><input type="text" placeholder="客服账号(可为空)" id="content"></div>';
                    $('#append').append(data);
                    break;
                default:
                    break;
            }
        $('.dropdown').dropdown();

        //给添加元素绑定事件
        $("#append").on("click", ".media_id", function() {
            $('.media_id').removeClass('myactive');
            $(this).addClass('myactive')
            $('#media_id').val($(this).attr('media_id'));
        });

        $("#append").on("click", ".select-mania-item", function() {
            var data = '<i class="ui teal tag label mynews" val='+$(this).attr('data-value')+'>'+$(this).text()+'</i>';
            $('#child').append(data);
        });

        $("#append").on("click", ".mynews", function() {
            $(this).remove();
        });

        $('.subBtn').click(function(){
            //获取post数据
            var data={};
            data['type'] = type;
            if(type == 'mnews' && action == 'create'){
                data['name'] = $('#name').val();
                data['note'] = $('#note').val();
                var content = [{
                    "title":$("#title").val(),
                    "description":$("#description").val(),
                    "image":$("#thumb_url").val(),
                    "url":$("#url").val()
                }];

                data['content'] = JSON.stringify(content);
            }else if(type == 'mnews' && action == 'createItem'){
                var item = {
                    "title":$("#title").val(),
                    "description":$("#description").val(),
                    "image":$("#thumb_url").val(),
                    "url":$("#url").val()
                };
                newsArr.push(item);
                data['content'] = JSON.stringify(newsArr);
            }else if(type == 'mnews' && action == 'update'){
                var item = {
                    "title":$("#title").val(),
                    "description":$("#description").val(),
                    "image":$("#thumb_url").val(),
                    "url":$("#url").val()
                };
                newsArr.splice(index,1,item);
                data['content'] = JSON.stringify(newsArr);
                data['name'] = $("#name").val();
                data['note'] = $("#note").val();
            }else{
                var flag = true;
                $('.opt').each(function(){
                    if($(this).val() == '' && $(this).attr('id') != 'note'){
                        flag = false;
                    }
                    data[$(this).attr('id')]=$(this).val();
                });
                if(!flag){
                    alert('缺少必要参数!');
                    return false;
                }
            }

            if(action == 'update' || action == 'createItem'){
                data['id']=id;
                url = 'update';
            }else{
                url = 'create';
            }
            post(
                url,
                data,
                function(data){
                    window.location.reload();
                }
            );
        })

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
                error:function(e){
                    alert('网络错误!');
                }
            });
        }
        function reset(){
            $('.opt').each(function(){
                $(this).val('');
            });
        }
    })
$(function() {
    window.WxImgTemp = '';
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
    $('.transimg').each(function(index, element) {
        showWxImg($(this))
    })
    window.WxImgTemp = '';
})
</script>
@endsection