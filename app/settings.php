<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ], 
        // Monolog settings
        /*'logger' => [
            'name' => 'api_ebl',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],*/
        'jwt' => [
            'secret' => 'r2ydeLrRPb6N59iZ6a5oaWvm'
        ]
    ],
];
