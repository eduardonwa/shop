<?php

use App\Events\OrderCreated;
use App\Listeners\SendOrderNotification;

return [
    'listen' => [
        OrderCreated::class => [
            SendOrderNotification::class,
        ],
    ],
];