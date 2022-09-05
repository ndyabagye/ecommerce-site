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

                //Capture Order Details
                $order_id = Order::insertGetId([
                    'user_id' => Auth::id(),
                    'division_id' => $request->division_id,
                    'district_id' => $request->district_id,
                    'state_id' => $request->state_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'post_code' => $request->post_code,
                    'notes' => $request->notes,
           
                    'payment_type' => 'Flutter wave',
                    'payment_method' => 'Flutter wave',
                     
                    'currency' =>  'Usd',
                    'amount' => request()->amount,
                     
           
                    'invoice_no' => 'EOS'.mt_rand(10000000,99999999),
                    'order_date' => Carbon::now()->format('d F Y'),
                    'order_month' => Carbon::now()->format('F'),
                    'order_year' => Carbon::now()->format('Y'),
                    'status' => 'pending',
                    'created_at' => Carbon::now(),	 
           
                ]);

                Session::put('flutter_order_id', $order_id);

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
           
                if (Session::has('coupon')) {
                   Session::forget('coupon');
               }
           
               Cart::destroy();

        return redirect($payment['data']['link']);
    }

    public function callback()
    {
        $status = request()->status;

        //if payment is successful
        if ($status ==  'successful') {
        
        $transactionID = Flutterwave::getTransactionIDFromCallback();
        $data = Flutterwave::verifyTransaction($transactionID);

        if(Session::has('flutter_order_id')) {
            $flutter_order = Order::findOrFail(Session::get('flutter_order_id'));
            $flutter_order->update([
                'transaction_id' => $transactionID
            ]);
            Session::forget('flutter_order_id');
        }

      $notification = array(
        'message' => 'Your Order Place Successfully',
        'alert-type' => 'success'
      );

      return redirect()->route('dashboard')->with($notification);
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
