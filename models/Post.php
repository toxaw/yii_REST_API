<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $anons
 * @property string $text
 * @property string $image
 * @property string $date
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $imageFile;
    
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text', 'anons'], 'string'],
            [['date'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxSize' => 1024 * 1024 * 2],
            [['title','anons', 'text'], 'required', 'message' => 'is empty'],
            ['title', 'unique',  'message' => 'is exists'],
           // [''] уникальность тайтла
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'anons' => 'anons',
            'text' => 'text',
            'image' => 'image',
            'date' => 'date',
        ];
    }

    public function create()
    {
        if ($this->validate()) 
        {
            $fileName = $this->image->baseName . '.' . $this->image->extension;

            $this->image->saveAs(Yii::getAlias('@imagePath'). $fileName);

            $this->date = Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s');

            $this->save(false);

            return $this->getPrimaryKey();
        } 
        else 
        {
            return false;
        }
    }
}
