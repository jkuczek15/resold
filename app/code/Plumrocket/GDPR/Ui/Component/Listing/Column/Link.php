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

class Link extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param $valueName
     * @param string $default
     * @return string
     */
    private function getConfigValue($valueName, $default = '')
    {
        $valueName = (string)$valueName;
        $config = $this->getData('config');

        return ($config && !empty($config[$valueName]))
            ? $config[$valueName]
            : $default;
    }

    private function getHrefConfig()
    {
        $data = [
            'path' => '*',
            'identifier' => 'entity_id',
            'source' => 'entity_id',
        ];

        $config = $this->getData('config');

        if ($config
            && isset($config['href'])
            && is_array($config['href'])
        ) {
            $hrefConfig = $config['href'];

            $data = array_merge($data, $hrefConfig);
        }

        return $data;
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
                $fieldName = $this->getData('name');
                $config = $this->getData('config');
                $hrefData = $this->getHrefConfig();
                if ($item['customer_exist']) {
                    $titleSource = ! empty($config['title']['source'])
                        ? (string)$config['title']['source']
                        : false;
                    $title = $titleSource ? $item[$titleSource] : $item[$fieldName];
                    $href = $this->urlBuilder->getUrl($hrefData['path'], [
                        $hrefData['identifier'] => $item[$hrefData['source']]
                    ]);
                    $item[$fieldName] = '<a href="' . $href . '">' . $this->escaper->escapeHtml($title) . '</a>';
                }
            }
        }

        return $dataSource;
    }
}
