<?php
/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $status
 * @property string $desc
 * @property string $file_path
 * @property integer $add_time
 * @property integer $update_time
 * @property integer $width
 * @property integer $height
 * @property string $mime
 * @property string $md5
 * @property integer $size
 * @property integer $dynamic
 */

namespace common\models;


use common\components\Utility;
use wallpaper\models\Album;
use wallpaper\models\AlbumImgRel;
use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use Imagine\Image\ManipulatorInterface;

/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $status
 * @property string $desc
 * @property string $file_path
 * @property integer $add_time
 * @property integer $update_time
 * @property integer $width
 * @property integer $height
 * @property string $mime
 * @property string $md5
 * @property integer $size
 * @property integer $dynamic
 */

class Image extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_MAP = [
        self::STATUS_INACTIVE => "不可用",
        self::STATUS_ACTIVE => "可用",
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'add_time', 'update_time', 'width', 'height', 'size', 'dynamic'], 'integer'],
            [['file_path', 'md5'], 'required'],
            [['desc', 'file_path', 'mime', 'md5'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'desc' => 'Desc',
            'file_path' => 'File Path',
            'add_time' => 'Add Time',
            'update_time' => 'Update Time',
            'width' => '宽',
            'height' => '高',
            'mime' => 'Mime',
            'md5' => 'Md5',
            'size' => 'Size',
            'dynamic' => 'Dynamic',
        ];
    }



    public function getSid() {
        return Utility::sid($this->id);
    }

    public function getDotExt() {
        return Utility::fileExt($this->mime);
    }



    public function fields()
    {
        $fields = parent::fields();
        unset($fields['id'], $fields['status'], $fields['add_time'], $fields['update_time'], $fields['file_path'], $fields['desc']);
        $fields[] = 'sid';
        $fields[] = 'dotExt';
//        $fields[] = 'album';
        return $fields;
    }



    public static function imgPath($ext) {
        $path = date('Ym', time()) . '/' . date('Ymd', time()) . '/';
        $filePath = yii::$app->params['imgDir'] . $path . Utility::getRandString(32) . '.' . $ext;
        if (!file_exists($filePath)) {
            @mkdir(dirname($filePath), 0777, true);
        }
        return $filePath;
    }

    public static function url($sid, $width= 0, $height = 0, $mode = 1) {
        if (empty($sid)) {
            throw new HttpException(404,'The resource cannot be found.');
        }
        $model = static::findOne(Utility::id($sid));
        if (empty($model)) {
            throw new HttpException(404,'The resource cannot be found.');
        }
        if (
            !file_exists(Yii::$app->params["imgDir"] . $model->file_path) &&
            ($content = file_get_contents('http://db.appcq.cn/thumb/' . $sid . '/1.' . Utility::fileExt($model->mime)))
        ) {
            @mkdir(dirname(Yii::$app->params["imgDir"] . $model->file_path), 0777, true);
            @chmod(dirname(Yii::$app->params["imgDir"] . $model->file_path), 0777);
            @chmod(dirname(dirname(Yii::$app->params["imgDir"] . $model->file_path)), 0777);
            file_put_contents(Yii::$app->params["imgDir"] . $model->file_path, $content);
        }

        return self::thumb($model->file_path, $width, $height, $mode);
    }


    private static function thumb($origin, $width = 0, $height = 0, $mode = 0) {
        if ($width || $height) {
            $thumbPath = self::thumbPath($origin, $width, $height, $mode);
            if (file_exists($thumbPath[0] . $thumbPath[1])) {
                return $thumbPath[0] . $thumbPath[1];
            }
            if (!file_exists($thumbPath[0])) {
                mkdir($thumbPath[0], 0777, true);
            }
            if (\yii\imagine\Image::thumbnail(\Yii::$app->params["imgDir"] . $origin,
                $width,
                $height,
                $mode ? ManipulatorInterface::THUMBNAIL_OUTBOUND : ManipulatorInterface::THUMBNAIL_INSET)
                ->save($thumbPath[0] . $thumbPath[1], ['quality' => 80])) {
                return $thumbPath[0] . $thumbPath[1];
            } else {
                return false;
            }
        } else {
            return Yii::$app->params["imgDir"] . $origin;
        }


    }

    private static function thumbPath($origin, $width, $height, $type){
        $pathInfo = pathinfo($origin);
        $path = $pathInfo["dirname"] . "/";
        $extension = $pathInfo["extension"];
        $fileName = $pathInfo["filename"] . "_{$width}_{$height}_{$type}.$extension";
        Yii::info("img thumb file path >>> " . $fileName);
        $dirName = yii::$app->params['imgDir'] . "thumbs/" . $path;
        return [$dirName, $fileName];
    }

}
