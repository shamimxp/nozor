<?php

namespace App\Services;

use App\Models\OrderRequest;
use App\Models\OrderRequestItem;
use App\Models\DealerOrder;
use App\Models\DealerOrderItem;
use App\Models\DraftOrder;
use App\Models\DraftOrderItem;
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

    /**
     * Create a Draft Order from an Order Request.
     */
    public function createDraftOrder($orderRequestId, $selectedItems, $orderNote = null, $adminNote = null)
    {
        return DB::transaction(function () use ($orderRequestId, $selectedItems, $orderNote, $adminNote) {
            $orderRequest = OrderRequest::with('items.product')->findOrFail($orderRequestId);
            $priceCalculator = app(\App\Services\ProductPriceCalculator::class);

            $subTotal = 0;
            $draftItemsData = [];

            foreach ($selectedItems as $itemData) {
                $draftQty = (int) ($itemData['confirm_qty'] ?? $itemData['qty'] ?? 0);
                $itemId   = $itemData['order_request_item_id'];

                if ($draftQty <= 0) {
                    continue;
                }

                $requestItem = $orderRequest->items->firstWhere('id', $itemId);
                if (!$requestItem) {
                    continue;
                }

                $pendingQty = $requestItem->requested_qty - $requestItem->confirmed_qty;
                if ($draftQty > $pendingQty) {
                    throw new \Exception("Draft quantity for product ID {$requestItem->product_id} exceeds available quantity.");
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

                $itemTotal = $unitPrice * $draftQty;
                $subTotal += $itemTotal;

                $draftItemsData[] = [
                    'request_item' => $requestItem,
                    'draft_qty'    => $draftQty,
                    'unit_price'   => $unitPrice,
                    'item_total'   => $itemTotal,
                ];
            }

            if (empty($draftItemsData)) {
                throw new \Exception("Please select at least one item with valid quantity to create a draft order.");
            }

            // 1. Create Draft Order
            $draftOrder = DraftOrder::create([
                'draft_number'     => DraftOrder::generateDraftNumber(),
                'order_request_id' => $orderRequest->id,
                'dealer_id'        => $orderRequest->dealer_id,
                'sub_total'        => $subTotal,
                'discount'         => 0,
                'carrying_charge'  => 0,
                'grand_total'      => $subTotal,
                'note'             => $orderNote,
                'admin_notes'      => $adminNote,
                'status'           => 'draft',
                'creator_id'       => auth()->id(),
            ]);

            // 2. Create Draft Order Items & Deduct from Order Request Items
            foreach ($draftItemsData as $data) {
                $requestItem = $data['request_item'];
                $draftQty    = $data['draft_qty'];

                DraftOrderItem::create([
                    'draft_order_id' => $draftOrder->id,
                    'product_id'     => $requestItem->product_id,
                    'qty'            => $draftQty,
                    'unit_price'     => $data['unit_price'],
                    'total'          => $data['item_total'],
                ]);

                // Deduct from requested_qty
                $requestItem->requested_qty -= $draftQty;
                if ($requestItem->requested_qty <= 0) {
                    $requestItem->delete();
                } else {
                    $requestItem->save();
                }
            }

            // 3. Update order request status or remove if no items remain
            $remainingCount = OrderRequestItem::where('order_request_id', $orderRequest->id)->count();
            if ($remainingCount == 0) {
                $orderRequest->delete();
            } else {
                $orderRequest->status = 'partially_drafted';
                $orderRequest->save();
            }

            return $draftOrder;
        });
    }

    /**
     * Restore a draft item back to the original order request.
     */
    public function restoreDraftItemToOrderRequest($orderRequestId, $productId, $qty)
    {
        if (!$orderRequestId || $qty <= 0) {
            return;
        }

        $orderRequest = OrderRequest::find($orderRequestId);
        if (!$orderRequest) {
            return;
        }

        $item = OrderRequestItem::where('order_request_id', $orderRequestId)
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $item->requested_qty += $qty;
            $item->save();
        } else {
            OrderRequestItem::create([
                'order_request_id' => $orderRequestId,
                'product_id'       => $productId,
                'requested_qty'    => $qty,
                'confirmed_qty'    => 0,
            ]);
        }

        if (in_array($orderRequest->status, ['drafted', 'partially_drafted'])) {
            $orderRequest->status = 'pending';
            $orderRequest->save();
        }
    }

    /**
     * Convert a Draft Order to an actual Dealer Order.
     */
    public function convertDraftToDealerOrder($draftOrderId)
    {
        return DB::transaction(function () use ($draftOrderId) {
            $draftOrder = DraftOrder::with(['items.product', 'dealer'])->findOrFail($draftOrderId);

            if ($draftOrder->status === 'converted') {
                throw new \Exception("This draft order has already been converted to an order.");
            }

            if ($draftOrder->items->count() === 0) {
                throw new \Exception("Cannot convert a draft order with no items.");
            }

            // Create DealerOrder
            $order = DealerOrder::create([
                'order_number'    => DealerOrder::generateOrderNumber(),
                'order_date'      => now(),
                'dealer_id'       => $draftOrder->dealer_id,
                'sub_total'       => $draftOrder->sub_total,
                'discount'        => $draftOrder->discount ?? 0,
                'carrying_charge' => $draftOrder->carrying_charge ?? 0,
                'grand_total'     => $draftOrder->grand_total,
                'paid'            => 0,
                'due'             => $draftOrder->grand_total,
                'note'            => $draftOrder->note,
                'admin_notes'     => $draftOrder->admin_notes,
                'request_date'    => $draftOrder->created_at,
                'creator_id'      => auth()->id(),
                'status'          => 'confirm',
            ]);

            // Create DealerOrderItems
            foreach ($draftOrder->items as $item) {
                DealerOrderItem::create([
                    'dealer_order_id' => $order->id,
                    'product_id'      => $item->product_id,
                    'qty'             => $item->qty,
                    'price'           => $item->unit_price,
                    'total'           => $item->total,
                    'note'            => $item->note,
                ]);
            }

            // Delete draft order and items so draft order is emptied upon conversion
            $draftOrder->items()->delete();
            $draftOrder->delete();

            return $order;
        });
    }
}
