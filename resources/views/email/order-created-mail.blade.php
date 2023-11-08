

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    </head>
    <body>
        <div class="row justify-content-center ">
            <div class="col-lg-8  my-2 px-5 py-2">
                {{-- logo --}}
                <div class="d-flex justify-content-center gap-3 my-2">
                    <img src="https://laravel.com/img/logomark.min.svg" alt="">
                    <img src="https://laravel.com/img/logotype.min.svg" alt="">
                </div>

                {{-- user name --}}
                <h5>Hello, {{$user->name}}</h5>

                {{-- intro text --}}
                <p>Thank you for your order from oraimo. Once your package ships we will send you a tracking number. You can check the status of your order by logging into your account. If you have questions about your order, you can email us at care.gh@oraimo.com.</p>

                <hr>

                <h3>Your order # {{$order->order_id}}</h3>

                {{-- <p>Placed on {{ $order->order_date->format('M j, Y, g:i:s A') }}</p> --}}
                <p>Placed on {{ Carbon\Carbon::parse($order->order_date)->format('M j, Y, g:i:s A') }}</p>


                <hr>

                {{-- Delivery info --}}
                <div class="row">
                    <div class="col">
                        <h4 class="fw-bold">Billing Info</h4>
                        <p>
                            <strong>Name</strong>  {{$order->user->name}} <br>
                            <strong>Address</strong>  {{$order->location}} <br>
                            <strong>Phone</strong>  {{$order->phone}} <br>
                            <strong>Alternate Phone</strong>  {{$order->alternate_phone}} <br>
                        </p>
                    </div>
                    <div class="col">
                        <h4 class="fw-bold">Delivery Info</h4>
                        <p>
                            <strong>Name</strong>  {{$order->user->name}} <br>
                            <strong>Address</strong>  {{$order->location}} <br>
                            <strong>Phone</strong>  {{$order->phone}} <br>
                            <strong>Alternate Phone</strong>  {{$order->alternate_phone}} <br>
                        </p>
                    </div>
                </div>

                <hr>

                {{--table  --}}
                <div class="card">
                    <div class="card-header fw-bold">Featured Products</div>
                    <div class="card-body  ">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                  <th scope="col">Item #</th>
                                  <th scope="col">Prodct name</th>
                                  <th scope="col">Quantity</th>
                                  <th scope="col">Price</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($order->order_items as $order_item)
                                    <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{$order_item->product->name}}</td>
                                    <td>{{$order_item->quantity}}</td>
                                    <td>{{$order_item->price}}</td>
                                    </tr>
                                @endforeach
                              </tbody>
                          </table>
                    </div>
                    <div class="card-footer">
                        <p>
                            Sub total - <strong> {{$order->total_amount}}</strong>  <br>
                            Delivery - <strong>0.00</strong>  <br>
                            Sub total - <strong class="text-success">{{$order->total_amount}}</strong>

                        </p>

                    </div>

                  </div>

                  <p>Sincerely</p>
                  <p>Laravel-play</p>
            </div>
        </div>

        <!-- Add Bootstrap JS script link (if needed) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    </body>
</html>
