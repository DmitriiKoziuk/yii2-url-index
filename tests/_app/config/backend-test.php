<?php

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../../../../../common/config/test-local.php',
    require __DIR__ . '/../../../../../../backend/config/main.php',
    require __DIR__ . '/../../../../../../backend/config/main-local.php',
    require __DIR__ . '/../../../../../../backend/config/test.php',
    [
        'language' => 'en',
    ]
);