<!DOCTYPE html>
<html>
    <head>
        <title><?php echo env('TITLE','');?>微信后台</title>
        <link rel="stylesheet" href="{{URL::asset('css/semantic.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('css/base.css')}}">
        @yield('css')
    </head>
    <body>
        <div class="ui sidebar left vertical menu visible icon labeled thin">
            <a href='<?php echo $baseUrl;?>admin/lists' class="item"><i class="github alternate icon"></i>账户</a>
            <a href='<?php echo $baseUrl;?>user/lists' class="item"><i class="github alternate icon"></i>用户</a>
            <a href='<?php echo $baseUrl;?>usertag/lists' class="item"><i class="empire icon"></i>用户标签</a>
            <a href='<?php echo $baseUrl;?>material/lists' class="item"><i class="bookmark icon"></i>素材管理</a>
            <a href='<?php echo $baseUrl;?>menu/lists' class="item"><i class="dashboard icon"></i>菜单</a>
            <a href='<?php echo $baseUrl;?>staff/lists' class="item"><i class="smile icon"></i>客服</a>
            <a href='<?php echo $baseUrl;?>message/lists' class="item"><i class="comments icon"></i>消息管理</a>
            <a href='<?php echo $baseUrl;?>event/lists' class="item"><i class="tags icon"></i>回复消息管理</a>
        </div>
        <div class="pusher">
            @yield('content')
        </div>
        <script src="{{URL::asset('js/jquery-3.3.1.min.js')}}"></script>
        <script src="{{URL::asset('js/semantic.min.js')}}"></script>
		<script src="{{URL::asset('js/zxf_page.js')}}"></script>
        @yield('js')
    </body>
</html>
