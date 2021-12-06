<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{URL::asset('css/semantic.min.css')}}">    
    <style>
            body{
                background: url('/img/bg.jpg') no-repeat;
                background-size:cover;
		overflow:hidden;
            }
        form{
            margin:300px auto;
            width: 300px;
            padding: 20px;
            border: 1px solid #e3e3e3;
            border-radius: 3px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.05);
            box-shadow: inset 0 1px 1px rgba(0,0,0,0.05);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: rgba(255,255,255, 0.2);
        }

    </style>
</head>
<body>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul style="color:red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif
    <form class="ui form" method="POST" action="login">
            {!! csrf_field() !!}        
            <div class="field">
              <label>用户名</label>
              <input type="text" name="name" placeholder="用户名">
            </div>
            <div class="field">
              <label>密码</label>
              <input type="password" name="password" placeholder="密码">
            </div>
            {{-- <div class="field">
              <div class="ui checkbox">
                <input type="checkbox" tabindex="0" name="remember">
                <label>记住我</label>
              </div>
            </div> --}}
            <button class="ui button" type="submit">登录</button>
    </form>
    <script src="{{URL::asset('js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{URL::asset('js/semantic.min.js')}}"></script>
</body>
</html>
