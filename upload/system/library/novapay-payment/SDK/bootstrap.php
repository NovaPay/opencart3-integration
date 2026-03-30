<?php

/**
 * NovapaySDK bootstrap file.
 * Use composer loader if you are able to use composer.
 * https://www.php-fig.org/psr/psr-2/
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

spl_autoload_register(
    function ($class) {
        // Novapay\Payment\SDK\Schema\Client.php
        $prefix = 'Novapay\Payment\SDK\\';
        $len = strlen($prefix);
        if ($prefix !== substr($class, 0, $len)) {
            return;
        }
        $class = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $len));

        $file = __DIR__ . DIRECTORY_SEPARATOR . "$class.php";

        // phpcs:ignore
        require_once $file;
    }
);