<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Plugin\Magento\Contact\Controller\Index;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

class PostPlugin
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxesHelper;

    /**
     * PostPlugin constructor.
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper
     */
    public function __construct(
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->checkboxesHelper = $checkboxesHelper;
    }

    /**
     * @param \Magento\Contact\Controller\Index\Post $subject
     * @param null|\Magento\Framework\Controller\Result\Redirect $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Contact\Controller\Index\Post $subject,
        $result
    ) {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $subject->getRequest();
        $postValue = $request->getPostValue();
        $dataPersistorValue = $this->dataPersistor->get('contact_us');

        if (! empty($postValue) && empty($dataPersistorValue)) {
            $this->checkboxesHelper->saveMultipleConsents(
                ConsentLocations::CONTACT_US,
                $request->getParam('consent')
            );
        }

        return $result;
    }
}
