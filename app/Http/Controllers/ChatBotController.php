<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

class ChatBotController extends Controller
{
    /**
     * Handle incoming chat messages via AJAX.
     */
    public function handle(Request $request)
    {
        $user = Auth::user();

        // Ensure message is always a string
        $message = $this->normalizeMessage($request->input('message', ''));

        // Route message to handler
        $reply = $this->routeMessage($message, $user);

        // Return JSON response
        return response()->json([
            'botman_reply' => $reply
        ]);
    }

    // Message Routing
    private function routeMessage(string $message, $user = null): string
    {
        if ($reply = $this->handleGreeting($message)) return $reply;
        if ($reply = $this->handleHelp($message)) return $reply;
        if ($reply = $this->handleProductQueries($message)) return $reply;
        if ($reply = $this->handleOrderQueries($message, $user)) return $reply;
        if ($reply = $this->handleShopQueries($message)) return $reply;
        if ($reply = $this->handleFAQ($message)) return $reply;

        return "🤖 I didn't get that. Try typing 'help' for things you can ask me about!";
    }

     //Handlers
    private function handleGreeting(string $msg): ?string
    {
        $greetings = ['hi', 'hello', 'hey'];
        $times     = ['good morning', 'good afternoon', 'good evening', 'good night'];

        if (in_array($msg, $greetings)) {
            return "Hello! 👋 How can I help you today?";
        }

        if (in_array($msg, $times)) {
            return "🌞 Good day! How can I assist you?";
        }

        return null;
    }

    private function handleHelp(string $msg): ?string
    {
        if ($msg === 'help' || strpos($msg, 'what can you do') !== false) {
            return <<<HELP
                You can ask me about products, orders, or shops. For example:
                - Cheapest product / Most expensive product
                - Cheapest eco-friendly product
                - Out of stock products
                - Product stock for [product name]
                - Product info for [product name]
                - 🌱 Eco-friendly products
                - Is [product name] eco friendly?
                - How many orders exist / My latest order / pending or completed orders
                - Order history / Track my past orders
                - Order status #1234
                - Delivery info
                - Shop info for [shop name]
                - What shops are available?
                - Payment methods
                - Return policy
                - Shipping / delivery options
                - Contact support
                - Recommend me [category]
                HELP;
            }
            return null;
    }

    private function handleProductQueries(string $msg): ?string
    {
        if (strpos($msg, 'cheapest') !== false && strpos($msg, 'product') !== false) {
            $product = Product::orderBy('price', 'asc')->first();
            return $product
                ? "🛍️ The cheapest product is **{$product->name}** priced at ₱{$product->price}."
                : "⚠️ No products found.";
        }

        if (strpos($msg, 'expensive') !== false && strpos($msg, 'product') !== false) {
            $product = Product::orderBy('price', 'desc')->first();
            return $product
                ? "💎 The most expensive product is **{$product->name}** priced at ₱{$product->price}."
                : "⚠️ No products found.";
        }

        return null;
    }

    private function handleOrderQueries(string $msg, $user = null): ?string
    {
        if (strpos($msg, 'how many orders') !== false) {
            return "📦 There are currently " . Order::count() . " total orders.";
        }

        if (strpos($msg, 'my latest order') !== false) {
            if (!$user) return "❌ You must be logged in to see your latest order.";

            $order = Order::with('product')->where('user_id', $user->id)->latest()->first();
            $productName = isset($order->product) ? $order->product->name : 'Unknown';
            return $order
                ? "🛒 Your latest order:\nProduct: {$productName}\nStatus: {$order->status}\nDelivery Date: {$order->delivery_date}\nAddress: {$order->delivery_address}"
                : "📭 You have no recent orders.";
        }

        if (strpos($msg, 'pending orders') !== false) {
            if (!$user) return "❌ You must be logged in to see your pending orders.";
            $count = Order::where('user_id', $user->id)->where('status', 'pending')->count();
            return "⏳ You have {$count} pending orders.";
        }

        if (strpos($msg, 'completed orders') !== false) {
            if (!$user) return "❌ You must be logged in to see your completed orders.";
            $count = Order::where('user_id', $user->id)->where('status', 'completed')->count();
            return "✅ You have {$count} completed orders.";
        }

        if (strpos($msg, 'track my past orders') !== false || strpos($msg, 'order history') !== false || strpos($msg, 'show my orders') !== false) {
            if (!$user) return "❌ You must be logged in to see your order history.";
            return $this->formatOrderHistory($user);
        }

        if (preg_match('/status #(\d+)/', $msg, $matches)) {
            $order = Order::with('product')->find($matches[1]);
            $productName = isset($order->product) ? $order->product->name : 'Unknown';
            return $order
                ? "📦 Order #{$order->id} is currently '{$order->status}' for product '{$productName}'."
                : "❌ Sorry, I couldn't find order #{$matches[1]}.";
        }

        if (strpos($msg, 'delivery info') !== false) {
            if (!$user) return "❌ You must be logged in to see delivery info.";
            $order = Order::with('product')->where('user_id', $user->id)->latest()->first();
            $productName = isset($order->product) ? $order->product->name : 'Unknown';
            return $order
                ? "🚚 Latest delivery:\nProduct: {$productName}\nDelivery Date: {$order->delivery_date}\nAddress: {$order->delivery_address}"
                : "📭 No orders found.";
        }

        return null;
    }

    private function handleShopQueries(string $msg): ?string
    {
        if (strpos($msg, 'how many shops') !== false) {
            return "🏬 There are currently " . Shop::count() . " shops in our marketplace.";
        }

        if (preg_match('/shop info for (.+)/', $msg, $matches) || preg_match('/about (.+) shop/', $msg, $matches)) {
            $shop = Shop::where('shop_name', 'like', "%{$matches[1]}%")->first();
            $ownerName = isset($shop->owner) ? $shop->owner->name : 'Unknown';
            return $shop
                ? "🏪 Shop Info:\n📌 Name: {$shop->shop_name}\n📝 Description: {$shop->shop_description}\n⭐ Rating: {$shop->shop_rating} ({$shop->shop_rating_count} reviews)\n📍 Address: {$shop->address}\n📦 Products Available: " . $shop->products()->count() . "\n👤 Owner: {$ownerName}"
                : "❌ Sorry, I couldn't find a shop named '{$matches[1]}'.";
        }

        if (strpos($msg, 'what shops are available') !== false || strpos($msg, 'list shops') !== false) {
            $shops = Shop::pluck('shop_name');
            return $shops->count()
                ? "🏬 Available Shops:\n- " . $shops->implode("\n- ")
                : "❌ No shops are currently registered.";
        }

        return null;
    }

    private function handleFAQ(string $msg): ?string
    {
        if (strpos($msg, 'payment methods') !== false || strpos($msg, 'how can i pay') !== false) {
            return "💵 We currently only accept *Cash on Delivery (COD)* as a payment method.";
        }

        if (strpos($msg, 'shipping') !== false || strpos($msg, 'delivery options') !== false || strpos($msg, 'how long does delivery take') !== false) {
            return "🚚 Delivery usually takes *2–4 business days*. 📍 Tracking will be provided once your order is shipped.";
        }

        if (strpos($msg, 'return policy') !== false || strpos($msg, 'refund') !== false) {
            return "❌ Sorry, we do not accept returns or refunds. Please make sure to confirm your order before checkout.";
        }

        return null;
    }

    // Helpers
    private function normalizeMessage($msg): string
    {
        $msg = strtolower(trim((string)$msg));
        $msg = preg_replace('/[^\w\s#]/', '', $msg); // keep # for order numbers

        $stopWords = ['show', 'me', 'please', 'the', 'a', 'an', 'tell', 'about'];
        $words = array_diff(explode(' ', $msg), $stopWords);

        return implode(' ', $words);
    }

    private function formatOrderHistory($user): string
    {
        $orders = Order::with('product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        if ($orders->isEmpty()) {
            return "❌ You have no past orders.";
        }

        $reply = "📜 Your recent orders:\n";
        foreach ($orders as $order) {
            $productName = isset($order->product) ? $order->product->name : 'Unknown';
            $reply .= "\n🆔 Order #{$order->id} ({$order->status})\n";
            $reply .= "- Product: {$productName}\n";
            $reply .= "- Qty: {$order->quantity}\n";
            $reply .= "- Total: ₱" . ($order->quantity * (isset($order->product) ? $order->product->price : 0)) . "\n";
            $reply .= "- Delivery: {$order->delivery_status} (Date: {$order->delivery_date})\n";

            if (!empty($order->customization_details)) {
                $reply .= "- Customization: " . json_encode($order->customization_details) . "\n";
            }
        }

        return $reply;
    }
}
