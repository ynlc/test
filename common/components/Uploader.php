<?php
namespace common\components;

use Yii;
use yii\base\Component;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;
use yii\base\ErrorException;

class Uploader extends Component
{
    private $dir = '/upload/images/';
    /**
     * 生成图片缩略图
     * @param $filename 文件名
     * @param $ext 后缀名
     * @param $sizeType 类型
     * @return string
     */
    public function thumb($img)
    {
        if(!$img){
            return false;
        }
        $types = ['jpg','jpeg','webp'];
        $sizes = $this->calculation($img['width'],$img['height']);
        $filepath = Yii::getAlias('@frontend') .'/web'. $this->dir;
        $tfile = $filepath.$img['image_name'];
        $imagine = new Imagine();
        $image = $imagine->open($tfile);
        try {
            foreach ($types as $val) {
                foreach ($sizes as $v){
                    $thumbName = $img['image_id'].'-w'.$v['width'].$val;
                    $image->resize(new Box(15, 25))
                        ->rotate(45)
                        ->crop(new Point(0, 0), new Box($v['width'], $v['width']))
                        ->save($filepath.$thumbName);
                }
            }
            return true;
        } catch (ErrorException $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * 获取缩略图
     * @param int $width
     * @param int $height
     * @param $ext
     * @param $img
     */
    public function getThumb($width=0,$height=0,$ext,$img)
    {
        if($width == 0 && $height == 0){
            return $img['l_url'];
        }
        $size = $this->getLatelyValue($width, $height,$img);
        if($width > 0){
            $file = Yii::getAlias('@frontend') .'/web'. $this->dir.$img['image_id'].'-w'.$size.'.'.$ext;
        }else{
            $file = Yii::getAlias('@frontend') .'/web'. $this->dir.$img['image_id'].'-h'.$size.'.'.$ext;
        }
        if(file_exists($file)){
            return $file;
        }
        return false;
    }

    /**
     * 计算距级
     * @param int $width 图片宽度
     * @param int $height 图片高度
     * @return array
     */
    public function calculation($width=0,$height=0)
    {
        if(!$width || !$height){
            return false;
        }
        $arr = [];
        $minWidth = intval($width/10);
        $minHeight = intval($height/10);
        for($i=1;$i<=10;$i++){
            $width = $i*$minWidth;
            $height = $i*$minHeight;
            $arr[] = ['width'=>$width,'height'=>$height];
        }
        return $arr;
    }

    /**
     * 获取最近的值
     * @param int $width
     * @param int $height
     * @param array $img 图片信息
     */
    public function getLatelyValue($width = 0, $height=0,$img)
    {
        $newArr = [];
        //计算最近的值
        if ($width > 0) {
            $maxSize = $width;
            $data = $this->calculation($img['width'], $img['height']);
            $arr = array_column($data, 'width');
        }
        if ($height > 0) {
            $maxSize = $height;
            $data = $this->calculation($img['width'], $img['height']);
            $arr = array_column($data, 'height');
        }
        $count = count($arr);
        for ($i = 0; $i < $count; $i++) {
            $arr2[] = abs($maxSize - $arr[$i]);
        }

        $min = min($arr2);
        for ($i = 0; $i < $count; $i++) {
            if ($min == $arr2[$i]) {
                $newArr[] = $arr[$i];
            }
        }
        return max($newArr);
    }
}