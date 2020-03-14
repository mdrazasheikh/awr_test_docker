<?php

namespace Awr\AutoTrustImporter\Cron;

use Awr\AutoTrustImporter\Model\ImportVehicle;
use Magento\Framework\App\ObjectManager;

class Import
{

    protected $objectManager;

    public function __construct()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    public function execute()
    {
        /**
         * @var $model ImportVehicle
         */
        $model = $this->objectManager->create('Awr\AutoTrustImporter\Model\ImportVehicle');
        $model->save();
    }
}
