<?php

use App\System\SystemController;

$app->get('/test', [SystemController::class, 'test']);
$app->get('/minify', [SystemController::class, 'minify']);
$app->get('/echo', [SystemController::class, 'echo']);