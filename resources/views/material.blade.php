@extends('share.base')
@section('css')
    <style>
        table{
            word-break:break-all;
            word-wrap:break-word;
        }
        .contentContainer{
            max-height:300px;
            overflow:auto;
        }
        #content,#contentU{
            width:800px;
            height:400px;
            font-size:18px;
            border-radius:5px;
        }
    </style>
@endsection
@section('content')
    <h2 class="ui header">素材列表</h2>
    <div>
            <select class="ui dropdown" id="searchName">
                <option value="">素材类型</option>
                <option value="image">图片素材</option>
                <option value="video">视频素材</option>
                <option value="voice">声音素材</option>
                <option value="thumb">缩略图素材</option>
                <option value="article">图文素材</option>
                <option value="marticle">多图文素材</option>
                <option value="articleimage">文章内容图片</option>
            </select>
            <button class="ui primary basic button" id='search'>查询列表</button>
            <button class="ui primary basic button" id='uploadShow'>上传素材</button>
    </div>
    <h3>永久素材</h3>
    <p>提示: - 图片（image）: 1M，支持 bmp/png/jpeg/jpg/gif 格式<br>
            - 语音（voice）：2M，播放长度不超过 60s，支持 mp3/wma/wav/amr 格式<br>
            - 视频（video）：10MB，支持MP4格式<br>
            - 缩略图（thumb）：64KB，支持JPG格式<br>
            - 永久素材的数量是有上限的，请谨慎新增。图文消息素材和图片素材的上限为5000，其他类型为1000；
    </p>
    <table class="striped ui celled table">
        <thead>
            <tr>
                <?php
                    $table_header = '<th>名称</th><th>类型</th>';
                    switch ($type) {
                        case 'image':
                        case 'articleimage':
                            $table_header .= '<th>图片链接</th><th>预览图</th>';
                            break;
                        case 'thumb':
                            $table_header .= '<th>预览图</th>';
                        break;
                        case 'voice':
                            break;
                        case 'video':
                            $table_header .= '<th>标题</th><th>描述</th>';
                            break;
                        case 'article':
                            $table_header .=  '<th>标题</th><th>作者</th><th>文章摘要</th><th>内容</th>
                                            <th>文章来源URL</th><th>封面图</th><th>封面</th><th>评论</th><th>评论限制</th>';
                            break;
                        case 'marticle':
                            $table_header .=  '<th>第几篇</th><th>标题</th><th>作者</th><th>文章摘要</th><th>内容</th>
                                            <th>文章来源URL</th><th>封面图</th><th>封面</th><th>评论</th><th>评论限制</th><th>修改</th>';
                        default:
                            break;
                    }
                    $table_header .='<th>备注</th><th>启用时间</th><th>操作</th>';
                    echo $table_header;
                ?>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($info as $val){
        $update_time = isset($val->update_time)?date('Y-m-d H:s:i',$val->update_time):'';
        if($val->type == 'articleimage' || (empty($val->path) && ($val->type != 'article' && $val->type != 'marticle'))){
            $btn = '';
        }else{
            if($val->blocked == 0){
                $btn = '<button class="ui primary basic button blockedPanel" media_id="'.$val->media_id.'" timetype="material">冻结</button>';
            }else{
                $btn = '<button class="ui primary basic button unlock" uid="'.$val->id.'" timetype="material">启用</button>';
            }
        }
        if(isset($val->path)){
            $btntwo = "<a class='ui primary basic button' href='/{$val->path}' download='{$val->name}'>下载</a>";
        }else{
            $btntwo = '';
        }
        $table_body = "<tr>
                        <td>{$val->name}</td>";
        switch ($type) {
            case 'image':
                if($val->type == 'image'){
                    $ctype = '图片';
                }
            case 'articleimage':
                if($val->type == 'articleimage'){
                    $ctype = '文章图片';
                }

                if(!empty($val->path)){
                    $val->pic = "<img src='/{$val->path}' style='width:100px;height:100px;'>";
                }else{
                    $val->pic = "<img src='{$val->url}' class='transimg'>";
                }

                $table_body .= "<td>{$ctype}</td><td>{$val->url}</td><td style='text-align:center'>{$val->pic}</td>";
                break;
            case 'thumb':
                if(!empty($val->path)){
                    $val->pic = "<img src='/{$val->path}' style='width:100px;height:100px;'>";
                }else if(!empty($val->url)){
                    $val->pic = "<img src='{$val->url}' class='transimg'>";
                }else{
                    $val->pic = "";
                }
                $table_body .= "<td>缩略图</td><td style='text-align:center'>{$val->pic}</td>";
                break;
            case 'voice':
                $table_body .= "<td>音频</td>";
                break;
            case 'video':
                $table_body .="<td>视频</td><td>{$val->title}</td><td>{$val->description}</td>";
                break;
            case 'article':
                switch ($val->show_cover_pic) {
                    case 0:
                        $show_cover = '隐藏';
                        break;
                    case 1:
                        $show_cover = '显示';
                        break;
                    default:
                        $show_cover = $val->show_cover_pic;
                        break;
                }
                switch ($val->need_open_comment) {
                    case 0:
                        $need_open_comment = '关闭';
                        break;
                    case 1:
                        $need_open_comment = '打开';
                        break;
                    default:
                        $need_open_comment = $val->need_open_comment;
                        break;
                }
                switch ($val->only_fans_can_comment) {
                    case 0:
                        $only_fans_can_comment = '任意';
                        break;
                    case 1:
                        $only_fans_can_comment = '粉丝';
                        break;
                    default:
                        $only_fans_can_comment = $val->only_fans_can_comment;
                        break;
                }
                if(!empty($val->thumb_url)){
                    $thumb_url = "<img src='{$val->thumb_url}' class='transimg' alt='{$val->thumb_media_id}'>";
                }else{
                    $thumb_url = "";
                }
                $table_body .= "<td>文章消息</td>
                                <td>$val->title</td>
                                <td>$val->author</td>
                                <td>{$val->digest}</td>
                                <td><div class='contentContainer'>{$val->content}</div></td>
                                <td>{$val->content_source_url}</td>
                                <td>{$thumb_url}</td>
                                <td>{$show_cover}</td>
                                <td>{$need_open_comment}</td>
                                <td>{$only_fans_can_comment}</td>";
                break;
                case 'marticle':
                $articleArr = json_decode($val->content,true);
                // $articleArr = array(array('title'=>'111','author'=>'222','digest'=>'333','thumb_media_id'=>'444','show_cover_pic'=>'0',
                //             'content'=>'555','content_source_url'=>'666','need_open_comment'=>'0','only_fans_can_comment'=>0,'thumb_url'=>'777'),
                //             array('title'=>'111','author'=>'222','digest'=>'333','thumb_media_id'=>'444','show_cover_pic'=>'0',
                //             'content'=>'555','content_source_url'=>'666','need_open_comment'=>'0','only_fans_can_comment'=>0,'thumb_url'=>'777'));
                $l = count($articleArr);
                $table_body = '';
                foreach ($articleArr as $index => $v) {
                    switch ($v['show_cover_pic']) {
                        case 0:
                            $show_cover = '隐藏';
                            break;
                        case 1:
                            $show_cover = '显示';
                            break;
                        default:
                            $show_cover = $v['show_cover_pic'];
                            break;
                    }
                    switch ($v['need_open_comment']) {
                        case 0:
                            $need_open_comment = '关闭';
                            break;
                        case 1:
                            $need_open_comment = '打开';
                            break;
                        default:
                            $need_open_comment = $v['need_open_comment'];
                            break;
                    }
                    switch ($v['only_fans_can_comment']) {
                        case 0:
                            $only_fans_can_comment = '任意';
                            break;
                        case 1:
                            $only_fans_can_comment = '粉丝';
                            break;
                        default:
                            $only_fans_can_comment = $v['only_fans_can_comment'];
                            break;
                    }
                    if(!empty($v['thumb_url'])){
                        $thumb_url = "<img src='{$v['thumb_url']}' class='transimg' alt='{$val->thumb_media_id}'>";
                    }else{
                        $thumb_url = "";
                    }
                    $i = $index+1;
                    $table_body .= "<tr>";
                    if($index == 0){
                        $table_body .= "<td rowspan='".$l."'>{$val->name}</td>
                        <td rowspan='".$l."'>多图文素材</td>";
                    }
                    $table_body .= "<td>{$i}</td>
                                    <td>{$v['title']}</td>
                                    <td>{$v['author']}</td>
                                    <td>{$v['digest']}</td>
                                    <td><div class='contentContainer'>{$v['content']}</div></td>
                                    <td>{$v['content_source_url']}</td>
                                    <td>{$thumb_url}</td>
                                    <td>{$show_cover}</td>
                                    <td>{$need_open_comment}</td>
                                    <td>{$only_fans_can_comment}</td>
                                    <td>
                                    <button class='ui primary basic button updateShow' type='{$val->type}' id='{$val->id}' uindex='{$index}' articleArr='{$val->content}' name='{$val->name}' note='{$val->note}'>修改</a></button>";
                    if($val->blocked === 1){
                        $table_body .="<button class='ui primary basic button deleteItem' uid='{$val->id}' uindex='{$index}' articleArr='{$val->content}' media_id='{$val->media_id}'>删除</a></button>";
                    }
                    if($i == $l && $val->blocked === 1){
                        $table_body .="<button class='ui primary basic button createItem' name='{$val->name}' note='{$val->note}' articleArr='{$val->content}' uid='{$val->id}'>新增</a></button>";
                    }
    
                    $table_body .="</td>";
                    if($index == 0){
                        $table_body .= "<td rowspan='".$l."'>{$val->note}</td>
                        <td rowspan='".$l."'>{$update_time}</td>
                        <td rowspan='".$l."'>
                        {$btn}
                        <button class='ui primary basic button delete' media_id='{$val->media_id}' path='{$val->path}' timetype='material' id='{$val->id}' type='{$val->type}'>删除</button></td>";
                    }
                    $table_body .= "</tr>";
                }
                break;
            default:
                break;
        }
        if($type != 'marticle'){
            $table_body .= "<td>{$val->note}</td>
            <td>{$update_time}</td>
            <td>
                            {$btn}
                            <button class='ui primary basic button updateShow' type='{$val->type}' id='{$val->id}' media_id='{$val->media_id}' timetype='material'
                            title='{$val->title}' name='{$val->name}' author='{$val->author}' digest='{$val->digest}' content_source_url='{$val->content_source_url}'
                            content='{$val->content}' show_cover='{$val->show_cover_pic}' thumb_media_id='{$val->thumb_media_id}' note='{$val->note}'>修改</button>
                            <button class='ui primary basic button delete' media_id='{$val->media_id}' path='{$val->path}' timetype='material' id='{$val->id}' type='{$val->type}'>删除</button>";
            $table_body .= $btntwo;
            $table_body .="</td>
                    </tr>";
        }

        echo $table_body;
    }
?>
                </tbody>
                <tfoot>
                    <tr><th colspan="19"><div id="zxf_pagediv" class="zxf_pagediv"></div></th></tr>
                </tfoot>
            </table>

<div id="temppanel">

    <h3>临时素材</h3>
    <table class="striped ui celled table">
        <thead>
            <tr>
            <?php
                    $table_header = '<th>名称</th><th>类型</th>';
                    switch ($type) {
                        case 'image':
                        case 'thumb':
                            $table_header .= '<th>预览图</th>';
                            break;
                        case 'voice':
                            break;
                        case 'video':
                            $table_header .= '<th>标题</th><th>描述</th>';
                            break;
                        default:
                            break;
                    }
                    $table_header .='<th>备注</th><th>启用时间</th><th>失效时间</th><th>操作</th>';
                    echo $table_header;
                ?>
            </tr>
        </thead>
        <tbody>
<?php
    foreach($tlists as $val){

        if(isset($val->path)){
            $btn2 = "<a class='ui primary basic button' href='/{$val->path}' download='{$val->name}'>下载</a>";
        }else{
            $btn2 = '';
        }

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
        $expires = $r;
        $table_body = "<tr>
        <td>{$val->name}</td>";
        switch ($type) {
            case 'image':
                if($val->type == 'image'){
                    $ctype = '图片';
                }
            case 'thumb':
                if($val->type == 'thumb'){
                    $ctype = '缩略图';
                }
                if(!empty($val->path)){
                    $val->pic = "<img src='/{$val->path}' style='width:100px;height:100px;'>";
                }else{
                    $val->pic = "";
                }
                $table_body .= "<td>{$ctype}</td><td style='text-align:center'>{$val->pic}</td>";
                break;
            case 'voice':
                $table_body .="<td>音频</td>";
                break;
            case 'video':
                $table_body .="<td>视频</td><td>{$val->title}</td><td>{$val->description}</td>";
                break;
            default:
                break;
        }
        $table_body .= "<td>{$val->note}</td>
            <td>{$val->update_time}</td>
            <td>$expires</td>
            <td>";
            if($expires == '素材已失效' && !empty($val->path)){
                $table_body .= '<button class="ui primary basic button unlock" timetype="temporary" uid="'.$val->id.'">重新启用</a>';
            };
                $table_body .= "<button class='ui primary basic button updateShow' timetype='temporary' type='{$val->type}' id='{$val->id}' media_id='{$val->media_id}' name='{$val->name}' note='{$val->note}'>修改</button>
                <button class='ui primary basic button delete' media_id='{$val->media_id}' path='{$val->path}' timetype='temporary' id='{$val->id}' type='{$val->type}'>删除</button>";
            $table_body .= $btn2;
            $table_body .= "</td>
        </tr>";
        echo $table_body;
    }
?>
                </tbody>
                <tfoot>
                    <tr><th colspan="19"><div id="tzxf_pagediv" class="zxf_pagediv"></div></th></tr>
                </tfoot>
            </table>
</div>
        <div class="ui basic modal" id='uploadPanel'>
            <div class="header">编辑素材</div>
            <div class="content">
                    <div class="ui input"><input type="text" placeholder="素材名称" id='nameContent'></div>
                    <div class="ui input"><input type="text" placeholder="备注" id='noteContent'></div>

                    <select class="ui dropdown" id="typeContent">
                            <option value="">消息类型</option>
                            <option value="image">图片素材</option>
                            <option value="video">视频素材</option>
                            <option value="voice">声音素材</option>
                            <option value="thumb">缩略图素材</option>
                            <option value="article">图文素材</option>
                            <option value="marticle">多图文素材</option>
                            <option value="articleimage">文章内容图片</option>
                    </select>
                    <select class="ui dropdown" id="timetypeContent">
                            <option value="">素材类型</option>
                            <option value="temporary">临时素材</option>
                            <option value="material">永久素材</option>
                    </select>
                    <div id="articlePanel" style="display:none;margin-top:20px">
                        <div class="ui input"><input type="text" placeholder="标题" id="title" class="opt"></div>
                        <div class="ui input"><input type="text" placeholder="作者" id="author" class="opt"></div>
                        <div class="ui input"><input type="text" placeholder="单图文摘要，多图文为空" id="digest" class="opt"></div>
                        <div class="ui input"><input type="text" placeholder="阅读原文跳转URL" id="content_source_url" class="opt" style="margin-bottom:20px"></div>
                        <select class="ui dropdown opt" id="show_cover_pic">
                            <option value="1">显示封面</option>
                            <option value="0">隐藏封面</option>
                        </select>
                        <select class="ui dropdown opt" id="only_fans_can_comment" disabled>
                            <option value="0">所有人可评论</option>
                            <option value="1">粉丝才可评论</option>
                        </select>
                        <select class="ui dropdown opt" id="need_open_comment" disabled>
                            <option value="0">关闭评论</option>
                            <option value="1">打开评论</option>
                        </select>
                        <select class="ui dropdown opt" id="thumb_media_id">
                            <option value="">无缩略图</option>
                            <?php 
                                foreach($articleThumb as $val){
                                    echo '<option value="'.$val->media_id.'" thumb_url="'.$val->url.'">'.$val->name.'</option>';
                                }
                            ?>
                        </select>
                        <textarea placeholder="内容" id="content" class="opt ui input" style="margin-top:20px"></textarea>
                    </div>

                    <div class="ui primary button" id="uploadContent">
                        <div id='upload'>选择素材</div>
                    </div>
                    <br>
                    <div class="ui input" style="display:none;margin-top:20px;" id='titleContent'><input type="text" placeholder="视频标题" id='title'></div>
                    <div class="ui input" style="display:none;margin-top:20px;" id='descriptionContent'><input type="text" placeholder="视频描述" id='description'></div>

                    <form action="upload" method="post" enctype="multipart/form-data" style='display:none' id="myForm">
                        <input type="file" name="file" id="file">
                        <input type="hidden" name="timetype">
                        <input type="hidden" name="name">
                        <input type="hidden" name="type">
                        <input type="hidden" name="title">
                        <input type="hidden" name="note">
                        <input type="hidden" name="description">
                    </form>
            </div>

            <div class="actions">
                <div class="ui red basic cancel inverted button">
                <i class="remove icon"></i>
                否
                </div>
                <div class="ui green ok inverted button uploadBtn">
                <i class="checkmark icon"></i>
                是
                </div>
            </div>
        </div>

        <div class="ui basic modal" id='updatePanel'>
                <div class="header">修改素材</div>
                <div class="content">
                    <div class="ui input"><input type="text" placeholder="素材名" id='name'></div>
                    <div class="ui input"><input type="text" placeholder="备注" id='note'></div>

                    <div id="articleUpdatePanel" style="display:none;margin-top:20px">
                        <div class="ui input"><input type="text" placeholder="标题" id="titleU" class="opt"></div>
                        <div class="ui input"><input type="text" placeholder="作者" id="authorU" class="opt"></div>
                        <div class="ui input"><input type="text" placeholder="单图文摘要，多图文为空" id="digestU" class="opt"></div>
                        <div class="ui input"><input type="text" placeholder="阅读原文跳转URL" id="content_source_urlU" class="opt" style="margin-bottom:20px"></div>
                        <select class="ui dropdown opt" id="show_cover_picU">
                            <option value="1">显示封面</option>
                            <option value="0">隐藏封面</option>
                        </select>
                        <select class="ui dropdown opt" id="only_fans_can_commentU" disabled>
                            <option value="0">所有人可评论</option>
                            <option value="1">粉丝才可评论</option>
                        </select>
                        <select class="ui dropdown opt" id="need_open_commentU" disabled>
                            <option value="0">关闭评论</option>
                            <option value="1">打开评论</option>
                        </select>
                        <select class="ui dropdown opt" id="thumb_media_idU">
                            <option value="">无缩略图</option>
                            <?php 
                                foreach($articleThumb as $val){
                                    echo '<option value="'.$val->media_id.'" thumb_url="'.$val->url.'">'.$val->name.'</option>';
                                }
                            ?>
                        </select>
                        <textarea placeholder="内容" id="contentU" class="opt ui input" style="margin-top:20px"></textarea>
                    </div>
                </div>
    
                <div class="actions">
                    <div class="ui red basic cancel inverted button">
                    <i class="remove icon"></i>
                    否
                    </div>
                    <div class="ui green ok inverted button updateBtn">
                    <i class="checkmark icon"></i>
                    是
                    </div>
                </div>
        </div>

        <div class="ui basic modal" id='delPanel'>
            <div class="header">是否删除?</div>
            <div class="actions">
                <div class="ui red basic cancel inverted button">
                <i class="remove icon"></i>
                否
                </div>
                <div class="ui green ok inverted button delbtn">
                <i class="checkmark icon"></i>
                是
                </div>
            </div>
        </div>

        <div class="ui basic modal" id='blockPanel'>
            <div class="header">是否冻结?这将使应用在图文素材和消息中的本素材失效!</div>
            <div class="actions">
                <div class="ui red basic cancel inverted button">
                <i class="remove icon"></i>
                否
                </div>
                <div class="ui green ok inverted button blocked">
                <i class="checkmark icon"></i>
                是
                </div>
            </div>
        </div>
        @endsection

@section('js')

<script>
    $(function(){
        var type='',path='',media_id=0,timetype='',id = 0,uploading = false,atricle_id = '',action = '';
        var currentType = '<?php echo $type; ?>';
        if(currentType == 'articleimage' || currentType == 'article' || currentType == 'marticle'){
            $('#temppanel').hide();
        }
        //查询列表
        $('#search').click(function(){
            type = $('#searchName').val();
            var url = "lists?type=" + type + "&start=1";
            window.location.href=url;
        })

        //上传
        $('#uploadShow').click(function(){
            $('#uploadPanel').modal('show');
        })

        $("#upload").click(function(){
            $("#file").trigger("click");
        })

        $('#file').on('change',function(){
            var str = $(this).val();
            str = str.slice(str.lastIndexOf('\\')+1);
            $('#upload').text(str);
        })

        $('#typeContent').on('change',function(){
            if($(this).val()=='video'){
                $('#articlePanel').hide();
                $('#titleContent').show();
                $('#descriptionContent').show();
                $("[data-value='temporary']").show();
            }else if($(this).val()=='article' || $(this).val()=='marticle'){
                $('#articlePanel').show();
                $('#uploadContent').hide();
                $('#titleContent').hide();
                $('#descriptionContent').hide();
                $("[data-value='temporary']").hide();
            }else if($(this).val()=='articleimage'){
                $('#articlePanel').hide();
                $("[data-value='temporary']").hide();
            }else{
                $('#articlePanel').hide();
                $('#uploadContent').show();                
                $('#titleContent').hide();
                $('#descriptionContent').hide();
                $("[data-value='temporary']").show();               
            }
        })

        $('.uploadBtn').click(function(){
            var timetype = $('#timetypeContent').val(),name = $('#nameContent').val(),type = $('#typeContent').val(),note = $('#noteContent').val(),file = $('#file').val();
            if(timetype == '' || name == '' || type == ''){
                alert('缺少必要参数!');
                return;
            }
            if(file == '' && type != 'article' && type != "marticle"){
                alert('缺少必要参数!');
                return;
            }
            if(type != 'article' && type != "marticle"){
                $("[name='timetype']").val(timetype);
                $("[name='name']").val(name);
                $("[name='type']").val(type);
                $("[name='note']").val(note);
                $("[name='title']").val($('#title').val());
                $("[name='description']").val($('#description').val());
                if(uploading){
                    alert("文件正在上传中，请稍候");
                    return false;
                }
                $.ajax({
                    url: "upload",
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
                        } else {
                            alert("上传出错!");
                        }
                        window.location.reload();
                        uploading = false;
                    }
                });
            }else{
                var title = $('#title').val(),
                    thumb_media_id = $('#thumb_media_id').val(),
                    content = $('#content').val()
                    data = {};
                if(title =='' || thumb_media_id == '' || content == ''){
                    alert('请填写必要参数!');
                    return false;
                }
                if(type == 'article'){
                    data = {
                        "name":name,
                        "type":type,
                        "title": title,
                        "thumb_media_id": thumb_media_id,
                        "author": $('#author').val(),
                        "digest": $('#digest').val(),
                        "show_cover_pic": $('#show_cover_pic').val(),
                        "thumb_url":$("[value='"+thumb_media_id+"']").attr('thumb_url'),
                        "content": content,
                        "content_source_url": $('#content_source_url').val(),
                        "need_open_comment":$('#need_open_comment').val(),
                        "only_fans_can_comment":$('#only_fans_can_comment').val(),
                        "note":$('#note').val()
                    };
                }else{
                    data = {
                        "name":name,
                        "type":type,
                        "content":JSON.stringify([{
                            "title": title,
                            "thumb_media_id": thumb_media_id,
                            "author": $('#author').val(),
                            "digest": $('#digest').val(),
                            "show_cover_pic": $('#show_cover_pic').val(),
                            "content": content,
                            "thumb_url":$("[value='"+thumb_media_id+"']").attr('thumb_url'),
                            "content_source_url": $('#content_source_url').val(),
                            "need_open_comment":$('#need_open_comment').val(),
                            "only_fans_can_comment":$('#only_fans_can_comment').val(),
                        }]),
                        "blocked":1,
                        "note":$('#note').val()
                    }
                }

                post(
                    "upload",
                    data,
                    function(){
                        window.location.reload();
                    }
                )
            }
        })
        
        //修改素材
        $('.updateShow').click(function(){
            action = 'update';
            uindex = 0;
            type = $(this).attr('type');
            id = $(this).attr('id');
            $("#note").val($(this).attr('note'));
            $("#name").val($(this).attr('name'));
            $("#name").removeAttr("disabled");
            $("#note").removeAttr("disabled");
            timetype = $(this).attr('timetype');
            media_id = $(this).attr('media_id');
            if(type == 'article'){
                $('#name').val($(this).attr('name'));
                $('#titleU').val($(this).attr('title'));
                $('#authorU').val($(this).attr('author'));
                $('#digestU').val($(this).attr('digest'));
                $('#content_source_urlU').val($(this).attr('content_source_url'));
                $('#contentU').val($(this).attr('content'));
                $('#show_coverU').dropdown('set selected', $(this).attr('show_cover'));
                $('#thumb_media_idU').dropdown('set selected', $(this).attr('thumb_media_id'));
                $('#articleUpdatePanel').show();
            }else if(type == 'marticle'){
                uindex = $(this).attr('uindex'),
                article = JSON.parse($(this).attr('articleArr'))[uindex];
                $('#name').val($(this).attr('name'));
                $('#note').val($(this).attr('note'));
                $('#titleU').val(article.title);
                $('#authorU').val(article.author);
                $('#digestU').val(article.digest);
                $('#content_source_urlU').val(article.content_source_url);
                $('#show_cover_picU').dropdown('set selected',article.show_cover_pic);
                $('#only_fans_can_commentU').dropdown('set selected',article.only_fans_can_comment);
                $('#need_open_commentU').dropdown('set selected',article.need_open_comment);
                $('#thumb_media_idU').dropdown('set selected',article.thumb_media_id);
                $('#contentU').val(article.content);
                $('#articleUpdatePanel').show();
            }else{
                $('#articleUpdatePanel').hide();
            }
            $('#updatePanel').modal('show');
        })

        $('.updateBtn').click(function(){
            var name = $('#name').val();
            var note = $('#note').val();
            if(name == ''){
                alert('请填写必要参数!');
                return;
            }
            if(action === 'createItem'){
                var thumb_media_id = $('#thumb_media_idU').val();
                var obj = {
                    "title": $('#titleU').val(),
                    "thumb_media_id": thumb_media_id,
                    "author": $('#authorU').val(),
                    "digest": $('#digestU').val(),
                    "show_cover_pic": $('#show_cover_picU').val(),
                    "content": $('#contentU').val(),
                    "content_source_url": $('#content_source_urlU').val(),
                    "need_open_comment":$('#need_open_commentU').val(),
                    "only_fans_can_comment":$('#only_fans_can_commentU').val(),
                    "thumb_url":$("[value='"+thumb_media_id+"']").attr('thumb_url')
                };
                articlecontent.push(obj);
                contenta = JSON.stringify(articlecontent);
                data = {
                    id:id,
                    content:contenta
                }
                post(
                    "updatearticle",
                    data,
                    function(data){
                        window.location.reload();
                    }
                );
                return true;
            }
            var data = {
                type:type,
                timetype:timetype,                    
                id:id,
                note:note,
                name:name,                    
                media_id:media_id
            }
            if((type == 'article' || type == 'marticle') && action !== 'createItem'){
                var title = $('#titleU').val(),
                    thumb_media_id = $('#thumb_media_idU').val(),
                    content = $('#contentU').val();
                if(title =='' || thumb_media_id == '' || content == ''){
                    alert('请填写必要参数!');
                    return false;
                }
                data = {
                    "type":type,
                    "id":id,
                    "note":note,
                    "name":name,      
                    "media_id":media_id,
                    "title": title,
                    "thumb_media_id": thumb_media_id,
                    "author": $('#authorU').val(),
                    "digest": $('#digestU').val(),
                    "show_cover_pic": $('#show_cover_picU').val(),
                    "content": content,
                    "content_source_url": $('#content_source_urlU').val(),
                    "need_open_comment":$('#need_open_commentU').val(),
                    "only_fans_can_comment":$('#only_fans_can_commentU').val(),
                    "thumb_url":$("[value='"+thumb_media_id+"']").attr('thumb_url'),
                    "index":uindex
                }
            }
            post(
                "update",
                data,
                function(data){
                    window.location.reload();
                }
            );
        })

        //更新文章素材
        $('.createItem').click(function(){
            action = 'createItem';
            articlecontent = JSON.parse($(this).attr('articleArr'));
            id = $(this).attr('uid');
            $('#name').val($(this).attr('name'));
            $('#note').val($(this).attr('note'));
            $('#name').attr('disabled','disabled');
            $('#note').attr('disabled','disabled');
            $('#titleU').val('');
            $('#authorU').val('');
            $('#digestU').val('');
            $('#content_source_urlU').val('');
            $('#show_cover_picU').dropdown('set selected',0);
            $('#only_fans_can_commentU').dropdown('set selected',0);
            $('#need_open_commentU').dropdown('set selected',0);
            $('#thumb_media_idU').dropdown('set selected','');
            $('#contentU').val('');
            $('#articleUpdatePanel').show();
            $('#updatePanel').modal('show');
        })

        //删除素材
        $('.deleteItem').click(function(){
            action = 'deleteItem';
            content = JSON.parse($(this).attr('articleArr'));
            uindex = $(this).attr('uindex');
            timetype = 'material';
            id = $(this).attr('uid');
            media_id = '';
            type = 'marticle';
            path = '';
            content.splice(uindex,1);
            $('#delPanel').modal('show');
        })

        //删除素材
        $('.delete').click(function(){
            action = 'delete';
            media_id = $(this).attr('media_id');
            path = $(this).attr('path');
            timetype = $(this).attr('timetype');
            type = $(this).attr('type');
            id = $(this).attr('id');
            $('#delPanel').modal('show');
        })

        $('.delbtn').click(function(){
            if(action === 'deleteItem' && content.length > 1){
                content = JSON.stringify(content);                
                post(
                    "updatearticle",
                    {
                        content:content,
                        id : id
                    },
                    function(data){
                        window.location.reload();
                    }
                );
            }else{
                post(
                    "delete",
                    {
                        media_id:media_id,
                        path:path,
                        timetype:timetype,
                        type : type,
                        id : id
                    },
                    function(data){
                        window.location.reload();
                    }
                );
            }
        })

        //重新启用素材
        $('.unlock').click(function(){
            id = $(this).attr('uid'),
            timetype = $(this).attr('timetype');
            post(
                "unlock",
                {
                    id:id,
                    timetype:timetype
                },
                function(data){
                    window.location.reload();
                }
            );
        })

        //冻结素材
        $('.blockedPanel').click(function(){
            media_id = $(this).attr('media_id');
            $('#blockPanel').modal('show');
        })

        $('.blocked').click(function(){
            post(
                "blocked",
                {
                    media_id:media_id
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
        $('#searchName').dropdown('set selected','<?php echo $type;?>');

        //翻页
        $("#zxf_pagediv").createPage({
            pageNum: <?php echo $page;?>,
            current: <?php echo $current;?>,
            backfun: function(e) {
                //type = GetQueryString(type);
                // var tstart = $('#tzxf_pagediv .current').text();
                // window.location.href="lists?start="+e.current+"&tstart="+tstart+"&type="+type;
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

        //翻页
        $("#tzxf_pagediv").createPage({
            pageNum: <?php echo $tpage;?>,
            current: <?php echo $tcurrent;?>,
            backfun: function(e) {
                // type = GetQueryString(type);
                // var start = $('#zxf_pagediv .current').text();
                // window.location.href="lists?tstart="+e.current+"&start="+start+"&type="+type;
                tstart = e.current;
                var url = window.location.href;
                if(url.search('[?]')==-1){
                    url = url + '?tstart=' + tstart;
                }else if(url.search('tstart=')==-1){
                    url = url + '&tstart=' + tstart;
                }else{
                    url = url.replace(/tstart=[^&]*&?/,'tstart='+tstart+'&');
                }
                window.location.href=url;
            }
        });

        function GetQueryString(name)
        {
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if(r!=null)return  unescape(r[2]); return '';
        }
    })
    $(function() {
    window.WxImgTemp = '';
    var showWxImg = function(jQele) { //jQele 为 jquery 对象
        var url = jQele.attr('src'),
            time = new Date().getTime(),
            frameid = 'wxImg_' + time;
        window.WxImgTemp = '<img id="img" style="width:85px" height="85px" src=\'' + url + '?' + time + '\' />\
                <script>window.onload = function() {\
                parent.document.getElementById(\'' + frameid + "').height = document.getElementById('img').height+'px';}<//script>";
        var iframe = '<iframe id="' + frameid + '" \
                              src="javascript:parent.WxImgTemp;" \
                              frameBorder="0" scrolling="no" width="100px" height="100px" style="padding:0;margin:0"></iframe>';
        jQele.after(iframe).remove()
    };
    $('.transimg').each(function(index, element) {
        showWxImg($(this))
    })
    window.WxImgTemp = '';
})
</script>
@endsection