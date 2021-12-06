@extends('share.base')
@section('css')
    <style>

        #menuBg{
            width: 300px;height: 50px;
            border-radius: 5px;
            margin: 20px 0;
            background: #777;
            display: flex;
            margin-top:200px;
        }

        #addMainmenu,.addSubmenu{
            background:salmon;
            flex-grow:1;
            border-radius:10px;
            text-align:center;
            line-height:50px;
            height:50px;
            cursor:pointer;
        }
        .yimenu{
            width: 100px;
            height: 50px;
            background: cornflowerblue;
            text-align: center;
            line-height: 50px;
            border-right:crimson 1px solid;
        }

        .menuitem .clean{
            display:none;
            width:20px;
            height:20px;
            border-radius:50%;
            position:absolute;
            z-index:20;
            right:5px;
            top:5px;
            cursor:pointer;
            text-align:center;
            background:sandybrown;
        }

        .menuitem,.ermenuitem{
            position: relative;
        }

        .ermenuBox{
            position: absolute;
            width: 100%;
            bottom: 50px;left: 0;
            background:darkkhaki;
            display:flex;
            flex-direction: column;
        }

        .ermenu{
            z-index: 100;
            background: wheat;
            width: 100%;            
            height: 50px;
            text-align: center;
            line-height: 50px;            
        }
        /* .selectPanel{
            width: 70%;
            float: left;
        }



        .card{
            float: left;
            margin: 20px!important;
        } */
    </style>
@endsection
@section('content')
<h2 class="ui header">菜单</h2>
    <div class="ui input"><input type="text" placeholder="测试微信号的openid" id='testid'></div> 
    <button class="ui primary basic button" id='test'>测试菜单</button>
    <h3 class="ui header">菜单列表</h3>
    <div class="clearfix">
    <?php
    //默认菜单
    if($data == []){
        print <<<EOT
        <div class="ui card">
        <div class="image">
            <img src="/img/menu.jpg">
        </div>
        <div class="content">
            <div class="header">默认菜单</div>
            <div class="meta">
                <span class="date">公众号默认菜单</span>
            </div>
        </div>
    </div>
EOT;
    }else{
        foreach($data as $val){
            print <<<EOT
            <div class="ui card">
                <div class="image mymenu">
                    <div class="flex-container">
EOT;
                        foreach($val->buttons as &$v){
                            if(count($v['sub_button']) == 0){
                                unset($v['sub_button']);
                                $str = '<div class="flex-item menu" data-html="';
                                $str .= "<div class='content'>";
                                foreach($v as $index =>$value){
                                    $str .= '<div>'.$index.':'.$value.'</div>';
                                }
                                $str .= '</div>">'.$v["name"].'</div>';
                            }else{
                                $str = '<div class="flex-item">'.$v["name"].'<div>';
                                foreach($v['sub_button'] as $subbtn){
                                    $str .= '<div class="submenu" data-html="';
                                    $str .= "<div class='content'>";
                                        unset($subbtn['sub_button']);
                                        foreach($subbtn as $i =>$j){
                                            $str .= '<div>'.$i.':'.$j.'</div>';
                                        }
                                    $str .= '</div>" data-position="right center">'.$subbtn["name"].'</div>';
                                }
                                $str .= '</div></div>';
                            }
                            echo $str;
                        }
print <<<EOT
                    </div>
                </div>
                <div class="content">
                    <div class="header">$val->name</div>    
EOT;

                if(!isset($val->matchRule)){
                    print <<<EOT
                    <div class="meta">
                        <span class="date">没有设置个性化菜单</span>
                    </div>
EOT;
                }else{
                    print <<<EOT
                    <div class="description">
                        <div>标签ID:{$val->matchRule->tag_id}</div>
                        <div>性别:{$val->matchRule->sex}</div>
                        <div>国家:{$val->matchRule->country}</div>
                        <div>省份:{$val->matchRule->province}</div>
                        <div>城市:{$val->matchRule->city}</div>
                        <div>客户端版本:{$val->matchRule->client_platform_type}</div>
                        <div>语言:{$val->matchRule->language}</div>
                    </div>
EOT;
                }
            print <<<EOT
            </div>
            <div class="extra content">
                <button class="ui primary basic button delete" id="{$val->id}">删除</button>
            </div>
        </div>
EOT;
        }
    } 
?>
    </div>
    <div class="clearfix" id="cMenuPanel">
        <h3>添加菜单</h3>
        <div id="menuBg">
            <div id="addMainmenu">添加菜单</div>
        </div>
        <button class="ui primary basic button" id='submitMenu'>提交菜单</button>
        <div class="ui basic modal" id='selectPanel'>
            <div class="header">添加菜单</div>
            <div class="content">
            <div class="ui input margin_bottom"><input type="text" placeholder="菜单名称" id='name' class="opt"></div> 
            <select class="ui dropdown" id='type'>
                <option value="">事件类型</option>
                <option value="click">点击事件</option>
                <option value="view">跳转网页</option>
                <option value="submenu">二级菜单</option>
                <option value="scancode_push">扫一扫</option>
                <option value="scancode_waitmsg">扫一扫并弹出提示框</option>
                <option value="pic_sysphoto">拍照</option>
                <option value="pic_photo_or_album">拍照或从相册选择</option>
                <option value="pic_weixin">从相册选择</option>
                <option value="location_select">位置选择器</option>
                <option value="miniprogram">跳转小程序</option>
            </select>
            <div id="append" style="margin:10px auto"></div>
            <div class="actions" style="margin-top:10px">
                <div class="ui green ok inverted button add">
                <i class="checkmark icon"></i>
                是
                </div>
                <div class="ui red basic cancel inverted button">
                <i class="remove icon"></i>
                否
                </div>
            </div>
        </div>
        </div>

        <div class="clearfix" style="margin-top:20px">
        <h2>菜单事件</h2>
        <button class="ui primary basic button" id='eventCreate'>创建事件</button>
        <table class="striped ui celled table">
        <thead>
            <tr>
                <th>事件类型</th>
                <th>关键词</th>
                <th>回复消息</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
<?php
foreach($eventList as &$val){
        switch ($val->eventtype) {
            case 'CLICK':
                $eventType = '点击事件';
                break;
            case 'VIEW':
                $eventType = '跳转网页';
                break;
            case 'scancode_push':
                $eventType = '扫一扫';
            break;
            case 'scancode_waitmsg':
                $eventType = '扫一扫并弹出提示框';
            break;
            case 'pic_sysphoto':
                $eventType = '拍照';
            break;
            case 'pic_photo_or_album':
                $eventType = '拍照或从照片选择';
            break;
            case 'pic_weixin':
                $eventType = '从相册选择';
            break;
            case 'location_select':
                $eventType = '位置选择器';
            break;
            case 'view_miniprogram':
                $eventType = '跳转小程序';
            break;
            default:
                $eventType = $val->eventtype;
                break;
        }
                print <<<EOT
                <tr>
                <td>{$eventType}</td>
                <td>{$val->keyword}</td>
                <td>{$val->name}</td>
                <td><button class="ui primary basic button eventUpdate" uid='{$val->id}' message='{$val->message}'>修改</a></button>
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
        <div class="clearfix" style="margin-top:20px">
        <h3>说明:</h3>
        <div>
            2、一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。<br/>
            3、创建菜单后,可以尝试取消关注公众账号后再次关注，则可以看到创建后的效果。<br/>
            4. 创建菜单之前,请先创建事件<br>
            6. 测试发现,扫一扫,上报位置和拍照功能不支持消息回复...可以用图片回复和位置消息回复替代

            1、click：点击事件,推送绑定的key事件；<br/>
            2、view：点击跳转URL,微信客户端将会打开绑定的网页URL<br/>
            3、scancode_push：扫码推事件<br/>
            4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框<br/>
            5、pic_sysphoto：弹出系统拍照发图<br/>
            6、pic_photo_or_album：弹出拍照或者相册发图<br/>
            7、pic_weixin：弹出微信相册发图器<br/>
            8、location_select：弹出地理位置选择器<br/>
            
            请注意，3到8的所有事件，仅支持微信iPhone5.4.1以上版本，和Android5.4以上版本的微信用户，旧版本微信用户点击后将没有回应，开发者也不能正常接收到事件推送。
        </div>
    </div>

        <div class="ui basic modal" id='panel'>
            <div class="header">个性化设置(可为空)</div>
            <div class="content">
                <div class="ui input"><input type="text" placeholder="标签ID" id='tag_id'></div>
                <select class="ui dropdown" id="sex">
                        <option value="">性别</option>
                        <option value="1">男</option>
                        <option value="2">女</option>
                </select>
                <div class="ui input"><input type="text" placeholder="国家" id='country'></div>
                <div class="ui input"><input type="text" placeholder="省份" id='province'></div>
                <div class="ui input"><input type="text" placeholder="城市" id='city'></div>
                <select class="ui dropdown" id="client_platform_type">
                        <option value="">客户端类型</option>
                        <option value="1">ios</option>
                        <option value="2">android</option>
                        <option value="3">其他</option>
                </select>
                <div class="ui input"><input type="text" placeholder="语言" id='language'></div>
            </div>
            <div class="actions">
                <div class="ui green ok inverted button subbtn">
                <i class="checkmark icon"></i>
                是
                </div>
                <div class="ui red basic cancel inverted button">
                <i class="remove icon"></i>
                否
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

        {{-- 更新 --}}
        <div class="ui basic modal" id="eventUpdatePanel">
            <div class="header">新建/修改事件</div>
            <div class="content">
                <div class="ui input" id="keywordbox"><input type="text" placeholder="关键词" id='keyword'></div>
                <select class="ui dropdown" id='eventType'>
                <option value="">事件类型</option>
                <option value="CLICK">点击事件</option>
                <option value="VIEW">跳转网页</option>
                <option value="scancode_push">扫一扫</option>
                <option value="scancode_waitmsg">扫一扫并弹出提示框</option>
                <option value="pic_sysphoto">拍照</option>
                <option value="pic_photo_or_album">拍照或从相册选择</option>
                <option value="pic_weixin">从相册选择</option>
                <option value="location_select">位置选择器</option>
                <option value="view_miniprogram">跳转小程序</option>
            </select>
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
                <div class="ui green ok inverted button" id='eventUpdateSub'>
                <i class="checkmark icon"></i>
                是
                </div>
            </div>
        </div>

        {{-- 删除 --}}
        <div class="ui basic modal eventDeletePanel">
            <div class="header">是否删除?</div>

            <div class="actions">
                <div class="ui red basic cancel inverted button">
                <i class="remove icon"></i>
                否
                </div>
                <div class="ui green ok inverted button eventDelete">
                <i class="checkmark icon"></i>
                是
                </div>
            </div>
        </div>
@endsection

@section('js')
<script>
    $(document).ready(function(){
        var menuType = '',menuArr = [],subindex = 0;
        
        init();

        $('#addMainmenu').click(function(){
            if(menuArr.length == 3){
                alert('一级菜单数量最大为3');
                return;
            }
            menuType = 'main';
            selectPanelShow();
        })
        $('#menuBg').on('click','.addSubmenu',function(event){
            subindex = $(this).parent().parent().index();
            if(menuArr[subindex].sub_button.length == 5){
                alert('二级菜单数量最大为5');
                return;
            }
            menuType = 'sub';
            selectPanelShow();
        })

        function selectPanelShow(){
            $('#name').val('');
            $('.dropdown').dropdown('clear');
            $('#append').empty();
            if(menuType == 'sub'){
                $('[data-value="submenu"]').hide();
            }else{
                $('[data-value="submenu"]').show();
            }
            $('#selectPanel').modal('show');
        }

        function createMainMenu(){
            if(optarr.type == 'submenu'){
                $("<div class='menuitem'><div class='yimenu' optarr='"+JSON.stringify(optarr)+"'>"+optarr.name+"</div><div class='ermenuBox'><div class='addSubmenu'>添加二级菜单</div></div><div class='clean'>x</div></div>").insertBefore("#addMainmenu");
            }else{
                $("<div class='menuitem'><div class='yimenu' optarr='"+JSON.stringify(optarr)+"'>"+optarr.name+"</div><div class='clean'>x</div></div>").insertBefore("#addMainmenu");
                console.log(menuArr);
            }
        }

        function createSubMenu(){
            $('.menuitem').eq(subindex).find(".addSubmenu").before("<div class='ermenuitem'><div class='ermenu' optarr='"+JSON.stringify(optarr)+"'>"+optarr.name+"</div><div class='clean'>x</div></div>")
            console.log(menuArr);
        }

        function init(){
            $('.dropdown').dropdown();

            $('.menu,.submenu').popup();
            
            $('.flex-item').hover(function(){
                $(this).children().toggle();
            })

            //添加菜单类型下拉框
            $('.item').click(function(){
                $('#append').empty();
                switch($(this).attr('data-value')){
                case 'submenu':
                break;
                case 'view':
                $('#append').append('<div class="ui input"><input type="text" placeholder="跳转网址" id="url" class="opt"></div>');
                break;
                case 'miniprogram':
                var data = '<div class="ui input margin_bottom"><input type="text" placeholder="小程序的appid" id="appid" class="opt"></div>\
                        <div class="ui input margin_bottom"><input type="text" placeholder="小程序的页面路径" id="pagepath" class="opt"></div>\
                        <div class="ui input margin_bottom"><input type="text" placeholder="不支持,将打开本url" id="url" class="opt"></div>'
                $('#append').append(data);
                break;
                default:
                var data = '<select class="ui dropdown myselect" id="key"><option value="">事件关键词</option>';
                var optionArr = <?php echo json_encode($event); ?>;
                optionArr.forEach((item) => {
                    if(item.eventtype.toLowerCase() == $(this).attr('data-value')){
                        data += '<option value="'+item.keyword+'">'+item.keyword+'</option>';
                    }
                });
                data += '</select>';
                    $('#append').append(data);
                    $('.myselect').dropdown();
                break;
                }
            })

            //新建按钮
            $('.add').click(function(){
                var flag = true;
                optarr = {};
                $('input[class="opt"]').each(function(){
                    if($(this).val() == ''){
                        flag = false;
                    }
                    optarr[$(this).attr('id')] = $(this).val();
                    if(optarr['url']){
                        optarr['url'] = optarr['url'];
                    }
                });
                optarr['type'] = $('#type').val();
                if(optarr['type'] != 'view' && optarr['type'] != 'submenu' && optarr['type'] != 'miniprogram'){
                    optarr['key'] = $('#key').val();
                    if(optarr['key'] == ''){
                        return;
                    }
                }
                if(!flag){
                    alert('请填写必要参数!');
                    return false;
                }
                if(optarr['type'] == 'submenu'){
                    optarr['sub_button'] = [];
                }
                if(menuType == 'main'){
                    console.log(optarr);
                    menuArr.push(optarr);
                    if(menuArr.length == 3){
                        $('#addMainmenu').hide();
                    }
                    createMainMenu();
                }else{
                    console.log(optarr);
                    menuArr[subindex].sub_button.push(optarr);
                    if(menuArr[subindex].sub_button.length == 5){
                        $('.addSubmenu').hide();
                    }
                    createSubMenu();
                }
            })

            $('#menuBg').on('mouseover mouseout','.yimenu,.ermenu',function(event){
                if(event.type == "mouseover"){
                    $("#tempmsg").remove();                
                    var obj = JSON.parse($(this).attr('optarr'));
                    var res = '<div class="ui popup top left transition visible" style="position:absolute;z-index:999;left:60%;top:50%" id="tempmsg"><div class="content">';
                    for(i in obj){
                        res += '<div>'+i+':'+obj[i]+'</div>';
                    }
                    res += '</div></div>';
                    $('body').append(res);
                }else if(event.type == "mouseout"){
                    $("#tempmsg").remove();
                    
                }
            })

            $('#menuBg').on('mouseover mouseout','.menuitem,.ermenuitem',function(event){
                if(event.type == "mouseover"){
                    $(this).children('.clean').show();
                }else if(event.type == "mouseout"){
                    $(this).children('.clean').hide();
                }
            })

            $('#menuBg').on('click','.clean',function(event){
                var index = $(this).parent().index();
                $(this).parent().remove();
                if(menuType == 'main'){
                    menuArr.splice(index,1);
                    if(menuArr.length < 3){
                        $('#addMainmenu').show();
                    }
                }else{
                    menuArr[subindex].sub_button.splice(index,1);
                    if(menuArr[subindex].sub_button.length < 5){
                        $('.addSubmenu').show();
                    }
                }
                console.log(menuArr);
            })

            $('#submitMenu').click(function(){
                $('#panel').modal('show');
            })
        }
                //ajax提交
                $(".subbtn").click(function(){
            var matchrule = {
                "tag_id":$('#tag_id').val(),
                "sex":$('#sex').val(),
                "country":$('#country').val(),
                "province":$('#province').val(),
                "city":$('#city').val(),
                "client_platform_type":$('#client_platform_type').val(),
                "language":$('#language').val()
            };
            if(menuArr == []){
                alert('新菜单不可为空!');
            }
            post("create",
                {
                    buttons:menuArr,
                    matchrule:matchrule
                },
                function(data){
                    window.location.reload();
                });
        });
    })



        //删除
        $(".delete").click(function(){
            id = $(this).attr('id');
            $('#deletePanel').modal('show');
        });

        $(".deletebtn").click(function(){
            $.post("delete",
                {
                    id:id
                },
                function(data,status){
                    window.location.reload();
                });
        });

        $("#test").click(function(){
            var userid = $("#testid").val();

            if(userid == ''){
                alert('请填写必要参数!');
                return false;
            }

            post("test",
                {
                    userid:userid
                },
                function(data){
                    alert(data);
                });
        })


        $("#eventCreate").click(function(){
            action = 'create';
            $("#keyword").val('');
            $('#message').dropdown('restore defaults');
            $('#eventType').dropdown('restore defaults');
            $("#keywordbox").show();
            $("#eventType").parent().show();
            $('#eventUpdatePanel').modal('show');
        });
        var eventId = 0;
        //更新
        $(".eventUpdate").click(function(){
            eventId = $(this).attr('uid');
            message = $(this).attr('message');
            $("#keywordbox").hide();
            $("#eventType").parent().hide();
            action = 'update';
            $('#message').dropdown('set selected',message)
            $('#eventUpdatePanel').modal('show');
        });

        //删除
        $(".delShow").click(function(){
            eventId = $(this).attr('uid');
            if(eventId<9){
                alert('不可删除!');
                return;
            }
            $('.eventDeletePanel').modal('show');
        });

        $(".eventDelete").click(function(){
            post(
                "/event/delete",
                {
                    id:eventId
                },
                function(data){
                    window.location.reload();
                }
            );
        });

        $("#eventUpdateSub").click(function(){
            var message = $('#message').val(),
                keyword = $('#keyword').val(),
                eventtype = $('#eventType').val();
            // if(message == '' || keyword == '' || eventtype == ''){
            //     alert('请填写必要参数!');
            //     return false;
            // }
            if(action == 'create'){
                post(
                    "/event/create",
                    {
                        message:message,
                        type:'event',
                        keyword:keyword,
                        eventtype:eventtype
                    },
                    function(data){
                        window.location.reload()
                    }
                );
            }else{
                post(
                    "/event/update",
                    {
                        id:eventId,
                        message:message
                    },
                    function(res){
                        window.location.reload();
                    }
                );
            }
        });

        function post(url,data,success){
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(data){
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
//     });
 </script>
@endsection