<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $dishes = Dish::orderBy('id','desc')->get();
        $tables = Table::all();

        $rawstatus = config('res.order_status');
        $status = array_flip($rawstatus);
        // dd($status);
        $orders = Order::where('status',4)->get();
        return view('order_form',compact('dishes','tables','orders','status'));
    }


    public function submit(Request $request)
    {
        //dd(array_filter($request->except('_token'))); // array filter => array value null ဖြစ်တာတွေကို remove လုပ်ပေး။ // except('_token') => remove form token key

        $data = array_filter($request->except('_token','table'));
        $order_id = rand();

        foreach($data as $key=>$value){
            if($value > 1){ // qty ၂ ဆို database ထဲ ၂ ကြောင်း၀◌င်။
                for($i = 0; $i < $value; $i++){
                    $this->saveOrder($order_id,$key,$request);
                }
            }else{
                $this->saveOrder($order_id,$key,$request);
            }
        }
        return redirect('/')->with('message','Order Submitted');
    }

    public function saveOrder($order_id,$dish_id,$request){
        $order = new Order();
        $order->order_id = $order_id;
        $order->dish_id = $dish_id;
        $order->table_id = $request->table;
        $order->status = config('res.order_status.new');// config/res.php

        $order->save();
    }

    public function serve(Order $order){
        $order->status = config('res.order_status.done');
        $order->save();
        return redirect('/')->with('message','Order serve to customer.');
    }

}
