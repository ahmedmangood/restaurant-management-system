<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Reservation;

use Carbon\Carbon;


class PrepareOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       
        $reservations = Reservation::where('start_date', '>', now())
        ->where('status', 'accepted')
        ->whereNotNull('order_id')
        ->get();
        
       foreach ($reservations as $reservation) {
        $order = Order::find($reservation->order_id);
        if ($order) {
            $prepare_time = Carbon::parse($reservation->start_date)->subMinutes(2);
            if (now() >= $prepare_time) {
                $order->status = 'Prepare';
                $order->save();
            }
        }
    }

      $this->info('Orders prepared successfully!');
}

}

