<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel-play - Account Created</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>
<body>


    <div class="container">
        <div class="row justify-content-center mt-5 ">
            <div class="col col-lg-6 p-10 text-bg-light py-3 px-5">
                <div class="d-flex justify-content-center gap-3">
                    <img src="https://laravel.com/img/logomark.min.svg" alt="">
                    <img src="https://laravel.com/img/logotype.min.svg" alt="">
                </div>
                <h4 class="text-center fw-bold mt-3">Congrats! {{$user->name}},</h4>
                <p class="text-center">You have successful created an account account. Enjoy your day :)</p>
                <div class="d-grid gap-2">
                    <a class="btn btn-danger btn-lg">Go to Laravel play site</a>
                </div>
            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
</html>
