<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GlobalBroadcastTop
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $playerName;
    public $score;
    public $rank;
    /**
     * Create a new event instance.
     *
     * @param string $playerName
     * @param int $score
     * @param int $rank
     */
    public function __construct($playerName, $score, $rank)
    {
        $this->playerName = $playerName;
        $this->score = $score;
        $this->rank = $rank;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Broadcasting on a public channel `top-scores`
        return new Channel('top-scores');
    }

    /**
     * Get the name of the event for broadcasting.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'GlobalBroadcastTop';  // Name the event for JavaScript listeners
    }
}
