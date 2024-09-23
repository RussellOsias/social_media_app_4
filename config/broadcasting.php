<?php

return [

    'default' => env('BROADCAST_DRIVER', 'null'),

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('d006c53948c8e840c514'),
            'secret' => env('92fbea14c8f7c73bfa9b'),
            'app_id' => env('1869471'),
            'options' => [
                'cluster' => env('ap1'),
                'useTLS' => true,
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
