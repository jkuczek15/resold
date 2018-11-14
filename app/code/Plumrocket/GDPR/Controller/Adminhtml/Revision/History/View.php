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

namespace Plumrocket\GDPR\Controller\Adminhtml\Revision\History;

use Plumrocket\GDPR\Model\ResourceModel\Revision\History as HistoryResource;
use Magento\Framework\Controller\ResultFactory;

/**
 * Controller View Revision History
 */
class View extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Plumrocket_GDPR::prgdpr';

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    private $resultLayoutFactory;

    /**
     * @var \Plumrocket\GDPR\Model\Revision\HistoryFactory
     */
    private $historyFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision\History
     */
    private $historyResource;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Plumrocket\GDPR\Model\Revision\HistoryFactory $history
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\History $historyResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Plumrocket\GDPR\Model\Revision\HistoryFactory $historyFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\History $historyResource
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->historyFactory = $historyFactory;
        $this->historyResource = $historyResource;
        parent::__construct($context);
    }

    /**
     * Retrieve transaction grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response = ['messages' => []];

        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $this->getRequest();
        $historyId = (int)$request->getParam('history_id');

        if ($historyId) {
            try {
                $history = $this->historyFactory->create();
                $historyResource = $this->historyResource->load(
                    $history,
                    $historyId,
                    HistoryResource::MAIN_TABLE_ID_FIELD_NAME
                );

                if ($history->getId()) {
                    $response = [
                        'success' => true,
                        'messages' => [],
                        'data' => $history->getData(),
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'messages' => [
                            __('Specified revision version not found.')
                        ],
                    ];
                }
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $response = [
                    'success' => false,
                    'messages' => [$exception->getMessage()]
                ];
            } catch (\Exception $exception) {
                $response = [
                    'success' => false,
                    'messages' => [
                        __('Some error occurred while loading data. Please contact our technical support.')
                    ]
                ];
            }
        } else {
            $response = [
                'success' => false,
                'messages' => [
                    __('Bad Request. Required parameter \'history_id\'  not specified.')
                ]
            ];
        }

        return $resultJson->setData($response);
    }
}
