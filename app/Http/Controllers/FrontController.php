<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function index() {

        $products = Product::where('is_featured', 'Yes')
            ->orderBy('id', 'DESC')->where('status', 1)
            ->take(8)->get();
        $data['featuredProducts'] = $products;

        $latestProducts = Product::orderBy('id', 'DESC')->where('status', 1)->take(8)->get();
        $data['latestProducts'] = $latestProducts;

        return view('front.home', $data);
    }

    public function addToWishlist(Request $request) {

        if (Auth::check() == false) {

            session(['url.intended' => url()->previous()]);

            return response()->json([
               'status' => false,
            ]);
        }

        $product = Product::where('id', $request->id)->first();
        if ($product == null) {
            return response()->json([
                'status' => true,
                'message' => '<div class="alert alert-danger"> Product not found </div>',
            ]);
        }

        Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $product->id,
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $product->id,
            ]
        );

        return response()->json([
           'status' => true,
           'message' => '<div class="alert alert-success"><strong>"' .$product->name. '"</strong> added to your wishlist </div>',
        ]);
    }
}
