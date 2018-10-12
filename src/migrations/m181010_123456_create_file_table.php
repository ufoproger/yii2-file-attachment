<?php

/**
 * Handles the creation of table `file`.
 */
class m181010_123456_create_file_table extends \yii\db\Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'filename' => $this->string(255)->notNull(),
            'filepath' => $this->string(1024)->notNull(),
            'mime_type' => $this->string(255),
            'size' => $this->bigInteger()->notNull(),
            'created_at' => $this->integer()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
        ]);  
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%file}}');
    }
}
