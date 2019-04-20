# QPF-Error

QPF Error Handler

### 开启错误处理

```php
Error::register();
```

### 配置

###### debug  调试模式

(bool) true|false

(int) 0|1|2

false|0 : 效果与flase相同

1: 将采用英文语言

true|2: 将采用中文语言

```php
if (debug == 2)
```

当debug的值为true时, int 2 转换为bool 就是true. 所以将采用中文语言

当debug的值为1时, 判断 1 == 2 结果为: false