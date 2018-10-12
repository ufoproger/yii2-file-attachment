<?php
namespace ufoproger\fileattachment\components;

use Yii;

use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\base\InvalidConfigException;

use ufoproger\fileattachment\components\FileUpload;
use ufoproger\fileattachment\models\File;

class FileUploadAction extends \yii\base\Action
{
    public $allowedExtensions = [];
    public $uploadDir = null;

    public function init()
    {
        parent::init();

        Yii::$app->controller->enableCsrfValidation = false;    

        if (empty($this->uploadDir)) {
            throw new InvalidConfigException(
                "Invalid value for the property 'uploadDir'."
            );
        }

        if (empty($this->allowedExtensions)) {
            throw new InvalidConfigException(
                "Invalid value for the property 'allowedExtensions'."
            );
        }
    }

    public function run()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $destination = Yii::getAlias('@webroot' . DIRECTORY_SEPARATOR . $this->uploadDir);

        if (!is_dir($destination)) {
            FileHelper::createDirectory($destination);
        }


        $uploader = new FileUpload('file');
        // $uploader->newFileName = time() . $uploader->getFileName();
        $uploader->sizeLimit = 10 * 1024 * 1024;
        $uploader->allowedExtensions = (array)$this->allowedExtensions;

        do {
            $uploader->newFileName = Yii::$app->security->generateRandomString() . '.' . pathinfo($uploader->getFileName(), PATHINFO_EXTENSION);
        } while (file_exists($uploader->uploadDir . DIRECTORY_SEPARATOR . $uploader->newFileName));

        $name = $uploader->getFileName();
        $result = $uploader->handleUpload(Yii::getAlias('@webroot' . DIRECTORY_SEPARATOR . $this->uploadDir));

        if (!$result) {
            return [
                'success' => false,
                'message' => $uploader->getErrorMsg()
            ];
        }

        $model = new File([
            'name' => $name,
            'filename' => $uploader->getFileName(),
            'filepath' => $this->uploadDir,
            'size' => filesize($uploader->uploadDir . DIRECTORY_SEPARATOR . $uploader->newFileName),
            'mime_type' => FileHelper::getMimeType($uploader->uploadDir . DIRECTORY_SEPARATOR . $uploader->newFileName),
        ]);

        if (!$model->save()) {
            return [
                'success' => false,
                'message' => $model->getErrors()    
            ];
        }

        return [
            'success' => true,
            'file' => $model->toArray([], ['url'])
        ];
    }
}