<?php

namespace backend\controllers;

/**
 * 服务运行日志管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class ServiceAppLogController extends AppLogController
{
    // 数据库
    public static $modelDb = DB_SERVICE;

    // 模型
    public static $modelName = 'AppLog';

    // 模型描述
    public static $modelInfo = '服务运行日志';
}
