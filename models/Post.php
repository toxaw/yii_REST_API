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
            [['datetime'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxSize' => 1024 * 1024 * 2],
            [['title','anons', 'text'], 'required', 'message' => 'is empty'],
            ['title', 'unique', 'on' => 'create',  'message' => 'is exists'],
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
            'datetime' => 'datetime',
        ];
    }

    public function create()
    {
        if ($this->validate()) 
        {
            $this->image->name = time() . $this->image->name; 

            $fileName = $this->image->baseName . '.' . $this->image->extension;

            $this->image->saveAs(Yii::getAlias('@imagePath') . $fileName);

            if($this->IsNewRecord)
            {                
                $this->datetime = Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s');
            }
            else
            {   
                @unlink(Yii::getAlias('@imagePath') . $this->findOne($this->id)->image);
            }

            $this->save(false);

            return $this->getPrimaryKey();      
        } 
        else 
        {
            return false;
        }
    }
}
