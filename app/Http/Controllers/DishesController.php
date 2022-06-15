<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Requests\DishCreateRequest;

class DishesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dishes = Dish::all();
        return view('kitchen.dish',compact('dishes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('kitchen.dish_create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DishCreateRequest $request)
    {
        $dish = new Dish();// use when not same with column name in db and name in form
        $dish->name = $request->name;
        $dish->category_id = $request->category;

        //upload image
        $imageName = date('YmdHis') . "." . request()->dish_image->getClientOriginalExtension(); // date.png
        request()->dish_image->move(public_path('images'), $imageName);

        $dish->image = $imageName;
        $dish->save();

        return redirect('/dish')->with('message','Dish Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Dish $dish)
    {
        $categories = Category::all();
        return view('kitchen.dish_edit',compact('dish','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dish $dish) // route model binding
    {
        request()->validate([
            'name' => 'required',
            'category' => 'required'
        ]);
        $dish->name = $request->name;
        $dish->category_id = $request->category;

        if($request->dish_image){
            // delete image by id
            $image_path = public_path("images/".$dish->image);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            // update image
            $imageName = date('YmdHis') . "." . request()->dish_image->getClientOriginalExtension(); // date.png
            request()->dish_image->move(public_path('images'), $imageName);
            $dish->image = $imageName;
        }
        $dish->save();
        return redirect('/dish')->with('message','Dish Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dish $dish)
    {
        $image_path = public_path("images/".$dish->image);
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        $dish->delete();
        return redirect('/dish')->with('message','Dish deleted Successfully.');
    }
    public function order(){
        $rawstatus = config('res.order_status');
        $status = array_flip($rawstatus);
        // dd($status);

        $orders = Order::whereIn('status',[1,2])->get();
        return view('kitchen.order',compact('orders','status'));
    }

    public function approve(Order $order){
        $order->status = config('res.order_status.processing');
        $order->save();
        return redirect('order')->with('message','Order Approved');
    }
    public function cancel(Order $order){
        $order->status = config('res.order_status.cancel');
        $order->save();
        return redirect('order')->with('message','Order Canceled');
    }
    public function ready(Order $order){
        $order->status = config('res.order_status.ready');
        $order->save();
        return redirect('order')->with('message','Order is ready');
    }
}
