<!DOCTYPE html>
<html>
    <head>
        <title><?php echo env('TITLE','');?>微信后台</title>
        <link rel="stylesheet" href="{{URL::asset('css/semantic.min.css')}}">
        <link rel="stylesheet" href="{{URL::asset('css/base.css')}}">
    </head>
<body>
<form class="ui form" style="width:500px;margin:100px auto;">
    <div class="required field">
        <label>账号</label>
        <input type="text" id="playerid">
    </div>
    <div class="ui submit button" id='submit'>绑定</div>
</form>
<script src="{{URL::asset('js/jquery-3.3.1.min.js')}}"></script>
<script>
$(function(){
    $('#submit').click(function(){
        var playerid = $('#playerid').val();
        var url = '/sign/bind',data = {playerid:playerid};
        $.post(url,data,function(res){
            if(res.error == 0){
                location.href = '/sign';
            }else{
                // errmsg
                alert('绑定失败！'+res.msg);
                $('#playerid').value('')
            }
        })
    })
})
</script>
</body>
</html>