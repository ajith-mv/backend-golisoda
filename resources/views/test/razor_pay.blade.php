<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>How to Integrate Razorpay Payment Gateway in Laravel 9 - LaravelTuts.com</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <button id="rzp-button1">Pay with Razorpay</button>
    
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    
    <form name='razorpayform' action="{{ route('razorpay.payment') }}" method="POST">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
    </form>
    <script>
    // Checkout details as a json
    var options = @json($data);
    
    /**
    * The entire list of checkout fields is available at
    * https://docs.razorpay.com/docs/checkout-form#checkout-fields
    */
    options.handler = function (response){
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.razorpayform.submit();
    };
    
    // Boolean whether to show image inside a white frame. (default: true)
    options.theme.image_padding = false;
    
    var rzp = new Razorpay(options);
    
    document.getElementById('rzp-button1').onclick = function(e){
        rzp.open();
        e.preventDefault();
    }
    </script>
</body>
</html>