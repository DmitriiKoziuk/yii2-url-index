<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dk_url_index_urls}}`.
 */
class m190904_140452_create_dk_url_index_modules_table extends Migration
{
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
        $this->createTable($this->urlIndexModulesTableName, [
            'id'              => $this->primaryKey(),
            'module_name'     => $this->string(45)->notNull()->defaultValue(''),
            'controller_name' => $this->string(45)->notNull(),
            'action_name'     => $this->string(45)->notNull(),
            'created_at'      => $this->integer()->unsigned()->notNull(),
            'updated_at'      => $this->integer()->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'dk_url_index_modules_uidx_full',
            $this->urlIndexModulesTableName,
            [
                'module_name',
                'controller_name',
                'action_name',
            ],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->urlIndexModulesTableName);
    }
}
