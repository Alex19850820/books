<?php

use yii\db\Migration;

class m250514_130041_create_url_qr_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('url_qr_code', [
            'id' => $this->primaryKey(),
            'href' => $this->string()->notNull(),
            'qr_code' => $this->text()
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('url_qr_code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250514_130041_create_url_qr_code cannot be reverted.\n";

        return false;
    }
    */
}
