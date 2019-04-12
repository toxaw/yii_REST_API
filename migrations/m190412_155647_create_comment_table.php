<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%post}}`
 */
class m190412_155647_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'author' => $this->string()->notNull(),
            'comment' => $this->text()->notNull(),
            'datetime' => $this->datetime(),
            'rating' => $this->integer()->notNull(),
        ]);

        // creates index for column `post_id`
        $this->createIndex(
            '{{%idx-comment-post_id}}',
            '{{%comment}}',
            'post_id'
        );

        // add foreign key for table `{{%post}}`
        $this->addForeignKey(
            '{{%fk-comment-post_id}}',
            '{{%comment}}',
            'post_id',
            '{{%post}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%post}}`
        $this->dropForeignKey(
            '{{%fk-comment-post_id}}',
            '{{%comment}}'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            '{{%idx-comment-post_id}}',
            '{{%comment}}'
        );

        $this->dropTable('{{%comment}}');
    }
}
