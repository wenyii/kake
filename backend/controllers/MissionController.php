<?php

namespace backend\controllers;

use common\components\Helper;
use Yii;

/**
 * 计划任务管理
 *
 * @auth-inherit-except index
 * @auth-inherit-except add
 * @auth-inherit-except edit
 * @auth-inherit-except front
 */
class MissionController extends GeneralController
{
    /**
     * 缓存任务列表
     */
    public function actionCache()
    {
        return $this->display('cache');
    }

    /**
     * 清空后台缓存
     */
    public function actionAjaxClearBackendCache()
    {
        Yii::$app->cache->flush();
        $this->success(null, '缓存清除成功');
    }

    /**
     * 清空前台缓存
     */
    public function actionAjaxClearFrontendCache()
    {
        $result = $this->api('frontend', 'site.clear-cache');
        if ($result['state'] < 1) {
            $this->fail('缓存清除失败: ' . $result['info']);
        }

        $this->success(null, '缓存清除成功');
    }

    /**
     * 清空服务缓存
     */
    public function actionAjaxClearServiceCache()
    {
        $result = $this->service('general.clear-cache');
        if (is_string($result)) {
            $this->fail('缓存清除失败: ' . $result);
        }

        $this->success(null, '缓存清除成功');
    }

    /**
     * 附件任务列表
     */
    public function actionAttachment()
    {
        return $this->display('attachment');
    }

    /**
     * 清理无效附件 (谨慎)
     *
     * @auth-info-style <span class="text-danger">{info}</span>
     */
    public function actionAjaxClearAttachment()
    {
        $script = Yii::getAlias('@script/attachment-handler.py');
        $uploadPath = Yii::$app->params['upload_path'];
        $cmd = sprintf('python %s %s %s %d', ...[
            $script,
            'kake',
            $uploadPath,
            0
        ]);

        exec($cmd, $result);

        $this->success(null, '该任务已执行, 数秒后将自动完成');
    }

    /**
     * 日志任务列表
     */
    public function actionLog()
    {
        return $this->display('log');
    }

    /**
     * 刷新日志到数据库
     *
     * @access private
     *
     * @param string  $projectName
     * @param string  $db
     * @param integer $maxLogFiles
     *
     * @return void
     */
    private function flushLog($projectName, $db, $maxLogFiles = 20)
    {
        $script = Yii::getAlias('@script/log-handler.py');
        $logPath = Yii::getAlias('@root/' . $projectName . '/runtime/logs');

        // `> /dev/null 2>&1 &` 是为不阻塞执行 shell
        $cmd = sprintf('python %s %s %s %d > /dev/null 2>&1 &', ...[
            $script,
            $db,
            $logPath,
            $maxLogFiles
        ]);
        exec($cmd, $result);

        $this->success(null, '该任务已开始执行, 短则数分, 长则数时');
    }

    /**
     * 刷新后台日志到数据库 (谨慎)
     *
     * @auth-info-style <span class="text-danger">{info}</span>
     */
    public function actionAjaxFlushAllBackendLog()
    {
        $this->flushLog('kake/backend', 'kake', 20);
    }

    /**
     * 刷新后台日志到数据库 - 最近
     *
     * @auth-same mission/ajax-flush-all-backend-log
     */
    public function actionAjaxFlushLatelyBackendLog()
    {
        $this->flushLog('kake/backend', 'kake', 0);
    }

    /**
     * 刷新前台日志到数据库 (谨慎)
     *
     * @auth-info-style <span class="text-danger">{info}</span>
     */
    public function actionAjaxFlushAllFrontendLog()
    {
        $this->flushLog('kake/frontend', 'kake', 20);
    }

    /**
     * 刷新前台日志到数据库 - 最近
     *
     * @auth-same mission/ajax-flush-all-frontend-log
     */
    public function actionAjaxFlushLatelyFrontendLog()
    {
        $this->flushLog('kake/frontend', 'kake', 0);
    }

    /**
     * 刷新服务日志到数据库 (谨慎)
     *
     * @auth-info-style <span class="text-danger">{info}</span>
     */
    public function actionAjaxFlushAllServiceLog()
    {
        $this->flushLog('service', 'service', 20);
    }

    /**
     * 刷新服务日志到数据库 - 最近
     *
     * @auth-same mission/ajax-flush-all-service-log
     */
    public function actionAjaxFlushLatelyServiceLog()
    {
        $this->flushLog('service', 'service', 0);
    }
}
