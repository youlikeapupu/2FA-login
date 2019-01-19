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
                formTest
            </div>

            {!! Form::open(['action' => 'FormController@send', 'method' => 'POST', 'name' => 'test', 'id' => 'test']) !!}
            {{--             <label>Username : </label>
            <input type="text" name="username" value="User1"></br></br> --}}
            <label>Email : </label>
            <input type="text" name="email" value="User1@123.tw"></br></br>
            <label>Password : </label>
            <input type="password" name="pass" value="0000"></br></br>
            <label>Country : </label>
            <input type="text" name="country" value="Taiwan"></br></br>
            <label>Phone : </label>
            <input type="text" name="phone" value="0954111111"></br></br>
            <label>Birthday : </label>
            <input type="text" name="birthday" value="1992-05-03"></br></br>
            <input type="button" name="send" id="send" value="SEND" onclick="myFunction()">
            {!! Form::close() !!}

            @php

            // Form::open(array('action' => 'FormController@send', 'method' => 'post', 'name' => 'test', 'id' => 'test'));

            @endphp

        </div>
    </div>
</body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.24.4/dist/sweetalert2.all.min.js"></script>
<script>
    function myFunction() {
        var data = $('#test').serialize();
        //console.log(data);
      //document.getElementById("testTittle").innerHTML = "Hello World";
      $.ajax({
        type: "POST",
        cache:false,
        dataType: 'json',
        data: data,
            // data:{
            //     email:email,
            //     pwd:pwd,
            //     apwd:apwd,
            //     country:country,
            //     phone:phone,
            //     bir:bir
            // },
            //contentType: "application/json",
            success: function (response) {
                //var obj = jQuery.parseJSON(response);
                console.log(response);
                swal({
                    title: response.status,
                    // text: response.message,
                    html: response.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#00DDAA',
                })
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError);
            }
        });
  }
</script>


</html>
