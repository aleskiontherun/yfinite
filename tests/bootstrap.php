<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('yfinite', __DIR__ . '/../src');
$loader->add('yfinite\test', __DIR__);
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
