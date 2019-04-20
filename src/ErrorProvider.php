<?php
// ╭───────────────────────────────────────────────────────────┐
// │ QPF Framework [Key Studio]
// │-----------------------------------------------------------│
// │ Copyright (c) 2016-2019 quiun.com All rights reserved.
// │-----------------------------------------------------------│
// │ Author: qiun <qiun@163.com>
// ╰───────────────────────────────────────────────────────────┘
namespace qpf\error;

/**
 * 错误处理程序供应商
 */
class ErrorProvider
{
    /**
     * 延迟加载
     * @var bool
     */
    public $lazy = false;
    
    /**
     * 引导
     */
    public function boot()
    {
        
    }
    
    /**
     * 注册服务
     */
    public function register()
    {
        \QPF::$app->service('error', [
            'class' => '\qpf\error\Error'
        ]);
    }
}