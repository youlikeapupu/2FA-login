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
                JustTest<br>
                <img src="{{ $twoFa_Qr }}" alt="">
            </div>

            {!! Form::open(['action' => 'TestController@verify', 'method' => 'POST', 'name' => '2fa', 'id' => '2fa']) !!}
            <label>2FAcode : </label>
            <input type="text" name="vericode" value="1234"></br></br>
            <input type="hidden" name="secretKey" value="{{ $secretKey }}"></br></br>
            <input type="button" name="send_2FA" id="send_2FA" value="SEND" onclick="fa_function()">
            {!! Form::close() !!}

            @php

            @endphp

        </div>
    </div>
</body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.24.4/dist/sweetalert2.all.min.js"></script>
<script>
    function fa_function() {
        var data = $('#2fa').serialize();
        $.ajax({
            type: "POST",
            cache:false,
            dataType: 'json',
            data: data,
            success: function (response) {
                swal({
                    title: response.status,
                    html: response.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#00DDAA',
                })
                return false;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError);
            }
        });
    }
</script>


</html>
