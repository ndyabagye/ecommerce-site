<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Session;
use Auth;
use Carbon\Carbon; 

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;

use KingFlamez\Rave\Facades\Rave as Flutterwave;

class FlutterWaveController extends Controller
{
    public function initialise(Request $request)
    {
        // Generate payment Reference
        $reference = Flutterwave::generateReference();

        // Capture payment details
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => request()->amount,
            'email' => request()->email,
            'tx_ref' => $reference,
            'currency' => "UGX",
            'redirect_url' => route('callback'),
            'customer' => [
                'email' => request()->email,
                "phone_number" => request()->phone,
                "name" => request()->name
            ],

            "customizations" => [
                "title" => 'Cart Payment',
                "description" => "Order"
            ]
        ];

        $payment = Flutterwave::initializePayment($data);

        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return 'Something went wrong';
        }

        Session::put('flutter_wave_order', $request->all());

        return redirect($payment['data']['link']);
    }

    public function callback()
    {
        $status = request()->status;

        //if payment is successful
        if ($status ==  'successful') {
        
        $transactionID = Flutterwave::getTransactionIDFromCallback();
        $data = Flutterwave::verifyTransaction($transactionID);

        if (Session::has('flutter_wave_order')) {
            $order_info = Session::get('flutter_wave_order');
            $order_id = Order::insertGetId([
                'user_id' => Auth::id(),
                'division_id' => $order_info['division_id'],
                'district_id' => $order_info['district_id'],
                'state_id' => $order_info['state_id'],
                'name' => $order_info['name'],
                'phone' => $order_info['phone'],
                'email' => $order_info['email'],
                'post_code' => $order_info['post_code'],
                'notes' => $order_info['notes'],
                'amount' => $order_info['amount'],
                'transaction_id' => $transactionID,
                'payment_type' => 'Flutter wave',
                'payment_method' => 'Flutter wave',
                'currency' =>  'Usd',
                'invoice_no' => 'EOS'.mt_rand(10000000,99999999),
                'order_date' => Carbon::now()->format('d F Y'),
                'order_month' => Carbon::now()->format('F'),
                'order_year' => Carbon::now()->format('Y'),
                'status' => 'pending',
                'created_at' => Carbon::now(),
            ]);

            $carts = Cart::content();

            foreach ($carts as $cart) {
                   OrderItem::insert([
                      'order_id' => $order_id, 
                      'product_id' => $cart->id,
                      'color' => $cart->options->color,
                      'size' => $cart->options->size,
                       'qty' => $cart->qty,
                       'price' => $cart->price,
                      'created_at' => Carbon::now(),
           
                   ]);
               }

               Cart::destroy();
               Session::forget('flutter_wave_order');

            try {
                $invoice = Order::findOrFail($order_id);
	            $data = [
			        'invoice_no' => $invoice->invoice_no,
		            'amount' => $invoice->amount,
	 		        'name' => $invoice->name,
	 		        'email' => $invoice->email,
                ];
                Mail::to($invoice->email)->send(new OrderMail($data));
            } catch (\Throwable $th) {
                return $th->getMessage();
            }

            $notification = array(
                'message' => 'Your Order Place Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('dashboard')->with($notification);
        }

        }
        elseif ($status ==  'cancelled'){
            //Put desired action/code after transaction has been cancelled here
            return 'Payment Cancelled';
        }
        else{
            //Put desired action/code after transaction has failed here
            return 'Payment Failed';
        }
    }
}
