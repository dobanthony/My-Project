<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Shop;

class ChatBotController extends Controller
{
    public function handle(Request $request)
    {
        $message = strtolower($request->input('message')); // user input
        $reply = 'Sorry, I did not understand that. Try typing "help".';

        // Simple greeting/help
        if ($message === 'hi') {
            $reply = "Hello! 👋 How can I help you today?";
        } elseif ($message === 'help') {
            $reply = "You can ask me about products, orders, or shops. For example, you can ask about the cheapest product,
            the most expensive product, how many products are available, the stock for a specific product,
            how many orders exist, delivery info for your latest order, or shop info for a specific shop.";
        }

        // Product queries
        elseif (str_contains($message, 'cheapest product')) {
            $product = Product::orderBy('price', 'asc')->first();
            if ($product) {
                $reply = "Cheapest product is '{$product->name}' for ₱{$product->price}.";
            } else {
                $reply = "No products found.";
            }
        } elseif (str_contains($message, 'most expensive product')) {
            $product = Product::orderBy('price', 'desc')->first();
            if ($product) {
                $reply = "Most expensive product is '{$product->name}' for ₱{$product->price}.";
            } else {
                $reply = "No products found.";
            }
        } elseif (str_contains($message, 'how many products')) {
            $count = Product::count();
            $reply = "We currently have {$count} products available.";
        } elseif (str_contains($message, 'product stock for')) {
            $name = trim(str_replace('product stock for', '', $message));
            $product = Product::where('name', 'like', "%{$name}%")->first();
            if ($product) {
                $reply = "Product '{$product->name}' has {$product->stock} items in stock.";
            } else {
                $reply = "Product '{$name}' not found.";
            }
        } elseif (str_contains($message, 'product info for')) {
            $name = trim(str_replace('product info for', '', $message));
            $product = Product::where('name', 'like', "%{$name}%")->first();
            if ($product) {
                $reply = "Product '{$product->name}':\n";
                $reply .= "Price: ₱{$product->price}\n";
                $reply .= "Stock: {$product->stock}\n";
                $reply .= "Description: {$product->description}";
            } else {
                $reply = "Product '{$name}' not found.";
            }
        }

        // Orders queries
        elseif (str_contains($message, 'how many orders')) {
            $count = Order::count();
            $reply = "There are currently {$count} orders in total.";
        } elseif (str_contains($message, 'delivery info')) {
            $order = Order::latest()->first();
            if ($order) {
                $reply = "Latest order delivery info:\n";
                $reply .= "Product: {$order->product->name}\n";
                $reply .= "Delivery Date: {$order->delivery_date}\n";
                $reply .= "Address: {$order->delivery_address}";
            } else {
                $reply = "No orders found.";
            }
        }

        // Shop queries
        elseif (str_contains($message, 'shop info for')) {
            $shopName = trim(str_replace('shop info for', '', $message));
            $shop = Shop::where('shop_name', 'like', "%{$shopName}%")->first();
            if ($shop) {
                $reply = "Shop '{$shop->shop_name}' info:\n";
                $reply .= "Description: {$shop->shop_description}\n";
                $reply .= "Rating: {$shop->shop_rating} ({$shop->shop_rating_count} reviews)\n";
                $reply .= "Products count: {$shop->products()->count()}\n";
                $reply .= "Followers: {$shop->followers()->count()}";
            } else {
                $reply = "Shop '{$shopName}' not found.";
            }
        }

        // Return the reply via session flash
        return redirect()->route('chat')->with('botman_reply', $reply);
    }
}
