@extends('frontend.main_master')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>

@section('title')
Flutter Wave Payment
@endsection


<style>
    /**
 * The CSS shown here will not be introduced in the Quickstart guide, but shows
 * how you can use CSS to style your Element's container.
 */
.waveElement {
  box-sizing: border-box;
  height: 40px;
  padding: 10px 12px;
  border: 1px solid transparent;
  border-radius: 4px;
  background-color: white;
  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}
.waveElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}
.waveElement--invalid {
  border-color: #fa755a;
}
.waveElement--webkit-autofill {
  background-color: #fefde5 !important;}
</style>


<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<ul class="list-inline list-unstyled">
				<li><a href="home.html">Home</a></li>
				<li class='active'>Flutterwave</li>
			</ul>
		</div><!-- /.breadcrumb-inner -->
	</div><!-- /.container -->
</div><!-- /.breadcrumb --> 




<div class="body-content">
	<div class="container">
		<div class="checkout-box ">
			<div class="row">
				 




				<div class="col-md-6">
					<!-- checkout-progress-sidebar -->
<div class="checkout-progress-sidebar ">
	<div class="panel-group">
		<div class="panel panel-default">
			<div class="panel-heading">
		    	<h4 class="unicase-checkout-title">Your Shopping Amount </h4>
		    </div>
		    <div class="">
				<ul class="nav nav-checkout-progress list-unstyled">

					 
<hr>
		 <li>
		 	@if(Session::has('coupon'))

<strong>SubTotal: </strong> ${{ $grandTotal }} <hr>

<strong>Coupon Name : </strong> {{ session()->get('coupon')['coupon_name'] }}
( {{ session()->get('coupon')['coupon_discount'] }} % )
 <hr>

 <strong>Coupon Discount : </strong> ${{ session()->get('coupon')['discount_amount'] }} 
 <hr>

  <strong>Grand Total : </strong> ${{ session()->get('coupon')['total_amount'] }} 
 <hr>


		 	@else

<strong>SubTotal: </strong> ${{ $grandTotal }} <hr>

<strong>Grand Total : </strong> ${{ $grandTotal }} <hr>


		 	@endif 

		 </li>
					 
					

				</ul>		
			</div>
		</div>
	</div>
</div> 
<!-- checkout-progress-sidebar -->
 </div> <!--  // end col md 6 -->







	<div class="col-md-6">
					<!-- checkout-progress-sidebar -->
<div class="checkout-progress-sidebar ">
	<div class="panel-group">
		<div class="panel panel-default">
			<div class="panel-heading">
		    	<h4 class="unicase-checkout-title">Flutter wave Payment Method</h4>
		    </div>

<form action="{{ route('wave.pay') }}" method="post" id="payment-form">
                            @csrf
        <div class="form-row">
            <label for="card-element">
           
      <input type="hidden" name="name"  value="{{ $data['shipping_name'] }}">
      <input type="hidden" name="email"  value="{{ $data['shipping_email'] }}">
      <input type="hidden" name="phone"  value="{{ $data['shipping_phone'] }}">
      <input type="hidden" name="post_code"  value="{{ $data['post_code'] }}">
      <input type="hidden" name="division_id"  value="{{ $data['division_id'] }}">
      <input type="hidden" name="district_id"  value="{{ $data['district_id'] }}">
      <input type="hidden" name="state_id" value="{{ $data['state_id'] }}">
      <input type="hidden" name="notes"  value="{{ $data['notes'] }}">
      <input type="hidden" name="amount"  value="{{ $grandTotal }}"> 
            </label>
             
            <div id="card-element">
            <!-- A Wave Element will be inserted here. -->
            </div>
            <!-- Used to display form errors. -->
            <div id="card-errors" role="alert"></div>
        </div>
        <br>
        <button class="btn btn-primary" type="submit">Submit Payment</button>
        </form>
		    
 

		</div>
	</div>
</div> 
<!-- checkout-progress-sidebar -->
 </div><!--  // end col md 6 -->



 



</form>
			</div><!-- /.row -->
		</div><!-- /.checkout-box -->
		<!-- === ===== BRANDS CAROUSEL ==== ======== -->
 







<!-- ===== == BRANDS CAROUSEL : END === === -->	
</div><!-- /.container -->
</div><!-- /.body-content -->



  

 <script type="text/javascript">
</script>




@endsection