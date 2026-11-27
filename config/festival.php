<?php

return [
    'tagline' => 'End the year loud. Start anew together.',
    'subtext' => 'Three days of pure emotion, midnight anthems, and the biggest year-end celebration in town.',
    'event' => [
        'city' => 'Bogor',
        'venue' => 'Kebun Raya Bogor',
        'location' => 'Kebun Raya Bogor, Bogor, Jawa Barat, Indonesia',
        'gates_open' => '14:00 WIB',
        'age_policy' => 'Semua usia dipersilakan.',
    ],
    'lineup' => [
        1 => [
            'title' => 'Day One - From Sunset Confessions to Midnight Anthems',
            'acts' => [
                ['time' => '15:00', 'artist' => 'The Lantis'],
                ['time' => '17:00', 'artist' => 'HIVI!'],
                ['time' => '20:30', 'artist' => 'Tulus'],
            ],
        ],
        2 => [
            'title' => 'Day Two - Malam Paling Jujur di Penghujung Tahun',
            'acts' => [
                ['time' => '15:00', 'artist' => 'Perunggu'],
                ['time' => '17:00', 'artist' => 'Hindia'],
                ['time' => '20:30', 'artist' => 'For Revenge'],
            ],
        ],
        3 => [
            'title' => 'Day Three - SING UNTIL MIDNIGHT, DANCE INTO 2027',
            'notes' => 'Perayaan Day 03 berlanjut melewati tengah malam hingga 1 Januari 2027.',
            'acts' => [
                ['time' => '15:00', 'artist' => 'Raim Laode'],
                ['time' => '17:00', 'artist' => 'Nadhif Basalamah'],
                ['time' => '20:30', 'artist' => 'Sal Priadi'],
                ['time' => '22:00', 'artist' => "Maliq & D'Essentials"],
                ['time' => '23:00', 'artist' => 'Dewa 19'],
                ['time' => '00:20', 'date_note' => '1 Januari 2027', 'artist' => 'Weird Genius'],
            ],
        ],
    ],
    'payment' => [
        'provider' => 'GoPay',
        'number' => env('FESTIVAL_GOPAY_NUMBER', '081313490079'),
        'account_name' => env('FESTIVAL_GOPAY_NAME', 'afterglow - Festival'),
    ],
    'contact' => [
        'whatsapp' => env('FESTIVAL_WHATSAPP', '6282128943420'),
        'whatsapp_message' => 'Halo tim AFTERGLOW! Saya ingin bertanya mengenai festival dan pemesanan tiket.',
        'email' => env('FESTIVAL_SUPPORT_EMAIL', 'afterglow@support.com'),
    ],
];
