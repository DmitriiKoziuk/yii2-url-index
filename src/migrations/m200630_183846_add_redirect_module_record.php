<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\migrations;

use yii\db\Migration;

use DmitriiKoziuk\yii2UrlIndex\UrlIndexModule;

/**
 * Class m200630_183846_add_redirect_module_record
 */
class m200630_183846_add_redirect_module_record extends Migration
{
    private string $urlIndexModulesTableName = '{{%dk_url_index_modules}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            $this->urlIndexModulesTableName,
            [
                'id' => 1,
                'module_name' => UrlIndexModule::getId(),
                'controller_name' => 'url',
                'action_name' => 'redirect',
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(
            $this->urlIndexModulesTableName,
            [
                'module_name' => UrlIndexModule::getId(),
                'controller_name' => 'url',
                'action_name' => 'redirect',
            ]
        );
    }
}
