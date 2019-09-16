<?php

namespace DmitriiKoziuk\yii2UrlIndex\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_url_index_urls}}`.
 */
class m190904_140453_create_dk_url_index_urls_table extends Migration
{
    private $dkUrlIndexesTableName = '{{%dk_url_index_urls}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->dkUrlIndexesTableName, [
            'id'              => $this->primaryKey(),
            'url'             => $this->string(255)->notNull(),
            'redirect_to_url' => $this->string(255)->null()->defaultValue(NULL),
            'module_name'     => $this->string(45)->defaultValue(NULL),
            'controller_name' => $this->string(45)->notNull(),
            'action_name'     => $this->string(45)->notNull(),
            'entity_id'       => $this->string(45)->notNull(),
            'created_at'      => $this->integer()->unsigned()->notNull(),
            'updated_at'      => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'dk_url_index_urls_url_idx',
            $this->dkUrlIndexesTableName,
            'url',
            true
        );
        $this->createIndex(
            'dk_url_index_urls_entity_idx',
            $this->dkUrlIndexesTableName,
            [
                'module_name',
                'controller_name',
                'action_name',
                'entity_id',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->dkUrlIndexesTableName);
    }
}
