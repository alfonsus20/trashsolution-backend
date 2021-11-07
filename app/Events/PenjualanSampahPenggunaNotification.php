<?php

namespace App\Events;

use App\Models\Penjualan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PenjualanSampahPenggunaNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message, $penjualan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Penjualan $penjualan, String $message)
    {
        $this->penjualan = $penjualan;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('penjualan-sampah.' . $this->penjualan->id);
    }

    public function broadcastAs()
    {
        return "penjualan-sampah-pengguna-notification";
    }
}
