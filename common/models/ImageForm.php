<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/7
 * Time: 16:53
 */

namespace common\models;


use common\components\Utility;
use yii\base\Model;

class ImageForm extends Model
{
    public $url;
    public $imageFiles;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['url'], 'required'],
            // rememberMe must be a boolean value
            ['url', 'string'],
            //[['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 5],
        ];
    }

    private function copy($url, $imgPath)
    {
        if (!file_exists($imgPath)) {
            @mkdir(dirname($imgPath), 0777, true);
            $ch = curl_init($url);
            if ($ch) {
                $fp = fopen($imgPath, 'wb');
                if ($fp) {
                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    $ret = curl_exec($ch);
                    curl_close($ch);
                    fclose($fp);
                    if (!file_exists($imgPath)) {
                        return false;
                    }
                    return true;
                }
                return false;
            }
            return false;
        } else {
            return true;
        }
    }

	private function copyToTmp($url)
    {
		$tmp = '/tmp/' . Utility::getRandString(32);
		$ch = curl_init($url);
		if ($ch) {
			$fp = fopen($tmp, 'wb');
			if ($fp) {
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				$ret = curl_exec($ch);
				curl_close($ch);
				fclose($fp);
				if (!file_exists($tmp)) {
					return false;
				}
				return $tmp;
			}
			return false;
        }
        return false;
    }

    private function mv($tmp, $imgPath)
    {
        if (!file_exists(dirname($imgPath))) {
            @mkdir(dirname($imgPath), 0777, true);
        }
        return rename($tmp, $imgPath);
    }


	/*
	 * todo 获取图片执行了三次
     */
    public function save()
    {
		$tmpfile = $this->copyToTmp($this->url);
		if (empty($tmpfile)) {
			return false;
		}
        $imageInfo = getimagesize($tmpfile);
        if ($imageInfo['mime'] == null) {
            return false;
        }
        $path = date('Ym') . '/' . date('d') . '/';
        $file = Utility::getRandString(32) . Utility::fileExt($imageInfo['mime']);

        if (!$this->mv($tmpfile, \Yii::$app->params['imgDir'] . $path . $file)) {
            $this->addError('', '文件拷贝错误');
            return false;
        }
        $imgContent = file_get_contents(\Yii::$app->params['imgDir'] . $path . $file);
        $fileSize = strlen($imgContent);

        $dynamic = 0;
        if ('image/gif' == $imageInfo['mime']) {
            $dynamic = strpos($imgContent, chr(0x21) . chr(0xff) . chr(0x0b) . 'NETSCAPE2.0') === FALSE ? 0 : 1;
        }
        $md5 = md5($imgContent);
        $img = Image::findOne(['md5' => $md5]);
        if (!empty($img)) {
            return $img;
        }
        $img = new Image();
        $img->add_time = time();
        $img->update_time = time();
        $img->file_path = $path . $file;
        $img->width = $imageInfo[0];
        $img->height = $imageInfo[1];
        $img->mime = $imageInfo['mime'];
        $img->md5 = $md5;
        $img->status = 1;
        $img->size = $fileSize;
        $img->dynamic = $dynamic;
        if (!$img->save()) {
            $this->addErrors($img->getErrors());
            return false;
        }
        return $img;

    }


    public function upload($upload)
    {
        $imageInfo = getimagesize($upload->tempName);
        $imgContent = file_get_contents($upload->tempName);
        if ($imageInfo['mime'] == null) {
            return false;
        }
        $path = date('Ym') . '/' . date('d') . '/';
        $file = Utility::getRandString(32) . Utility::fileExt($imageInfo['mime']);

        if (!file_exists(\Yii::$app->params['imgDir'] . $path . $file)) {
            @mkdir(dirname(\Yii::$app->params['imgDir'] . $path . $file), 0777, true);
        }

        if (!$upload->saveAs(\Yii::$app->params['imgDir'] . $path . $file)) {
            $this->addError('', '文件拷贝错误');
            return false;
        }

        $fileSize = $upload->size;    // Consider HTTP Code 302

        $dynamic = 0;
        if ('image/gif' == $imageInfo['mime']) {
            $dynamic = strpos($imgContent, chr(0x21) . chr(0xff) . chr(0x0b) . 'NETSCAPE2.0') === FALSE ? 0 : 1;
        }
        $md5 = md5($imgContent);
        $img = Image::findOne(['md5' => $md5]);
        if (!empty($img)) {
            return $img;
        }
        $img = new Image();
        $img->add_time = time();
        $img->update_time = time();
        $img->file_path = $path . $file;
        $img->width = $imageInfo[0];
        $img->height = $imageInfo[1];
        $img->mime = $imageInfo['mime'];
        $img->md5 = $md5;
        $img->status = 1;
        $img->size = $fileSize;
        $img->dynamic = $dynamic;
        if (!$img->save()) {
            $this->addErrors($img->getErrors());
            return false;
        }
        return $img;

    }
    
    public function uploadMultiple($imgFiles){
        if(empty($imgFiles)){
            $this->addErrors(['上传图片为空']);
            return false;
        }
        $imgs = [];
        foreach($imgFiles as $imgFile){
            $img = $this->upload($imgFile);
            if(empty($img)){
                return false;
            }
            $imgs[] = $img;
        }
        
        return $imgs;
    }

}
