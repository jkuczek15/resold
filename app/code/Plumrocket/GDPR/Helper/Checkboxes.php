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

namespace Plumrocket\GDPR\Helper;

use Plumrocket\GDPR\Model\Config\Source\ConsentAction;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * Class Checkboxes
 */
class Checkboxes extends \Magento\Framework\App\Helper\AbstractHelper
{
    const POPUP_CLASSNAME = 'pr-inpopup';

    /**
     * @var array
     */
    private $checkboxes = null;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Geo\Location
     */
    private $geoLocationHelper;

    /**
     * @var \Plumrocket\GDPR\Model\Config\Source\ConsentLocations
     */
    private $consentLocations;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog
     */
    private $consentsLogResource;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory
     */
    private $consentsLogCollectionFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory
     */
    private $revisionCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    private $context;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepositoryInterface;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $cookiesConsentActions = [
        \Magento\Cookie\Helper\Cookie::IS_USER_ALLOWED_SAVE_COOKIE,
        \Plumrocket\GDPR\Helper\Data::IS_USER_DECLINE_SAVE_COOKIE,
    ];

    /**
     * Checkboxes constructor.
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper
     * @param \Plumrocket\GDPR\Model\Config\Source\ConsentLocations $consentLocations
     * @param \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog $consentsLogResource
     * @param \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory $consentsLogCollectionFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $revisionCollectionFactory
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper,
        \Plumrocket\GDPR\Model\Config\Source\ConsentLocations $consentLocations,
        \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog $consentsLogResource,
        \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory $consentsLogCollectionFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $revisionCollectionFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface
    ) {
        $this->dataHelper = $dataHelper;
        $this->geoLocationHelper = $geoLocationHelper;
        $this->consentLocations = $consentLocations;
        $this->consentsLogResource = $consentsLogResource;
        $this->consentsLogCollectionFactory = $consentsLogCollectionFactory;
        $this->revisionCollectionFactory = $revisionCollectionFactory;
        $this->context = $context;
        $this->dateTime = $dateTime;
        $this->remoteAddress = $content->getRemoteAddress();
        $this->filterManager = $filterManager;
        $this->storeManager = $storeManager;
        $this->currentCustomer = $currentCustomer;
        $this->subscriberFactory = $subscriberFactory;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->logger = $context->getLogger();
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isSubscribedCurrentCustomer()
    {
        $customerId = $this->currentCustomer->getCustomerId();

        if (! $customerId) {
            return false;
        }

        $subscriber = $this->subscriberFactory->create();

        return $subscriber->loadByCustomerId($customerId)->isSubscribed();
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCheckboxesConfig($store = null)
    {
        return (string)$this->dataHelper->getConfig(
            $this->dataHelper->getConfigSectionId() . '/consent_checkboxes/checkboxes',
            $store
        );
    }

    /**
     * @param null $store
     * @return array
     */
    public function getCheckboxesConfigArray($store = null)
    {
        $result = json_decode($this->getCheckboxesConfig($store), true);

        return is_array($result) ? $result : [];
    }

    /**
     * @param $page
     * @return bool
     */
    public function canShowCheckboxes($page = null)
    {
        if (! $this->dataHelper->moduleEnabled()) {
            return false;
        }

        if (ConsentLocations::NEWSLETTER == (string)$page
            && $this->isSubscribedCurrentCustomer()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param null $page
     * @param int | string $store
     * @param bool $withAlreadyChecked
     * @param bool $checkVersion
     * @return array
     */
    public function getCheckboxes(
        $page = null,
        $store = null,
        $withAlreadyChecked = false,
        $checkVersion = true
    ) {
        if (! $this->canShowCheckboxes($page)) {
            return [];
        }

        if (null === $this->checkboxes) {
            $this->checkboxes = [];

            if ($checkboxesConfig = $this->getCheckboxesConfigArray($store)) {
                foreach ($checkboxesConfig as $key => $data) {
                    // validate Geo Location
                    if (! $this->geoLocationHelper->isPassCheckboxGeoIPRestriction($data)) {
                        continue;
                    }

                    // Prepare Main Data
                    $this->prepareDataForCheckbox($key, $data);
                    // Prepare Page Data
                    $this->preparePageDataForCheckbox($data);

                    // only after page data loading for checkbox
                    if (! $withAlreadyChecked && $this->isAlreadyChecked($data, $checkVersion)) {
                        continue;
                    }

                    $this->checkboxes[$data['page_type']][$key] = $data;
                }
            }
        }

        if ($page) {
            return array_key_exists($page, $this->checkboxes) ? $this->checkboxes[$page] : [];
        }

        $result = [];

        foreach ($this->checkboxes as $pageType => $checkboxes) {
            $result += $checkboxes;
        }

        return $result;
    }

    /**
     * @param $id
     * @param $data
     */
    public function prepareDataForCheckbox($id, &$data)
    {
        $data['consentId'] = $id;
        $data['checkboxLabel'] = $data['checkbox_label'];
        $data['agreeUrl'] = $this->_getUrl(
            'prgdpr/consentpopups/confirm',
            ['_secure' => true]
        );
        $data['is_required'] = isset($data['is_required'])
            ? (bool)$data['is_required']
            : true;
        $data['cms_page'] = null;
    }

    /**
     * @param $data
     * @return void
     */
    public function preparePageDataForCheckbox(&$data)
    {
        $pageId = ! empty($data['page_id']) ? (int)$data['page_id'] : false;

        if ($pageId) {
            try {
                /** @var \Magento\Cms\Api\Data\PageInterface $cmsPage */
                $cmsPage = $this->pageRepositoryInterface->getById($pageId);
                /** @var \Plumrocket\GDPR\Model\ResourceModel\Revision\Collection $revisionCollection */
                $revisionCollection = $this->revisionCollectionFactory->create();
                $documentVersion = $revisionCollection->getRevisionByPageId($pageId)
                    ->getData('document_version');
                $data['cms_page'] = [
                    'title' => $cmsPage->getTitle(),
                    'url' => $this->_getUrl(
                        $cmsPage->getIdentifier(),
                        ['_secure' => true]
                    ),
                    'content' => $cmsPage->getContent(),
                    'version' => $documentVersion ? $documentVersion : ''
                ];
                $data['checkboxLabel'] = str_replace(
                    "{{url}}",
                    $data['cms_page']['url'],
                    $data['checkbox_label']
                );

                if (false !== strpos($data['checkboxLabel'], self::POPUP_CLASSNAME)) {
                    $searchStr = 'class=';
                    $replaceStr = 'data-bind="attr: {\'data-checkboxid\':'
                        . ' $parent.getCheckboxId($parentContext, consentId)}, '
                        . 'click: function(data, event) '
                        . '{return $parent.showContent(data, event);}" class=';
                    $data['checkboxLabel'] = str_replace($searchStr, $replaceStr, $data['checkboxLabel']);
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $data['checkboxLabel'] = str_replace(
                "{{url}}",
                'JavaScript:void(0);',
                $data['checkbox_label']
            );
        }
    }

    /**
     * @param $checkbox
     * @param $checkVersion
     * @return bool
     */
    public function isAlreadyChecked($checkbox, $checkVersion)
    {
        if ($customerId = $this->currentCustomer->getCustomerId()) {
            /** @var \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\Collection $consentsLogCollection */
            $consentsLogCollection = $this->consentsLogCollectionFactory->create()
                ->addFieldToFilter('customer_id', ['eq' => $customerId]);

            if ($checkbox['cms_page']) {
                $consentsLogCollection->addFieldToFilter('cms_page_id', ['eq' => $checkbox['page_id']]);
                if ($checkVersion) {
                    $consentsLogCollection->addFieldToFilter('version', ['eq' => $checkbox['cms_page']['version']]);
                }
            } else {
                $consentsLogCollection->addFieldToFilter(
                    'label',
                    ['eq' => $this->filterManager->stripTags($checkbox['checkbox_label'])]
                );
            }

            if ($consentsLogCollection->getSize()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $consentIds
     * @param null $page
     * @param null $store
     * @param bool $withAlreadyChecked
     * @param bool $checkVersion
     * @return bool
     */
    public function isValidConsents(
        $consentIds,
        $page = null,
        $store = null,
        $withAlreadyChecked = false,
        $checkVersion = true
    ) {
        $checkboxes = $this->getCheckboxes($page, $store, $withAlreadyChecked, $checkVersion);

        if (empty($checkboxes)) {
            return true;
        }

        if (! is_array($consentIds)) {
            return false;
        }

        foreach ($checkboxes as $id => $data) {
            if ($data['is_required'] && ! in_array($id, $consentIds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $location
     * @return bool
     */
    public function isValidLocation($location)
    {
        $locations = $this->consentLocations->toOptionAssocArray();

        return array_key_exists($location, $locations);
    }

    /**
     * @return int
     */
    public function getCurrentCustomerId()
    {
        return (int)$this->currentCustomer->getCustomerId();
    }

    /**
     * @return int
     */
    public function getCurrentWebsiteId()
    {
        return (int)$this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * @return string
     */
    public function getCurrentRemoteAddress()
    {
        return (string)$this->remoteAddress->getRemoteAddress();
    }

    /**
     * @return int
     */
    public function getGmtTimestamp()
    {
        return $this->dateTime->gmtTimestamp();
    }

    /**
     * @param null $timestamp
     * @return string
     */
    public function getFormattedGmtDateTime($timestamp = null)
    {
        if (null === $timestamp) {
            $timestamp = $this->getGmtTimestamp();
        }

        return (string)date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * @param $column
     * @return mixed
     */
    public function getBaseDataByColumn($column)
    {
        $result = null;

        switch ($column) {
            case 'created_at':
                $result = $this->getFormattedGmtDateTime();
                break;
            case 'customer_id':
                $result = $this->getCurrentCustomerId();
                break;

            case 'website_id':
                $result = $this->getCurrentWebsiteId();
                break;

            case 'customer_ip':
                $result = $this->getCurrentRemoteAddress();
                break;
            case 'action':
                $result = ConsentAction::ACTION_ACCEPT_VALUE;
                break;
        }

        return $result;
    }

    /**
     * @param null $forceBaseData
     * @return array
     */
    public function getBaseDataForConsent($forceBaseData = null)
    {
        $result = [];
        $columns = [
            'created_at',
            'customer_id',
            'website_id',
            'customer_ip',
            'action',
        ];

        foreach ($columns as $column) {
            $result[$column] = isset($forceBaseData[$column])
                ? $forceBaseData[$column]
                : $this->getBaseDataByColumn($column);
        }

        return $result;
    }

    /**
     * @param $location
     * @param $consentIds
     * @param null $forceBaseData
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveMultipleConsents($location, $consentIds, $forceBaseData = null)
    {
        if (! $this->dataHelper->moduleEnabled()
            || ! is_array($consentIds)
        ) {
            return 0;
        }

        if (! $this->isValidLocation($location)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Undefined location for specified consents.')
            );
        }

        $baseRowData = $this->getBaseDataForConsent($forceBaseData);

        if (! $baseRowData['customer_id']) {
            if (isset($forceBaseData['customer_id'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Undefined Customer ID for specified consents.')
                );
            }

            return 0;
        }

        $insertData = [];
        $checkboxes = $this->getCheckboxesConfigArray();

        foreach ($consentIds as $consentId) {
            if (! is_string($consentId) && ! is_numeric($consentId)) {
                continue;
            }

            if (array_key_exists($consentId, $checkboxes)) {
                $checkboxData = $checkboxes[$consentId];

                if (! isset($checkboxData['page_type'])
                    || $location !== $checkboxData['page_type']
                ) {
                    continue;
                }

                $this->prepareDataForCheckbox($consentId, $checkboxData);
                $this->preparePageDataForCheckbox($checkboxData);

                // Prepare row data
                $rowData = $baseRowData;
                $rowData['location'] = (string)$location;
                $rowData['label'] = (string)$this->filterManager->stripTags($checkboxData['checkbox_label']);
                $rowData['cms_page_id'] = (int)$checkboxData['page_id'];
                $rowData['version'] = null;

                if ($rowData['cms_page_id']) {
                    $rowData['version'] = ! empty($checkboxData['cms_page']['version'])
                        ? (string)$checkboxData['cms_page']['version']
                        : '';
                }

                $insertData[] = $rowData;
            }
        }

        return ! empty($insertData) ? $this->consentsLogResource->saveMultipleConsents($insertData) : 0;
    }

    /**
     * @param $action
     * @return bool
     */
    public function isValidCookiesConsentAction($action)
    {
        return in_array($action, $this->cookiesConsentActions);
    }

    /**
     * @param $action
     * @return string
     */
    public function getCookiesConsentButtonLabel($action)
    {
        return \Magento\Cookie\Helper\Cookie::IS_USER_ALLOWED_SAVE_COOKIE == $action
            ? $this->dataHelper->getAcceptCookieButtonLabel()
            : $this->dataHelper->getDeclineCookieButtonLabel();
    }

    /**
     * @param $action
     * @param null $label
     * @return \Magento\Framework\Phrase|string
     */
    public function getDecoratedCookiesConsentLabel($action, $label = null)
    {
        $defaultLabel = \Magento\Cookie\Helper\Cookie::IS_USER_ALLOWED_SAVE_COOKIE == $action
            ? __('Accept Cookies')
            : __('Decline Cookies');

        if (empty($label)) {
            return $defaultLabel;
        }

        return $defaultLabel == $label
            ? $defaultLabel
            : __('%1 (%2)', $label, $defaultLabel);
    }

    /**
     * @param $action
     * @return int|null
     */
    public function getCookiesConsentActionValue($action)
    {
        $result = null;

        switch (strtolower($action)) {
            case \Magento\Cookie\Helper\Cookie::IS_USER_ALLOWED_SAVE_COOKIE:
                $result = ConsentAction::ACTION_ACCEPT_VALUE;
                break;
            case \Plumrocket\GDPR\Helper\Data::IS_USER_DECLINE_SAVE_COOKIE:
                $result = ConsentAction::ACTION_DECLINE_VALUE;
                break;
        }

        return $result;
    }

    /**
     * @param $action
     * @param null $params
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveCookiesConsent($action, $params = null)
    {
        $action = strtolower($action);

        if (! $this->isValidCookiesConsentAction($action)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid consent action. Please provide valid log action.')
            );
        }

        // Base data for consent
        $consent = $this->getBaseDataForConsent($params);

        if (! empty($consent['customer_id'])) {
            $consent['action'] = $this->getCookiesConsentActionValue($action);
            $consent['label'] = ! empty($params['label'])
                ? (string)$params['label']
                : $this->getDecoratedCookiesConsentLabel($action);
            $consent['location'] = ConsentLocations::COOKIE;

            return $this->consentsLogResource->saveMultipleConsents([$consent]);
        }

        return 0;
    }
}
