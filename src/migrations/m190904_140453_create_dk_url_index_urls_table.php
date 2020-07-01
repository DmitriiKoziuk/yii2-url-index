<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_url_index_urls}}`.
 */
class m190904_140453_create_dk_url_index_urls_table extends Migration
{
    private string $urlIndexUrlsTableName = '{{%dk_url_index_urls}}';
    private string $urlIndexModulesTableName = '{{%dk_url_index_modules}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->urlIndexUrlsTableName, [
            'id'              => $this->primaryKey(),
            'module_id'       => $this->integer()->notNull(),
            'entity_id'       => $this->integer()->unsigned()->notNull(),
            'url'             => $this->string(255)->notNull(),
            'redirect_to_url' => $this->integer()->null()->defaultValue(NULL),
            'created_at'      => $this->integer()->unsigned()->notNull(),
            'updated_at'      => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'dk_url_index_urls_idx_module_id',
            $this->urlIndexUrlsTableName,
            'module_id'
        );
        $this->createIndex(
            'dk_url_index_urls_idx_module_entity',
            $this->urlIndexUrlsTableName,
            [
                'module_id',
                'entity_id',
            ]
        );
        $this->createIndex(
            'dk_url_index_urls_uidx_url',
            $this->urlIndexUrlsTableName,
            'url',
            true
        );
        $this->createIndex(
            'dk_url_index_urls_idx_redirect_to_url',
            $this->urlIndexUrlsTableName,
            'redirect_to_url'
        );
        $this->addForeignKey(
            'dk_url_index_urls_fk_module_id',
            $this->urlIndexUrlsTableName,
            'module_id',
            $this->urlIndexModulesTableName,
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'dk_url_index_urls_fk_redirect_to_url',
            $this->urlIndexUrlsTableName,
            'redirect_to_url',
            $this->urlIndexUrlsTableName,
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
        $this->dropForeignKey('dk_url_index_urls_fk_module_id', $this->urlIndexUrlsTableName);
        $this->dropForeignKey('dk_url_index_urls_fk_redirect_to_url', $this->urlIndexUrlsTableName);
        $this->dropTable($this->urlIndexUrlsTableName);
    }
}
