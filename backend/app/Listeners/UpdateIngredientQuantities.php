<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Product;
use App\Models\Ingredient;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

use Illuminate\Queue\InteractsWithQueue;

class UpdateIngredientQuantities
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
    public function handle(OrderCreated $event)
    {  $order = $event->order;

        Log::info('UpdateIngredientQuantities listener handling OrderCreated event for order ' . $order->id);

        foreach ($order->products as $product) {
            foreach ($product->ingredients as $ingredient) {
                $pivot = $product->ingredients()->where('ingredient_id', $ingredient->id)->first()->pivot;
                $quantity = $pivot->quantity;

                Log::info("Updating ingredient {$ingredient->name} (id: {$ingredient->id}) quantity from {$ingredient->quantity} to " . ($ingredient->quantity - $quantity));

                $ingredient->decrement('quntity', $quantity);
            }
        }
    }



    
}

