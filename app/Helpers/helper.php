<?php

use App\Models\Category;

    function getCategories() {
        return Category::orderBy('name', 'ASC')
            ->where('status', 1)
            ->with('sub_category')
            ->where('show', 'Yes')->get();
    }

    function getProductImage($productId) {
        return ProductImage::where('product_id', $productId)->first();
    }

    function orderEmail($orderId) {
        $order = Order::where('id', $orderId)
            ->with('items')->first();

        $mailData = [
            'subject' => 'Thanks for your order',
            'order' => $order,
        ];

        Mail::to($order->email)->send(new OrderEmail($mailData));
    }
?>
