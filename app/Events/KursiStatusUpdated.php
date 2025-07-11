<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KursiStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_jadwal;
    public $kursi; // Data kursi yang diupdate

    public function __construct($id_jadwal, $kursi)
    {
        $this->id_jadwal = $id_jadwal;
        $this->kursi = $kursi;
    }

    public function broadcastOn(): array
    {
        // Channel bersifat private, hanya untuk jadwal spesifik
        return [new PrivateChannel('jadwal.' . $this->id_jadwal)];
    }
}
