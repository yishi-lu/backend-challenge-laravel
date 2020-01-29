<?php

return [
    /* Auth Services */
    "App\Contracts\Auth\AuthService" => [
        "class" => "App\Services\Auth\AuthServiceImp",
        "shared" => false,
        "singleton" => true,
    ],

    /* Etfs Services */
    "App\Contracts\Business\EtfsService" => [
        "class" => "App\Services\Business\EtfsServiceImp",
        "shared" => false,
        "singleton" => true,
    ],

    /* Parser Services */
    "App\Contracts\Business\ParserService" => [
        "class" => "App\Services\Business\ParserServiceImp",
        "shared" => false,
        "singleton" => true,
    ],
];
