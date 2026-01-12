<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscription}}`.
 */
class m260112_084714_create_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(20)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Добавляем внешний ключ на таблицу author
        $this->addForeignKey(
            'fk-subscription-author_id',
            'subscription',
            'author_id',
            'author',
            'id',
            'CASCADE'
        );

        // Создаём уникальный индекс (author_id, phone)
        $this->createIndex(
            'idx-subscription-author_phone',
            'subscription',
            ['author_id', 'phone'],
            true // true = уникальный индекс
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         // Удаляем уникальный индекс
        $this->dropIndex('idx-subscription-author_phone', 'subscription');

        // Удаляем внешний ключ
        $this->dropForeignKey('fk-subscription-author_id', 'subscription');

        // Удаляем таблицу
        $this->dropTable('subscription');
    }
}
