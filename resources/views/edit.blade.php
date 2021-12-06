@extends('share.base')
@section('css')
    <style>
        #editor{
            height: 80%;
            margin-top: 20px;
        }
    </style>
@endsection
@section('content')
<h2 class="ui header">编写文章</h2>
<button class="ui primary basic button" id='print'>生成代码</button>
<div id="editor">
    <p>欢迎使用 <b>wangEditor</b> 富文本编辑器</p>
</div>

<input type="text" readonly="readonly" unselectable="on" value="代码" id='mytxt' style="background: none;border: none;color: #dfc37a; overflow:scroll;width:100%;">
@endsection

@section('js')
    <!-- 注意， 只需要引用 JS，无需引用任何 CSS ！！！-->
    <script type="text/javascript" src="{{URL::asset('js/wangEditor.min.js')}}"></script>
    <script>
        var E = window.wangEditor;
        var editor = new E('#editor');
        // 或者 var editor = new E( document.getElementById('editor') )
        editor.create();
        $(function(){
            $('#print').click(function(){
                var val = editor.txt.html();
                var dd = $('#mytxt');
                dd.val(val);
                dd.select();
                document.execCommand("Copy");
                alert('生成成功!')
            });
        });
    </script>
@endsection