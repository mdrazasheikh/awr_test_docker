<?php

namespace Awr\AutoTrustImporter\Controller\Index;

use Awr\AutoTrustImporter\Model\ImportVehicle;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        /**
         * @var $model ImportVehicle
         */
        $model = $this->_objectManager->create('Awr\AutoTrustImporter\Model\ImportVehicle');
        $model->save();
    }
}
