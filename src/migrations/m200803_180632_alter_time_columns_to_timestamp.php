<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2UrlIndex\migrations;

use yii\db\Migration;

class m200803_180632_alter_time_columns_to_timestamp extends Migration
{
    private string $urlIndexModulesTableName = '{{%dk_url_index_modules}}';
    private string $urlIndexUrlsTableName = '{{%dk_url_index_urls}}';

    public function safeUp()
    {
        $this->dropColumn(
            $this->urlIndexModulesTableName,
            'created_at'
        );
        $this->dropColumn(
            $this->urlIndexModulesTableName,
            'updated_at'
        );
        $this->dropColumn(
            $this->urlIndexUrlsTableName,
            'created_at'
        );
        $this->dropColumn(
            $this->urlIndexUrlsTableName,
            'updated_at'
        );

        $this->addColumn(
            $this->urlIndexModulesTableName,
            'created_at',
            $this->timestamp()->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP')
        );
        $this->addColumn(
            $this->urlIndexModulesTableName,
            'updated_at',
            $this->timestamp()->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->append('ON UPDATE CURRENT_TIMESTAMP')
        );
        $this->addColumn(
            $this->urlIndexUrlsTableName,
            'created_at',
            $this->timestamp()->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP')
        );
        $this->addColumn(
            $this->urlIndexUrlsTableName,
            'updated_at',
            $this->timestamp()->notNull()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->append('ON UPDATE CURRENT_TIMESTAMP')
        );
    }

    public function safeDown()
    {
        $this->dropColumn(
            $this->urlIndexModulesTableName,
            'created_at'
        );
        $this->dropColumn(
            $this->urlIndexModulesTableName,
            'updated_at'
        );
        $this->dropColumn(
            $this->urlIndexUrlsTableName,
            'created_at'
        );
        $this->dropColumn(
            $this->urlIndexUrlsTableName,
            'updated_at'
        );

        $this->addColumn(
            $this->urlIndexModulesTableName,
            'created_at',
            $this->integer()->unsigned()->notNull()
        );
        $this->addColumn(
            $this->urlIndexModulesTableName,
            'updated_at',
            $this->integer()->unsigned()->notNull()
        );
        $this->addColumn(
            $this->urlIndexUrlsTableName,
            'created_at',
            $this->integer()->unsigned()->notNull()
        );
        $this->addColumn(
            $this->urlIndexUrlsTableName,
            'updated_at',
            $this->integer()->unsigned()->notNull()
        );
    }
}
