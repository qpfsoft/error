<?php
// ╭───────────────────────────────────────────────────────────┐
// │ QPF Framework [Key Studio]
// │-----------------------------------------------------------│
// │ Copyright (c) 2016-2019 quiun.com All rights reserved.
// │-----------------------------------------------------------│
// │ Author: qiun <qiun@163.com>
// ╰───────────────────────────────────────────────────────────┘
namespace qpf\error;

use qpf\base\ServiceProvider;
use qpf\base\Application;

/**
 * 错误处理程序供应商
 */
class ErrorProvider extends ServiceProvider
{

    /**
     * 注册服务
     */
    public function register()
    {
        // 延迟加载
        $this->app->single('error', function (Application $app, $params, $option) {
            $error = new Error();
            $error->setDebug($this->app->isDebug());
            
            return $error;
        });
    }
}