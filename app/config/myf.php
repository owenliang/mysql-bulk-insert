<?php

return [
    // 调试模式
    'debug' => true,

    // 路由配置
    'route' => [
        // 静态路由
        'static' => [
            '/mysql/create' => ['Mysql', 'create'],
            '/mysql/bulk' => ['Mysql', 'bulk'],
            '/mysql/single' => ['Mysql', 'single'],
        ],
        // pcre正则路由
        'regex' => [],
    ],
];