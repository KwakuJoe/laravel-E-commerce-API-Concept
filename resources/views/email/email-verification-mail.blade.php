<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Verification</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>
<body>

    {{-- <div class="row justify-content-center py-20">
        <div class="col-6 p-10">
            <h4 class="text-center fw-bold">Congrats! {{$user['name']}},</h4>
            <p>You have successful created an account. Enjoy your day</p>
        </div>
    </div> --}}

    <div class="container">
        <div class="row justify-content-center mt-5 ">
            <div class="col col-lg-6 p-10 text-bg-light py-3 px-5">
                <div class="d-flex justify-content-center gap-3">
                <img src="https://laravel.com/img/logomark.min.svg" alt="">
                <img src="https://laravel.com/img/logotype.min.svg" alt="">
                </div>
                <h4 class="text-center fw-bold mt-3">{{$user->name}}!, Verify your email address</h4>
                <p class="text-center">confirm that you want to use this as your Sellty account
                    etnail address. Once it's done you will able to start Playing!</p>
                <div class="d-grid gap-2">
                    <a  href="{{$url}}"  class="btn btn-danger btn-lg">verify Email</a>
                </div>

                <hr>
            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</head>
<body>

</body>
</html>
