<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\OrderHeader;
use App\Models\User;
use Validator;
use Exception;



class OrderController extends Controller
{
    //
    public function createOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'shipping_contact_mech_id' => 'required|exists:contact_mechs,id',
            'billing_contact_mech_id' => 'required|exists:contact_mechs,id',
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $order = OrderHeader::create([
            'order_date' => $request->order_date,
            'customer_id' => $request->customer_id,
            'shipping_contact_mech_id' => $request->shipping_contact_mech_id,
            'billing_contact_mech_id' => $request->billing_contact_mech_id,
        ]);

        foreach ($request->order_items as $item) {
            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'status' => $item['status'],
            ]);
        }

        return response()->json([
            'message' => 'Order created successfully.',
            'order' => $order,
        ], 201);
    }

    public function retrieveOrder($orderId)
    {
        $order = OrderHeader::with([
            'customer', 
            'shippingContactMech', 
            'billingContactMech', 
            'orderItems.product'
        ])
        ->find($orderId);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'order' => $order
        ], Response::HTTP_OK);
    }

    public function deleteOrder($orderId)
    {
        $order = OrderHeader::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

    public function addOrderItem(Request $request, $orderId)
    {

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1', 
        ]);

        $order = OrderHeader::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $orderItem = new OrderItem([
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
        ]);

        $order->orderItems()->save($orderItem);

        return response()->json(['message' => 'Order item added successfully', 'order_item' => $orderItem], 201);
    }

    public function updateOrderItem(Request $request, $orderId, $orderItemSeqId)
    {

        $request->validate([
            'quantity' => 'required|integer|min:1', 
            'status' => 'required|string|max:255', 
        ]);

        $orderItem = OrderItem::where('order_id', $orderId)
                                ->where('order_item_seq_id', $orderItemSeqId) 
                                ->first();

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $orderItem->update([
            'quantity' => $request->quantity,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Order item updated successfully', 'order_item' => $orderItem], 200);
    }

    public function deleteOrderItem($orderId, $orderItemSeqId)
    {
        $orderItem = OrderItem::where('order_id', $orderId)
                              ->where('order_item_seq_id', $orderItemSeqId)
                              ->first();

        if ($orderItem) {
            $orderItem->delete();

            return response()->json(['message' => 'Order item deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Order item not found'], 404);
        }
    }

    public function updateOrder(Request $request, $order_id)
{
    $validator = Validator::make($request->all(), [
        'order_date' => 'nullable|date',
        'shipping_contact_mech_id' => 'nullable|exists:contact_mechs,id',
        'billing_contact_mech_id' => 'nullable|exists:contact_mechs,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $order = OrderHeader::find($order_id);

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    $order->order_date = $request->get('order_date', $order->order_date);
    $order->shipping_contact_mech_id = $request->get('shipping_contact_mech_id', $order->shipping_contact_mech_id);
    $order->billing_contact_mech_id = $request->get('billing_contact_mech_id', $order->billing_contact_mech_id);

    $order->save();

    return response()->json([
        'message' => 'Order updated successfully.',
        'order' => $order
    ], 200);
}

}