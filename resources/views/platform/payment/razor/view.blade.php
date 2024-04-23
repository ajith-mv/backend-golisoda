<div>Please wait while your order is being processed...</div>
            
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<form name='razorpayform' action="{{ route('razorpay.payment.store') }}" method="POST">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
</form>

<script>
    // Checkout details as a json
    var options =  {!! json_encode($data) !!};
    
    options.handler = function (response){
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.razorpayform.submit();
    };
    // Boolean whether to show image inside a white frame. (default: true)
    options.theme.image_padding = false;

    options.modal = {
        ondismiss: function() {
            console.log("This code runs when the popup is closed");
        
            location.href="{{ route('fail.page') }}";
        },
        
        escape: true,
        
        backdropclose: false
    };

    var rzp = new Razorpay(options);

    (function() {
    // your page initialization code here
    // the DOM will be available here
    rzp.open();
        e.preventDefault();
    })();
    document.getElementById('rzp-button1').onclick = function(e){
        rzp.open();
        e.preventDefault();
    }
</script>