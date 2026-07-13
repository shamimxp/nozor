<?php

namespace App\Services;

use App\Models\OrderRequest;
use App\Models\OrderRequestItem;
use App\Models\DealerOrder;
use App\Models\DealerOrderItem;
use Illuminate\Support\Facades\DB;

class OrderRequestService
{
    /**
     * Create a new order request from POS (Dealer).
     */
    public function createOrderRequest($dealerId, $items, $note = null)
    {
        return DB::transaction(function () use ($dealerId, $items, $note) {
            $orderRequest = OrderRequest::create([
                'request_number' => OrderRequest::generateRequestNumber(),
                'dealer_id' => $dealerId,
                'status'    => 'pending',
                'note'      => $note,
            ]);

            foreach ($items as $item) {
                OrderRequestItem::create([
                    'order_request_id' => $orderRequest->id,
                    'product_id'       => $item['product_id'],
                    'requested_qty'    => $item['requested_qty'],
                    'confirmed_qty'    => 0,
                ]);
            }

            return $orderRequest;
        });
    }

    /**
     * Confirm parts or all of an order request.
     */
    public function confirmOrderRequest($orderRequestId, $confirmedItems, $orderNote = null,$admin_note = null)
    {
        return DB::transaction(function () use ($orderRequestId, $confirmedItems, $orderNote,$admin_note) {
            $orderRequest = OrderRequest::with('items.product')->findOrFail($orderRequestId);
            
            $priceCalculator = app(\App\Services\ProductPriceCalculator::class);
            
            $subTotal = 0;
            $orderItemsData = [];
            
            foreach ($confirmedItems as $itemData) {
                $confirmQty = $itemData['confirm_qty'];
                $itemId = $itemData['order_request_item_id'];
                
                if ($confirmQty <= 0) {
                    continue;
                }

                $requestItem = $orderRequest->items->firstWhere('id', $itemId);
                if (!$requestItem) {
                    continue;
                }
                
                // Validation: confirm_qty <= pending_qty
                $pendingQty = $requestItem->requested_qty - $requestItem->confirmed_qty;
                if ($confirmQty > $pendingQty) {
                    throw new \Exception("Confirm quantity for product ID {$requestItem->product_id} exceeds pending quantity.");
                }

                $unitPrice = 0;
                if ($requestItem->product) {
                    if ($requestItem->product->is_manufacturer == 1) {
                        try {
                            $priceData = $priceCalculator->calculate($requestItem->product_id, $orderRequest->dealer_id);
                            $unitPrice = $priceData['dealer_price'];
                        } catch (\Exception $e) { $unitPrice = 0; }
                    } else {
                        $unitPrice = $requestItem->product->selling_price ?? 0;
                    }
                }
                
                $itemTotal = $unitPrice * $confirmQty;
                $subTotal += $itemTotal;
                
                $orderItemsData[] = [
                    'request_item' => $requestItem,
                    'confirm_qty'  => $confirmQty,
                    'unit_price'   => $unitPrice,
                    'item_total'   => $itemTotal,
                ];
            }

            if (empty($orderItemsData)) {
                throw new \Exception("No items selected for confirmation.");
            }
            
            // Create a new Order
            $order = DealerOrder::create([
                'order_number'    => DealerOrder::generateOrderNumber(),
                'order_date'      => now(),
                'dealer_id'       => $orderRequest->dealer_id,
                'sub_total'       => $subTotal,
                'discount'        => 0,
                'carrying_charge' => 0,
                'grand_total'     => $subTotal,
                'paid'            => 0,
                'due'             => $subTotal,
                'note'            => $orderNote,
                'admin_notes'     => $admin_note,
                'request_date'    => $orderRequest->created_at,
                'creator_id'      => auth()->id(), // admin id
                'status'          => 'confirm',
            ]);

            foreach ($orderItemsData as $data) {
                $requestItem = $data['request_item'];
                $confirmQty  = $data['confirm_qty'];
                
                // 2. Insert into dealer_order_items
                DealerOrderItem::create([
                    'dealer_order_id' => $order->id,
                    'product_id'      => $requestItem->product_id,
                    'qty'             => $confirmQty,
                    'price'           => $data['unit_price'],
                    'total'           => $data['item_total'],
                ]);

                // 3. Update confirmed_qty
                $requestItem->confirmed_qty += $confirmQty;
                $requestItem->save();

                // 4. If fully confirmed, delete item
                if (($requestItem->requested_qty - $requestItem->confirmed_qty) == 0) {
                    $requestItem->delete();
                }
            }

            // 5. Check if all items are deleted
            $remainingItemsCount = OrderRequestItem::where('order_request_id', $orderRequest->id)->count();
            
            if ($remainingItemsCount == 0) {
                // Delete the order request if all items are fulfilled
                $orderRequest->delete();
            } else {
                // 6. Update order_request status
                $orderRequest->status = 'partially_confirmed';
                $orderRequest->save();
            }

            return $order;
        });
    }
}
