<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use PDF;
use SnappyImage;

class PdfController extends Controller {

    public function index() {
        $users = User::all();
        $pdf = \PDF::loadView('pdf.template',compact('users'))
                ->setOption('encoding', 'utf-8');
        //$pdf = \PDF::loadView('pdf.pizza');
        //ブラウザ上で開ける
        //return $pdf->inline('user_list.pdf');
        return $pdf->stream('user_list.pdf');
        
        //download()方法下载。
        //return $pdf->download('user_list.pdf');
        //save('user_list.pdf')方法保存到文件
        
        //可以更改方向（landscape将方向设为横向，一般使用的都是竖向的，使用时注意一下）和纸张大小，
        //PDF::loadHTML($html)->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0)->save('myfile.pdf')
        //其他的一些基本使用和配置请参考文档资料
        //https://github.com/barryvdh/laravel-snappy
        //https://qiita.com/ats05/items/cbb2956727cad2681d1d
        //http://www.cnblogs.com/weiyalin/p/7765558.html
        //cp vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64 /usr/local/bin/
        //cp vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64 /usr/local/bin/
        //chmod +x /usr/local/bin/wkhtmltoimage-amd64 
        //chmod +x /usr/local/bin/wkhtmltopdf-amd64
        //Downgrade libssl: sudo apt install libssl1.0-dev=1.0.2n-1ubuntu5
        //不锁也可以吧。Lock it from future upgrades:sudo apt-mark hold libssl1.0-dev 
        
        ////生成图片
        //$pdf = SnappyImage::loadView('pdf.template',compact('users'));
        //return $pdf->download('user_list.jpg');
    }

}
