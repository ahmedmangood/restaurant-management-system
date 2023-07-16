<?php

namespace App\Listeners;

use App\Events\OrderProductCanceled;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class OnOrderProductCanceled
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderProductCanceled $event): void
    {
        $order = $event->order;
        $product = $event->product;

        foreach ($product->ingredients as $ingredient) {
            $pivot = $product->ingredients()->where('ingredient_id', $ingredient->id)->first()->pivot;
            $quantity = $pivot->quantity;

            Log::info("Updating ingredient {$ingredient->name} (id: {$ingredient->id}) quantity from {$ingredient->quantity} to " . ($ingredient->quantity + $quantity));

            $ingredient->increment('quntity', $quantity);
        }  
      }
}
