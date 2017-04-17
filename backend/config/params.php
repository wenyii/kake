<?php
return [
    'menu' => [
        'a' => [
            'name' => '配置管理',
            'sub' => [
                'environment.index' => '生产环境',
                'config.index' => '常规配置',
                'config.file' => '文件预配置'
            ]
        ],
        'b' => [
            'name' => '计划任务',
            'sub' => [
                'mission.cache' => '缓存任务',
                'mission.attachment' => '附件任务',
                'mission.log' => '日志任务',
            ]
        ],
        'c' => [
            'name' => '用户',
            'sub' => [
                'user.index' => '用户管理',
                'login-log.index' => '登录日志',
            ]
        ],
        'd' => [
            'name' => '运行日志',
            'sub' => [
                'app-log.index' => '项目日志',
                'service-app-log.index' => '服务日志',
                'phone-captcha.index' => '短信验证码日志'
            ]
        ],
        'e' => [
            'name' => '酒店&产品',
            'sub' => [
                'hotel.index' => '酒店管理',
                'product.index' => '产品管理',
                'product-package.index' => '产品套餐管理',
            ]
        ],
        'f' => [
            'name' => '订单',
            'sub' => [
                'order.index' => '主订单管理',
                'order-sub.index' => '子订单管理',
                'bill.index' => '发票管理',
                'order-instructions-log.index' => '订单操作日志',
            ]
        ],
        'g' => [
            'name' => '通用',
            'sub' => [
                'ad.index' => '广告管理',
                'attachment.index' => '附件管理',
            ]
        ],
    ],

    'service_app_id' => 'kk_096a64b5359a74d',
    'service_app_secret' => '6819c5a31ab3f36be3cc18eedd5d60f7',
];
