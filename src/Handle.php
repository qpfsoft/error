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
 * 异常处理程序
 */
class Handle
{
    /**
     * 当前正在处理的异常
     * @var \Exception
     */
    protected $e;
    
    /**
     * 过滤的异常
     * @var array
     */
    protected $filterException = [
        '\\qpf\\error\\HttpExcetion'
    ];
    
    /**
     * 选项
     * @var array
     */
    protected $option = [
        'showError' => true,
        'message'   => 'Page Error!',
        'tpl'       => __DIR__ .'/tpl/error_handler.php',
    ];
    
    /**
     * 设置要解析的异常对象
     * @param \Exception $e 异常对象实例
     * @return void
     */
    public function set($e)
    {
        $this->e = $e;
    }
    
    /**
     * 返回当前正在处理的异常
     * @return Exception
     */
    public function get()
    {
        return $this->e;
    }
    
    /**
     * 判断是否处理该异常
     * @param \Throwable $e
     * @return bool
     */
    protected function isHandle(\Throwable $e)
    {
        foreach ($this->filterException as $type) {
            if ($e instanceof $type) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 解析异常
     * @param \Throwable $e
     * @return string
     */
    public function parse(\Throwable $e)
    {
        $result = false;
        
        if ($this->isHandle($e)) {
            if (Error::isDebug()) {
                $context = [
                    'name'    => $this->getName($e),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'message' => $this->getMessage($e),
                    'code'    => $this->getCode($e),
                ];
                

                $result = "[{$context['code']}]-{{$context['name']}}{$context['message']}[{$context['file']}:{$context['line']}]";
            } else {
                $context = [
                    'message' => $this->getMessage($e),
                    'code'    => $this->getCode($e),
                ];
                $result = "[{$context['code']}]{$context['message']}";
            }
        }
        
        return $result;
    }
    
    /**
     * 获取异常名称
     * @param \Throwable $e
     * @return string
     */
    public function getName(\Throwable $e)
    {
        if (method_exists($e, 'getName')) {
            $name = $e->getName();
            if (Error::isDebug() == 1 && ($pos = strpos($name, '('))) {
                $name = substr($name, $pos + 1, -1);
            }
            return $name;
        }
        
        $name = get_class($e);
        
        // 优化异常名称显示
        if(Error::isDebug() == 2) {
            $name = strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $name));

            return ErrorTranslation::translate(strtr($name, '_', ' '));
        }
        
        return $name;
    }
    
    /**
     * 解析带跟踪的消息
     * @param $string $message
     * @return int
     */
    protected function parseTraceMessage($message)
    {
        
        if(($pos = strpos($message, 'Stack trace:')) !== false) {
            // 清除跟踪字符串中的换行符
            $message = str_replace(["\n", "\r\n"], '', $message);
            $arr = explode('Stack trace:', $message);
            
            // 将消息转换为数组作为跟踪0
            $trace0 = [];
            if(($pos = strpos($arr[0], ' in ')) !== false) {
                $trace0['message'] = substr($arr[0], 0, $pos);
                $_str = substr($arr[0], $pos + 3);
                $_pos = strrpos($_str, ':');
                $trace0['file'] = substr($_str, 0, $_pos);
                $trace0['line'] = substr($_str, $_pos + 1);
                $trace0['function'] = '';
            }
            
            // 分割跟踪字符串
            $stackTrace = explode('#', $arr[1]);
            
            // 获得跟踪字符串数组
            $_info = [];
            foreach ($stackTrace as $i => $str) {
                if(!empty($str)) {
                    $_info[] = substr($str, 2);
                }
            }

            // 解析跟踪字符串数组
            $parse_trace_string = function($trace) {
                $result = [];
                foreach ($trace as $i => $value) {
                    $arr = explode(': ', $value);
                    if(!isset($arr[1])) {
                        $file = '';
                        $line = '';
                        $function = $arr[0];
                    } elseif (($pos = strpos($arr[0], '(')) !== false) {
                        $file = substr($arr[0], 0, $pos);
                        $line = substr($arr[0], $pos + 1, - 1);
                        $function = $arr[1];
                    } else {
                        $file = '';
                        $line = '';
                        $function = $arr[0] . $arr[1];
                    }
                    
                    $result[] = [
                        'file' => $file,
                        'line' => $line,
                        'function' => $function,
                    ];
                }

                return $result;
            };
            
            $trace = $parse_trace_string($_info);
            array_unshift($trace, $trace0);
            
            return [$trace0['message'], $trace];
        }
    }
    
    
    /**
     * 获得异常的错误信息
     * @param \Throwable $e
     */
    public function getMessage(\Throwable $e)
    {
        $message = $e->getMessage() ?: 'Server Error';
        
        if (PHP_SAPI == 'cli') {
            return $message;
        }

        if (Error::isDebug() == 2) {
            $message = ErrorTranslation::translate($message);
        }
        
        return $message;
    }
    
    /**
     * 获取错误编码
     * @param \Throwable $e
     * @return int
     */
    public function getCode(\Throwable $e)
    {
        $code = $e->getCode();
        
        if(!$code && $e instanceof ErrorException) {
            $code = $e->getType();
        }
            
        return $code;
    }
    
    /**
     * 获取出错PHP源码
     * @param string $file 文件
     * @param string $line 出错行号
     * @param string $type 显示类型, 影响显示行数
     * @return array
     */
    public function getPHPCode($file, $line, $type = 1)
    {
        try{
            $code = file($file);
            $count = count($code);
            $type = $type === 1 ? 19 : 13;
            $half = (int) ($type / 2); 
            $start = $line - $half > 0 ? $line - $half : 0;
            $end = $line + $half < $count ? $line + $half : $count - 1;
            $code = [
                'start' => $start,
                'end'   => $end,
                'code'  => array_slice($code, $start - 1, $type, true),
            ];
        } catch (\Exception $e) {
            $code = [];
        }
        
        return $code;
    }

    /**
     * 渲染显示异常
     * @param \Throwable $e
     * @return Response
     */
    public function renderException(\Throwable $e)
    {
        return $this->render($e);
    }
    
    /**
     * 记录日志
     * @param \Throwable $e
     * @return void
     */
    public function renderLog(\Throwable $e)
    {
        $log = $this->parse($e);
        if($log !== false) {
            Error::log($log, 'error');
        }
    }
    
    /**
     * 渲染显示到控制台
     * @param \Throwable $e
     * @return void
     */
    public function renderCli(\Throwable $e)
    {
        echo PHP_EOL . '\033[;36m ' . $e->getMessage() . '\x1B[0m\n' . PHP_EOL;
        exit(1);
    }
    
    /**
     * 渲染引擎
     * @param \Throwable $e
     * @return Response
     */
    protected function render(\Throwable $e)
    {
        $context = [];
        if (Error::isDebug()) {
            $context['name'] = $this->getName($e);
            $context['message'] = $this->getMessage($e);
            $context['code'] = $this->getCode($e);
            $context['file'] = $e->getFile();
            $context['line'] = $e->getLine();
            $context['trace'] = $e->getTrace();
            $context['source'] = $this->getPHPCode($context['file'], $context['line']);
            $context['context'] = [
                'GET'   => $_GET,
                'POST'  => $_POST,
                'Files' => $_FILES,
                'Cookie'    => $_COOKIE,
                'Session'   => isset($_SESSION) ? $_SESSION : [],
                'Server'    => $_SERVER,
                'Env'       => $_ENV,
                'Constants'  => $this->getConstants(),  
            ];
        } else {
            $context['code'] = $this->getCode($e);
            $context['message'] = $this->getMessage($e);
            
            if (!Error::isDebug()) {
                $context['message'] = $this->option['message'];
            }
        }
        $tpl = $this->option['tpl'];
        
        if (!Error::isDebug()) {
            $this->clearOutput();
        } else {
            $context['echo'] = $this->getLastOutput();
        }
        
        echo $this->viewFile($tpl, $context);
    }
    
    /**
     * 渲染PHP文件视图
     * @param string $_file_ PHP模板文件
     * @param array $_params_ 注入的PHP变量
     * @return string
     */
    protected function viewFile($_file_, $_params_)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        require ($_file_);
        
        return ob_get_clean();
    }
 
    /**
     * 清除之前所有的输出内容
     * @return void
     */
    protected function clearOutput()
    {
        for ($level = ob_get_level(); $level > 0; -- $level) {
            if (! @ob_end_clean()) {
                ob_clean();
            }
        }
    }
    
    /**
     * 返回最后一层缓冲区的内容
     * @return string
     */
    protected function getLastOutput()
    {
        while (ob_get_level() > 1) {
            ob_end_clean();
        }
        
        return ob_get_clean();
    }
    
    /**
     * 返回当前定义的所有常量
     * @return array
     */
    protected function getConstants()
    {
        $const = get_defined_constants(true);
        return isset($const['user']) ? $const['user'] : [];
    }
}