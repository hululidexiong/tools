## Tools 功能组件含如下静态方法： ##

 1. 判断为空 isEmpty
 2. 分转换元 beforePrice  说明：返回值 乘 100
 3. 元转换分 lastPrice  说明：值 除 100 保留两位小数（末位会因精度导致四舍五入）
 4. 保留小数两位 （末位会因精度导致四舍五入） float2
 5. 分页 page
 6. 多结果分页 multiPage
 7. 按计时方式显示时间 secondToWords
 8. 验证是索引数组 is_indexArray
 9. 对称加密方法 sEncode
 10. 对称解密方法 sDecode


 isEmpty 接受一个值 同 empty不同的是 这个方法不会验证 0
 page 分页 如下：

 ```

         接收三个参数 int $count , int $page  , int $pagesize
         print_r( Tools::page(50 , 1) );

```

multiPage 等于page的升级版至此多结果集分页：

```

        除第一个参数是数组 其他与page相同
        $C = array();
        $C[] =  12;
        $C[] = 4;
        $C[] = 20;
        $C[] = 5;
        print_r( Tools::multiPage($C , 1 , 50) );

```

secondToWords( second ) 接收一个 int 参数 返回：按计时方式显示时间
 如：3分45秒

is_indexArray 验证数组是索引数组


## Installation

> 使用composer 安装

```shell
composer require "bbear/tools"
```

## Usage

```php
    use BBear\Tools\Base\Tools;
    $str = '~!@#$%^&*()_+  asdasdzxc啊哦哦';
    $enStr = Tools::sEncode($str);
    $deStr = Tools::sDecode($enStr);
    echo $str . "\n";
    echo $enStr . "\n";
    echo $deStr . "\n";
    var_dump( $str === $deStr);
```