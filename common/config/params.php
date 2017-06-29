<?php
$secondSite = 'www';
$secondBackend = 'backend';
$secondSource = 'source';
$secondPic = 'pic';
$domain = DOMAIN;

return [
    'app_name' => 'KAKE',
    'api_token_backend' => '#maiqi$kk',
    'api_token_frontend' => '#maiqi$kk',
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

    'tmp_path' => '/tmp',

    'wechat_callback' => "http://{$secondSite}.${domain}/",
    'alipay_callback' => "http://{$secondSite}.${domain}/",

    'thrift_ip' => '106.14.65.39',
    'thrift_port' => '8888',

    'site_search_ad_keyword' => null,
    'site_search_ad_url' => null,

    'site_focus_limit' => 6,
    'site_sale_limit' => 6,
    'site_ad_banner_limit' => 3,
    'site_ad_focus_limit' => 3,
    'site_product_limit' => 10,

    'product_page_size' => 8,
    'order_page_size' => 8,

    'upgrade' => false,
    'upgrade_title' => 'System upgrade',
    'upgrade_minute' => 15,
    'upgrade_message' => '系统版本升级中，本次升级约需 %d 分钟，尽情期待',

    'order_pay_timeout' => 30,

    'distribution_limit' => 5,

    'commission_min_price' => 99,
];