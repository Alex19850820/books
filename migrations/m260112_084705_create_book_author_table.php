<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_author}}`.
 */
class m260112_084705_create_book_author_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);

        // Устанавливаем первичный ключ (составной)
        $this->addPrimaryKey(
            'pk-book_author', 
            'book_author',
            ['book_id', 'author_id']
        );

          // Добавляем внешние ключи с ON DELETE CASCADE
        $this->addForeignKey(
            'fk-book_author-book_id',
            'book_author',
            'book_id',
            'book',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-book_author-author_id',
            'book_author',
            'author_id',
            'author',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         // Удаляем внешние ключи (порядок важен: сначала FK, потом PK)
        $this->dropForeignKey('fk-book_author-book_id', 'book_author');
        $this->dropForeignKey('fk-book_author-author_id', 'book_author');

        // Удаляем таблицу
        $this->dropTable('book_author');
    }
}
