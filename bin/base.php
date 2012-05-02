<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $rootDir = __DIR__ . '/../';
} else {
    $rootDir = __DIR__ . '/../../../../';
}

if (file_exists($rootDir . '/vendor/autoload.php') === false) {
    die('Please run "composer.phar install" first' . "\n");
}

require $rootDir . '/vendor/autoload.php';
return $rootDir;
?>