<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

chdir(dirname(__DIR__));

if (file_exists('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
}


try {
    \App\Main::init()->run();
} catch (\App\Exceptions\ValidationException $validationException) {
    header('HTTP/1.1 400');
    //throw $validationException;
    echo "==========\n", sprintf('Validation Failed - %s', $validationException->getMessage()), "==========\n";
} catch (\Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "==========\n", 'Ooops! Internal Server Error', "==========\n";
}
?>