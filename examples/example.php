<?php

require __DIR__ . '/../vendor/autoload.php';

include 'ComplicatedController.php';

$controller = new ComplicatedController();
echo $controller->actionDoTheJob();
