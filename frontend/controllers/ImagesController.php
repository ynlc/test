<?php

namespace frontend\controllers;
use Yii;
use \yii\web\Controller;
use common\models\images;
use common\components\Uploader;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

class ImagesController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['index'],
                'lastModified' => function ($action, $params) {
                    //获取参数
                    $getId = trim(Yii::$app->request->get('id',''));
                    if(!$getId && strpos('-',$getId) === false){
                        return  $this->redirect(['site/error']);
                    }
                    $arr = explode('-',$getId);
                    //获取数据
                    $imgInfo = images::getOne($arr[0]);
                    if(!$imgInfo){
                        return $this->redirect(['site/error']);
                    }
                    $width = 0;
                    $height = 0;
                    $infos = explode('.',$arr[1]);
                    if(strpos($infos[0],'w') !== false){
                        $width = ltrim($infos[0],'w');
                    }
                    if(strpos($infos[0],'h') !== false){
                        $height = ltrim($infos[0],'h');
                    }
                    $ext = $infos[1];
                    //判断浏览器是否支持webp格式
                    $webp = strpos($_SERVER['HTTP_ACCEPT'], 'image/webp');
                    if($webp === false && $ext == 'webp'){ //不支持随便给个
                        $ext = 'png';
                    }
                    //先获取图片
                    $obj = new Uploader();
                    $getImg = $obj->getThumb($width,$height,$ext,$imgInfo);
                    if(!file_exists($getImg)){
                        return 0;
                    }
                    return filemtime($getImg);
                },
            ],
        ];
    }
    public function actionIndex()
    {
        //获取参数
        $getId = trim(Yii::$app->request->get('id',''));
        if(!$getId && strpos('-',$getId) === false){
            return  $this->redirect(['site/error']);
        }
        $arr = explode('-',$getId);
        //获取数据
        $imgInfo = images::getOne($arr[0]);
        if(!$imgInfo){
            return $this->redirect(['site/error']);
        }
        $width = 0;
        $height = 0;
        $infos = explode('.',$arr[1]);
        if(strpos($infos[0],'w') !== false){
            $width = ltrim($infos[0],'w');
        }
        if(strpos($infos[0],'h') !== false){
            $height = ltrim($infos[0],'h');
        }
        $ext = $infos[1];
        //判断浏览器是否支持webp格式
        $webp = strpos($_SERVER['HTTP_ACCEPT'], 'image/webp');
        if($webp === false && $ext == 'webp'){ //不支持随便给个
            $ext = 'png';
        }
        //先获取图片
        $obj = new Uploader();
        $getImg = $obj->getThumb($width,$height,$ext,$imgInfo);
        if(!$getImg){
            //生成缩略图
            $setThumb = $obj->thumb($imgInfo);
            if(!$setThumb){
                return $this->redirect(['site/error']);
            }
            $getImg = $obj->getThumb($width,$height,$ext,$imgInfo);
        }
        $imagine = new Imagine();
        return $imagine->open($getImg)
            ->show($ext);
        //return $this->render('index');
    }

}
