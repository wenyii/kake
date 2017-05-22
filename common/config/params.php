<?php
$secondSite = 'www';
$secondBackend = 'backend';
$secondSource = 'source';
$secondPic = 'pic';
$domain = DOMAIN;

return [
    'app_name' => 'KAKE',
    'socks_token' => '#maiqi$kk',
    'use_cache' => true,

    'app_title' => 'KAKE旅行',
    'app_description' => 'KAKE旅行',
    'app_keywords' => 'KAKE旅行',

    'frontend_url' => "http://{$secondSite}.{$domain}",
    'frontend_source' => "http://{$secondSource}.{$domain}/kake/frontend",

    'backend_url' => "http://{$secondBackend}.{$domain}",
    'backend_source' => "http://{$secondSource}.{$domain}/kake/backend",

    'upload_path' => '/upload/kake',
    'upload_url' => "http://{$secondPic}.{$domain}/upload/kake",

    'wechat_callback' => "http://{$secondSite}.${domain}/",
    'alipay_callback' => "http://{$secondSite}.${domain}/",

    'thrift_ip' => '106.14.65.39',
    'thrift_port' => '8888'
];