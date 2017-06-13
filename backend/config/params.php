<?php
return [
    'use_cache' => true,
    'service_app_id' => 'kk_096a64b5359a74d',
    'service_app_secret' => '6819c5a31ab3f36be3cc18eedd5d60f7',
    'menu' => [
        'a' => [
            'name' => '生产环境',
            'sub' => [
                'environment.index' => '生产环境'
            ],
            'pass_role' => 10
        ],
        'b' => [
            'name' => '项目配置',
            'sub' => [
                'config.index' => '常规配置',
                'config.file' => '文件预配置'
            ]
        ],
        'c' => [
            'name' => '计划任务',
            'sub' => [
                'mission.cache' => '缓存任务',
                'mission.attachment' => '附件任务',
                'mission.log' => '日志任务',
            ]
        ],
        'd' => [
            'name' => '用户',
            'sub' => [
                'user.index' => '用户管理',
                'producer-setting.index' => '分销商管理',
                'login-log.index' => '登录日志',
            ]
        ],
        'e' => [
            'name' => '运行日志',
            'sub' => [
                'app-log.index' => '项目日志',
                'service-app-log.index' => '服务日志',
                'phone-captcha.index' => '短信验证码日志'
            ]
        ],
        'f' => [
            'name' => '酒店和产品',
            'sub' => [
                'hotel.index' => '酒店管理',
                'product.index' => '产品管理',
                'product-producer.index' => '产品分销管理',
                'product-package.index' => '产品套餐管理',
            ]
        ],
        'g' => [
            'name' => '订单',
            'sub' => [
                'order.index' => '主订单管理',
                'order-sub.index' => '子订单管理',
                'bill.index' => '发票管理',
                'order-instructions-log.index' => '订单操作日志',
            ]
        ],
        'h' => [
            'name' => '活动日志',
            'sub' => [
                'activity-lottery-code.index' => '抽奖码领取记录'
            ]
        ],
        'i' => [
            'name' => '其他',
            'sub' => [
                'ad.index' => '广告管理',
                'attachment.index' => '附件管理',
                'wx-menu.index' => '服务号菜单',
            ]
        ],
        'j' => [
            'name' => '分销系统',
            'sub' => [
                'producer-setting.center' => '个人设置',
                'producer.index' => '推广链接',
                'producer-product.index' => '分销产品管理',
                'producer-order.index' => '分销订单',
            ],
            'pass_role' => 10
        ]
    ]
];