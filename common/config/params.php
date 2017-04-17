<?php

$secondSite = 'www';
$secondBackend = 'backend';
$secondSource = 'source';
$domain = DOMAIN;

return [
    'app_name' => 'KAKE',
    'socks_token' => '#maiqi$kk',

    'frontend_url' => "http://{$secondSite}.{$domain}",
    'frontend_source' => "http://{$secondSource}.{$domain}/kake-frontend",

    'backend_url' => "http://{$secondBackend}.{$domain}",
    'backend_source' => "http://{$secondSource}.{$domain}/kake-backend",

    'upload_path' => '/upload/kake',
    'upload_url' => "http://{$secondSite}.{$domain}/upload/kake",

    'wechat_callback' => "http://{$secondSite}.${domain}/",
    'alipay_callback' => "http://{$secondSite}.${domain}/",

    'thrift_ip' => '192.168.0.222',
    'thrift_port' => '8888'
];
