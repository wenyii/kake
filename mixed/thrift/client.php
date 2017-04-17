#!/usr/bin/env php
<?php
error_reporting(E_ALL);

$thriftPath = dirname(__DIR__) . '/../vendor/apache/thrift/lib/php/lib';
require_once $thriftPath . '/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', $thriftPath);
$loader->registerDefinition('api', __DIR__ . '/gen-php');
$loader->register();

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;

try {

    $socket = new THttpClient($argv[2], $argv[3], '/service.php');

    $transport = new TBufferedTransport($socket, 1024, 1024);
    $protocol = new TBinaryProtocol($transport);
    $client = new \api\CommonClient($protocol);

    $transport->open();
    $result = $client->run($argv[1]);
    print $result;

    $transport->close();

} catch (TException $tx) {
    print $tx->getMessage();
}
