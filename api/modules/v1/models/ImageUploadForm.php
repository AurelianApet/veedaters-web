<?php

namespace api\modules\v1\models;

use yii\base\Model;
use yii\helpers\Url;
use yii\web\UploadedFile;

class ImageUploadForm extends Model {
    public $temp_images;
    public $file_context;
    public $response;
    
    public function rules() {
        return [
            [['temp_images'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg, png, jpeg', 'maxFiles' => 15, 'mimeTypes' => 'image/*']
        ];
    }
    
    public function upload($fname = '_shop') {
        if(!$this->validate()) {
            return null;
        }
        
        $response = array();
        foreach($this->temp_images as $tmpImage) {
            $filename = microtime(true).rand(0000,1000). $fname;
            if(isset($this->file_context)){
                $filename .= '_'.$this->file_context.'.jpg';
            }else{
                $filename .= '.jpg';
            }
            if(!file_exists(\Yii::getAlias('@uploads') . '/temp/')) {
                mkdir(\Yii::getAlias('@uploads') . '/temp/', 0777, true);
                chmod(\Yii::getAlias('@uploads') . '/temp/' . $filename, 0777);
            }             
            $tmpImage->saveAs(\Yii::getAlias('@uploads') . '/temp/' . $filename);
            chmod(\Yii::getAlias('@uploads') . '/temp/' . $filename, 0777);
            $response[] = [
                'originalName' => $tmpImage->name,
                'savedName' => $filename,
                'previewUrl' => Url::to(['imagine/resize', 
                    'ImageBtv' => [
                        'type' => 'uploads',
                        'width' => 150,
                        'height' => 150,
                        'image_path' => 'temp/' . $filename
                    ]
                ]) 
            ];
        } 
        $this->response = $response;
        return true;
    }
}