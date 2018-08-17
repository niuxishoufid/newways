<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
-----http://www.cnblogs.com/weiyalin/p/7765558.html------
32位:
$ composer require h4cc / wkhtmltopdf-i386 0.12.x
$ composer require h4cc / wkhtmltoimage-i386 0.12.x，
64位:
$ composer require h4cc/wkhtmltopdf-amd64 0.12.x
$ composer require h4cc/wkhtmltoimage-amd64 0.12.x

(uname -a 命令查看系统位数)

cp vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64 /usr/local/bin/
cp vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64 /usr/local/bin/
并使其可执行：
chmod +x /usr/local/bin/wkhtmltoimage-amd64 
chmod +x /usr/local/bin/wkhtmltopdf-amd64

Linux 下使用该第三方插件需要几个库的支持 
sudo apt-get install libXrender*
sudo apt-get install libfontconfig*

↓実行しなくてもいいかもしれない
sudo apt-get install -y libxrender1 libfontconfig1 libxext6 fonts-ipafont
参照：https://qiita.com/ats05/items/cbb2956727cad2681d1d

Downgrade libssl 
  sudo apt install libssl1.0-dev=1.0.2n-1ubuntu5
不锁也可以吧。Lock it from future upgrades
  sudo apt-mark hold libssl1.0-dev 
参照：https://github.com/barryvdh/laravel-snappy/issues/217

安装laravel-snappy扩展包
composer require barryvdh/laravel-snappy

将ServiceProvider添加到config / app.php中的providers数组
Barryvdh\Snappy\ServiceProvider::class,

添加facade到config / app.php中的aliases数组中
'PDF' => Barryvdh\Snappy\Facades\SnappyPdf::class,
'SnappyImage' => Barryvdh\Snappy\Facades\SnappyImage::class,

生成配置文件
php artisan vendor:publish --provider="Barryvdh\Snappy\ServiceProvider"
此命令会在config/snappy.php生成配置文件

使用
先引入
use PDF;
use SnappyImage;

生成PDF文件
可以使用门面（facade）加载HTML字符串、文件或者视图，然后使用stream()方法显示在浏览器中、
save()方法保存到文件或者download()方法下载。
$pdf = PDF::loadView('pdf.invoice', $data);
return $pdf->download('invoice.pdf');

也可以链式操作
return PDF::loadFile(public_path().'/myfile.html')
  ->save('/path-to/my_stored_file.pdf')
  ->stream('download.pdf');

可以更改方向（landscape将方向设为横向，一般使用的都是竖向的，使用时注意一下）
和纸张大小，并隐藏或显示错误（默认情况下，调试打开时显示错误）
PDF::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')

生成图片
$pdf = SnappyImage::loadView('pdf.invoice', $data);
return $pdf->download('invoice.image');

参考：
    https://github.com/barryvdh/laravel-snappy
    https://qiita.com/ats05/items/cbb2956727cad2681d1d

*/

