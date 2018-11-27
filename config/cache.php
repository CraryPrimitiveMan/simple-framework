<?php

return [
    // 'class' => '\sf\cache\FileCache',
    // 'cachePath' => SF_PATH . '/runtime/cache/'
    'class' => 'sf\cache\RedisCache',
    'redis' => [
        'host'     => 'localhost',
        'port'     => 6379,
        'database' => 0,
        'password' => false,
        // 'options' => [Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP],
    ],
];
