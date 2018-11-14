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

namespace Plumrocket\GDPR\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class RemovalActions
 */
class RemovalActions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /** Url path */
    const REMOVALREQUEST_URL_PATH_CANCEL = 'prgdpr/removalrequests/cancel';

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['request_id']) && $item['status'] == 'pending') {
                    $item[$this->getData('name')] = [
                        'cancel' => [
                            'href' => $this->urlBuilder->getUrl(
                                self::REMOVALREQUEST_URL_PATH_CANCEL,
                                ['request_id' => $item['request_id']]
                            ),
                            'label' => __('Cancel'),
                            'confirm' => [
                                'title' => __('Are You Sure?'),
                                'message' => __('Are you sure you want to cancel this removal request?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
