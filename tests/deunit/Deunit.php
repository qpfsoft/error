<?php
// ╭───────────────────────────────────────────────────────────┐
// │ QPF Framework [Key Studio]
// │-----------------------------------------------------------│
// │ Copyright (c) 2016-2019 quiun.com All rights reserved.
// │-----------------------------------------------------------│
// │ Author: qiun <qiun@163.com>
// ╰───────────────────────────────────────────────────────────┘
namespace qpf\deunit;

/**
 * 单元调试
 * @version 0.1.0
 */
class Deunit
{
    /**
     * 注册可搜索类的目录
     * ```
     * dir[] = __DIR__
     * ```
     * @var array
     */
    public static $dir = [];
    /**
     * 类名与类文件路径的映射
     * ```
     * map[qpf\dir\Class] = '/root/Class.php';
     * ```
     * 注意命名空间不要以`\`开头
     * @var array
     */
    public static $map = [];
    /**
     * 注册命名空间所在目录路径
     * ```
     * namespace['qpf'] = __DIR__
     * namespace['qpf\image'] = __DIR__ . '\image' </br>
     * ```
     * 注意命名空间不要以`\`开头或结尾
     * @var array
     */
    public static $namespace = [];
    
    /**
     * 初始化
     */
    public static function init()
    {
        spl_autoload_register([
            self::class,'autoload'
        ], true, true);
    }
    
    /**
     * 自动加载
     * @param string $class 类名, `aa\bb\Cc` 命名空间始终不会以`\`开头
     */
    public static function autoload($class)
    {
        $file = self::findFile($class);
        
        if (!empty($file)) {
            include($file);
        }
    }
    
    /**
     * 搜索类文件
     * @param string $class 类名
     * @return string
     */
    public static function findFile($class)
    {
        // 匹配类映射
        if (isset(self::$map[$class])) {
            return self::$map[$class];
        }
        
        // 匹配命名空间
        if (false !== strpos($class, '\\')) {
            foreach (self::$namespace as $prefix => $path) {
                $strlen = strlen($prefix);
                if (strncmp($prefix, $class, $strlen) === 0) {
                    $file = $path . strtr(substr($class, $strlen), '\\', DIRECTORY_SEPARATOR) . '.php';
                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }
        
        return self::findExtFile($class, '.php');
    }
    
    /**
     * 搜索指定扩展文件
     * @param string $class 类名
     * @param string $ext 扩展名, 例`.php`
     */
    public static function findExtFile($class, $ext)
    {
        // 文件名 namespan\name => namespan/name.ext
        $baseName = strtr($class, '\\', DIRECTORY_SEPARATOR) . $ext;
        
        // 在注册目录下搜索文件
        foreach (self::$dir as $path) {
            if (is_file($file = $path . DIRECTORY_SEPARATOR . $baseName)) {
                return $file;
            }
        }
        
        // 分割目录, 逐次去除一层命名空间目录
        $findPath = explode(DIRECTORY_SEPARATOR, $baseName);
        foreach (self::$dir as $root) {
            // 确保去除到仅剩文件名
            while (count($findPath) > 1) {
                array_shift($findPath); // 注册目录下已查找, 所以首选要去除一次目录
                $file = $root . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $findPath);
                if(is_file($file)) {
                    return $file;
                }
            }
        }
    }
}