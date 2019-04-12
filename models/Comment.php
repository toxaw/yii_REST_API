<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $post_id
 * @property string $author
 * @property string $comment
 * @property string $datatime
 * @property int $rating
 *
 * @property Post $post
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'author', 'comment'], 'required'],
            /*rating*/
            [['post_id', 'rating'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['datetime'], 'safe'],
            [['author'], 'string', 'max' => 255],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_id' => 'Post ID',
            'author' => 'Author',
            'comment' => 'Comment',
            'datetime' => 'Datetime',
            'rating' => 'Rating',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    public function create()
    {
        if ($this->validate()) 
        {       
            $this->datetime = Yii::$app->formatter->asDate('now', 'php:Y-m-d H:i:s');

            $this->post->rating_sum = $this->post->rating_sum + $this->rating;
            
            $this->save(false);
            
            $count = count($this->post->comments);

            $this->post->rating = $this->post->rating_sum / $count;

            $this->post->save(false);

            return $this->getPrimaryKey();      
        } 
        else 
        {
            return false;
        }
    }
}
