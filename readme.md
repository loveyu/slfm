# Single PHP 文件管理器（SLFM）
基于PHPFM单文件管理器修改过来的文件管理器SLFM，添加中文支持和压缩支持。

## 多文件模式
支持直接访问index.php 进行文件管理，便于修改和开发，以及定位错误。

## 单文件模式
可以通过build.php 构建单个PHP文件slfm.php进行访问操作。
具体执行 `php build.php` 即可生成slfm.php 和 slfm.min.php，其中min对PHP代码进行了压缩处理。
>	php build.php

## 关于
* 基于`PHPFM(0.9.8)`修改，详见[http://phpfm.sourceforge.net/](http://phpfm.sourceforge.net/)
* 修改后主页 [http://www.loveyu.net/slfm](http://www.loveyu.net/slfm)
* 修改者 : [loveyu](http://www.loveyu.org/3887.html)