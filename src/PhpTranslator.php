<?php
namespace qpf\error;

use qpf\lang\DictionaryLangPack;

/**
 * PHP 错误信息翻译者
 */
class PhpTranslator
{
    protected static $langPack;
    
    public static function init()
    {
        if (self::$langPack === null) {
            self::$langPack = new DictionaryLangPack(__DIR__ . '/langs');
        }
    }
    
    /**
     * 翻译
     * @param string $message 信息
     * @param string $lang 目标语言
     * @return string
     */
    public static function translate($message, $lang = 'zh-cn')
    {
        self::init();
        
        return self::$langPack->translate('dict', $message, $lang);
    }
}