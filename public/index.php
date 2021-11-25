<?php

declare(strict_types=1);

use Api\App\Routes;

require_once './../vendor/autoload.php';

$app = Routes::create();

$app->addErrorMiddleware(true, true, true);

$app->run();
