<?php

namespace ufoproger\fileattachment\models;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property string $group
 * @property string $name
 * @property string $filename
 * @property string $filepath
 * @property string $mime_type
 * @property integer $size
 * @property string $created_at
 * @property integer $created_by
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
                'updatedAtAttribute' => null,
            ],

            [
                'class' => 'yii\behaviors\BlameableBehavior',
                'updatedByAttribute' => null,
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'filename', 'filepath', 'size'], 'required'],
            ['size', 'integer'],
            [['name', 'filename'], 'string', 'max' => 255],
            ['filepath', 'string', 'max' => 1024],
            ['mime_type', 'string', 'max' => 128],
        ];
    }

    public function getUrl()
    {
        return Yii::getAlias(implode('/', [
            '@web',
            $this->filepath,
            $this->filename,
        ]));
    }

    public function getPath()
    {
        return Yii::getAlias(implode(DIRECTORY_SEPARATOR, [
            '@webroot',
            $this->filepath,
            $this->filename,
        ]));
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            // 'id' => Yii::t('site', 'ID'),
            // 'name' => Yii::t('site', 'Name'),
            // 'filename' => Yii::t('site', 'Filename'),
            // 'mime_type' => Yii::t('site', 'Mime Type'),
            // 'size' => Yii::t('site', 'Size'),
            // 'created_at' => Yii::t('site', 'Created At'),
            // 'created_by' => Yii::t('site', 'Created By'),
        ];
    }

    public function extraFields()
    {
        return ['url'/*, 'path'*/];
    }
}
