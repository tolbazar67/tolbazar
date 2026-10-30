<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    private function sid(): string
    {
        return Session::getId();
    }

    public function index()
    {
        $items = CartItem::with(['product.service', 'product.branch'])
            ->where('session_id', $this->sid())
            ->get();

        $subtotal = $items->sum(fn($i) => $i->line_total);
        $tax = 0;   // can calculate per product
        $total = $subtotal + $tax;

        return view('public.cart', compact('items', 'subtotal', 'tax', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $mode = \App\Support\BookingMode::for($product->service?->code);
        if ($mode !== 'cart') {
            return back()->with('error', 'This product uses a different booking flow.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'meta'     => 'nullable|string|max:500',
        ]);

        $existing = CartItem::where('session_id', $this->sid())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'session_id' => $this->sid(),
                'user_id'    => auth()->id(),
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'unit_price' => $product->price,
                'meta'       => ['note' => $request->meta],
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        if ($item->session_id !== $this->sid()) abort(403);

        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $item)
    {
        if ($item->session_id !== $this->sid()) abort(403);
        $item->delete();
        return back()->with('success', 'Item removed.');
    }

    public function clear()
    {
        CartItem::where('session_id', $this->sid())->delete();
        return back()->with('success', 'Cart cleared.');
    }
}
