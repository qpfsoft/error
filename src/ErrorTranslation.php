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
 * PHP错误消息翻译
 * --------------------------
 * - 采用词组和单词的方式来翻译，先检查词组库再检查单词库。
 * - 单词库采用首字符分组形式，减少索引范围
 * - 翻译机智采用空格分隔方式,由于php错误语句存在用单词与:,!三种符号链接,
 * - 翻译支持单词首位出现符号,[:,!]如有其他需要添加符号列表
 * - 词组库尽量少用,因为每个词组都要增加1次循环遍历替换.
 */
class ErrorTranslation
{
    /**
     * 翻译错误
     *
     * @param string $string 错误信息
     * @return string
     */
    public static function translate($string)
    {
        $string = self::parsePhrase($string);
        $string = explode(' ', $string);
        foreach ($string as $key => $val) {
            $string[$key] = self::parseWord($val);
        }
        
        return implode(' ', $string);
    }
    
    /**
     * 解析单词
     *
     * @param string $str 一个单词
     * @return string 解析成功返回解释，否则原样返回
     */
    protected static function parseWord($str)
    {
        // 符号库
        $symbol = [':', ',', '!', ';'];
        // 获取字符最后一位
        $last_symbol = substr($str, - 1, 1);
        $first_symbol = strtolower(substr($str, 0, 1));
        
        // 允许单词尾部是附加的一个符号
        if ($last_symbol !== null && in_array($last_symbol, $symbol)) {
            $str = rtrim($str, $last_symbol);
            $str = self::parseWord($str) . $last_symbol;
        }
        
        // 单词库
        $lib = self::getWordlibrary($first_symbol);
        
        if (isset($lib[strtolower($str)])) {
            return $lib[strtolower($str)];
        }
        
        return $str;
    }
    
    /**
     * 解析短语
     *
     * @param string $str 一句短语
     * @return string 解析短语中部分内容
     */
    protected static function parsePhrase($str)
    {
        /* 特殊短语，多个单词组成不可分割, 不区分大小写 */
        $lib = [
            // a
            // b
            'be a'  => '是一个',
            // c
            'Cannot redeclare' => '不能重新声明',
            'Can only'  => '只可以',
            // d
            'does not exist' => '不存在',
            // e
            // f
            // g
            // h
            'have a' => '有一个',
            'a valid' => '一个有效的',
            // i
            'in use'    => '被使用',
            // j
            // k
            // l
            // m
            'may not' => '不能',
            // n
            'non-object' => '非对象',
            'Non-static' => '非静态',
            // o
            // p
            // q
            // r
            // s
            'smaller than' => '小于',
            // t
            'to be' => '是',
            'Too few' => '缺少',
            // u
            // v
            // w
            // x
            // y
            // z
            
        ];
        
        foreach ($lib as $phrase => $parse) {
            if (false !== ($pos = stripos($str, $phrase))) {
                
                // bug: `be a` 会错误的替换 `be assigned` 为 `是一个ssigned`
                // 修复: 判断要替换值紧挨的下一个字符为空或空格
                $next = substr($str, $pos + strlen($phrase), 1);
                if ($next === '' || $next === ' ') {
                    $str = str_ireplace($phrase, $parse, $str);
                }
            }
        }
        
        
        
        return $str;
    }
    
    /**
     * 返回单词库
     *
     * @param string $index 首字母
     * @return array 指定首字母所有单词
     */
    private static function getWordlibrary($index)
    {
        // 必须小写
        $lib = [
            'a' => [
                'aborting'      => '终止',
                'assumed'       => '假定',
                'a'             => '一',
                'an'            => '一个',
                'argument'      => '参数',
                'arguments'     => '参数',
                'and'           => '和',
                'access'        => '访问',
                'allowed'       => '允许',
                'are'           => '是',
                'abstract'      => '(abstract)抽象',
                'assuming'      => '假设',
                'already'       => '已经',
                'assigned'      => '分配',
                'alphanumeric'  => '字母',
                'active'        => '活跃',
            ],
            'b' => [
                'be'            => '是',
                'base'          => '库',
                'by'            => '由',
                'because'       => '因为',
                'bounds'        => '界限',
                'backslash'     => '反斜线',
            ],
            'c' => [
                'call'          => '调用',
                'callable'      => '回调',
                'class'         => '类',
                'constant'      => '常量',
                'could'         => '可能',
                'can'           => '可以',
                'converted'     => '转换',
                'context'       => '上下文',
                'called'        => '调用',
                'character'     => '字符',
                'cannot'        => '不能',
                'contains'      => '包含',
                'conversion'    => '转换',
                'callback'      => '回调',
                'construct'     => '构造',
                'compatible'    => '兼容',
                'connection'    => '连接',
                'compile'       => '编译',
                'comment'       => '注解',
            ],
            'd' => [
                'delimiter'     => '分隔符',
                'defined'       => '定义',
                'definition'    => '定义',
                'directory'     => '目录',
                'declaration'   => '声明',
                'detected'      => '检测到',
                'digit'         => '数字',
                'declared'      => '宣布',
                'declare'       => '声明',
            ],
            'e' => [
                'expect'        => '预期',
                'expecting'     => '预期',
                'expected'      => '预期',
                'error'         => '错误',
                'expects'       => '要求',
                'empty'         => '空',
                'est'           => '为',
                'encoding'      => '编码',
                'exists'        => '存在',
                'exist'         => '存在',
                'exactly'       => '正好',
                'execution'     => '执行',
                'exceeded'      => '超过',
                'element'       => '元素',
                'exception'     => '异常',
            ],
            'f' => [
                'function'      => '函数',
                'found'         => '找到',
                'for'           => '对于',
                'from'          => '从',
                'file'          => '文件',
                'first'         => '第一个',
                'failed'        => '失败',
                'filter'        => '过滤'
            ],
            'g' => [
                'given'         => '特定'
            ],
            'h' => [
                'hex'           => '十六进制',
                'have'          => '有',
            ],
            'i' => [
                'inconnue'      => '未知', // mysql
                'interface'     => '接口',
                'is'            => '是',
                'invalid'       => '无效',
                'index'         => '索引[\'?\']',
                'in'            => '于',
                'illegal'       => '非法',
                'input'         => '输入',
                'implement'     => '实现(implement)',
                'incompatible'  => '不相容',
                'internal'      => '内置',
                'instance'      => '实例',
                'include'       => '包含',
                'interfaces'    => '接口',
                'incorrect'     => '不正确',
            ],
            'j' => [],
            'k' => [],
            'l' => [
                'level'         => '级别',
                'line'          => '行',
                'length'        => '长度',
                'least'         => '最小',
            ],
            'm' => [
                'maximum'       => '最大值',
                'method'        => '方法',
                'member'        => '成员',
                'missing'       => '缺少',
                'miss'          => '缺少', // missing 简写
                'modifiers'     => '修饰符',
                'multiple'      => '多个',
                'must'          => '必须',
                'methods'       => '方法',
                'make'          => '使',
                'magic'         => '魔术',
                'modifier'      => '修饰符',
            ],
            'n' => [
                'nesting'       => '嵌套',
                'not'           => '不',
                'namespace'     => '命名空间',
                'no'            => '没有',
                'non'           => '非',
                'name'          => '名称',
            ],
            'o' => [
                'of'            => '的',
                'or'            => '或',
                'object'        => '对象',
                'on'            => '在',
                'only'          => '仅', // 只有
                'offset'        => '偏移',
                'open'          => '打开',
                'omitted'       => '省略',
                'operand'       => '操作数',
                'options'       => '选项',
            ],
            'p' => [
                'param'         => '参数', // parameter简写
                'parameter'     => '参数',
                'parameters'    => '参数',
                'property'      => '属性',
                'protected'     => 'protected(受保护的)',
                'passed'        => '传给',
                'public'        => 'public(公共的)',
                'pass'          => '通过',
            ],
            'q' => [],
            'r' => [
                'reached'       => '到达',
                'reference'     => '引用',
                'remaining'     => '剩余'
            ],
            's' => [
                'syntax'        => '语法',
                'string'        => '字符串',
                'supplied'      => '提供',
                'should'        => '应该',
                'such'          => '这样的',
                'statement'     => '语句',
                'script'        => '脚本',
                'stream'        => '流',
                'static'        => '(static)静态的',
                'statically'    => '静态方式',
                'source'        => '资源',
                'scope'         => '范围',
                'seconds'       => '秒',
                'setting'       => '设置',
                'starting'      => '开始',
            ],
            't' => [
                'to'            => '至',
                'type'          => '类型',
                'therefore'     => '因此',
                'the'           => '该',
                'transaction'   => '事务',
                'there'         => '那里', 
            ],
            'u' => [
                'undefined'     => '未定义',
                'unexpected'    => '意外',
                'use'           => '使用',
                'uninitialized' => '未初始化',
                'unsupported'   => '不支持',
                'uncaught'      => '未捕获',
                'unknown'       => '未知',
                'undeclared'    => '未申报',
                'unterminated'  => '未终止的',
            ],
            'v' => [
                'variable'      => '变量',
                'variables'     => '变量',
                'violation'     => '冲突',
                'vide'          => '空',
                'visibility'    => '可见',
            ],
            'w' => [
                'wrong'         => '错误',
                'warning'       => '警告',
            ],
            'x' => [],
            'y' => [],
            'z' => []
        ];
        return isset($lib[$index]) ? $lib[$index] : [];
    }
}