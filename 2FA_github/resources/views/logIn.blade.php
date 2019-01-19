<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>LaravelTest</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .tfa_box {
            display: none;
        }
    </style>
</head>
<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
        <div class="top-right links">
            @auth
            <a href="{{ url('/home') }}">Home</a>
            @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
        @endif

        <div class="content">
            <div class="title m-b-md" id="testTittle">
                loginTest
            </div>

            {!! Form::open(['action' => 'FormController@login', 'method' => 'POST', 'name' => 'login_f', 'id' => 'login_f']) !!}
            <label>Email : </label>
            <input type="text" name="email" id="email" value="User1@123.tw"></br></br>
            <label>Password : </label>
            <input type="password" name="pass" value="0000"></br></br>
            <input type="button" name="send_login" id="send_login" value="SEND" onclick="login_Function()">
            {{--  {!! Form::close() !!} --}}

            {{-- {!! Form::open(['action' => 'FormController@tfalogin', 'method' => 'POST', 'name' => 'login_2fa', 'id' => 'login_2fa']) !!} --}}
            <div class="tfa_box" id="tfa_box">
                <img id="qr_img" src="" alt=""><br><br>
                <label id="qr_url"></label><br><br>
                <input type="text" name="check_tfa" id="check_tfa" value=""  placeholder="請輸入手機中的認證碼">
                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="secertkey" value="">
                <input type="button" name="send_tfa" id="send_tfa" value="SEND_2FA" onclick="otp_fun()">
            </div>
            {!! Form::close() !!}
            @php


            @endphp

        </div>
    </div>
</body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.24.4/dist/sweetalert2.all.min.js"></script>
<script>
    function login_Function() {
        var data = $('#login_f').serialize();
        $.ajax({
            type: "POST",
            cache:false,
            dataType: 'json',
            data: data,
            //contentType: "application/json",
            success: function (response) {
                console.log('response');
                //var email = getId("email").value;
                if (response.has_secert == 'N') {
                    getId('tfa_box').style.display="block";
                    getId("qr_img").src = response.qr_url;
                    getId("secertkey").value = response.secretkey;
                    getId("qr_url").innerHTML = '使用者不在白名單內，請使用兩階段驗證方式登入或聯絡管理員。';
                    console.log(response);
                } else if (response.has_secert == 'Y') {
                    getId('tfa_box').style.display="block";
                    //getId("qr_img").src = response.qr_url;
                    getId("secertkey").value = response.secretkey;
                    getId("qr_url").innerHTML = '請輸入手機驗證碼。';
                    console.log(response);
                }else{
                    swal({
                        title: response.status,
                    // text: response.message,
                    html: response.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#00DDAA',
                })
                }
                //var obj = jQuery.parseJSON(response);
                //console.log(response);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError);
            }
        });
    }

    function getId(id){
        var el = '';
        el = document.getElementById(id);
        return el;
    }

    function otp_fun(){
        var url = '/test/tfalogin';
        var tfa = getId("check_tfa").value;
        var token = getId("_token").value;
        var secretkey = getId("secertkey").value;
        var data = {secretKey : secretkey,
            otp : tfa,
            _token : token };
        //console.log(data);
        $.ajax({
            url: url,
            type: "POST",
            cache:false,
            dataType: 'json',
            data: data,
            //contentType: "application/json",
            success: function (response) {
                //var obj = jQuery.parseJSON(response);
                alert(response.message);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError);
            }
        });
    }
</script>


</html>
