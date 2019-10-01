<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_url_index_urls}}`.
 */
class m190904_140453_create_dk_url_index_urls_table extends Migration
{
    private $tableName = '{{%dk_url_index_urls}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
            'id'              => $this->primaryKey(),
            'url'             => $this->string(255)->notNull(),
            'redirect_to_url' => $this->integer()->null()->defaultValue(NULL),
            'module_name'     => $this->string(45)->defaultValue(NULL),
            'controller_name' => $this->string(45)->notNull(),
            'action_name'     => $this->string(45)->notNull(),
            'entity_id'       => $this->string(45)->notNull(),
            'created_at'      => $this->integer()->unsigned()->notNull(),
            'updated_at'      => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'dk_url_index_urls_url_idx',
            $this->tableName,
            'url',
            true
        );
        $this->createIndex(
            'dk_url_index_urls_entity_idx',
            $this->tableName,
            [
                'module_name',
                'controller_name',
                'action_name',
                'entity_id',
            ]
        );
        $this->createIndex(
            'dk_url_index_urls_ redirect_to_url_idx',
            $this->tableName,
            'redirect_to_url'
        );
        $this->addForeignKey(
            'dk_url_index_urls_redirect_to_url_fk',
            $this->tableName,
            'redirect_to_url',
            $this->tableName,
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('dk_url_index_urls_redirect_to_url_fk', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
