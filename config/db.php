<?php

return [
    'class'      => '\sf\db\Connection',
    'dsn'        => 'mysql:host=localhost;dbname=sf',
    'username'   => 'jun',
    'password'   => 'jun',
    'attributes' => [
        \PDO::ATTR_EMULATE_PREPARES  => false,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
    ],
];
