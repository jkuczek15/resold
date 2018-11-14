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

namespace Plumrocket\GDPR\Model\Account\Processors;

use Plumrocket\GDPR\Api\DataProcessorInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class AbstractProcessor
 */
abstract class AbstractProcessor implements DataProcessorInterface
{
    // @codingStandardsIgnoreStart
    /**
     * need for each child that will be extended from this class
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $dataExport = [];

    /**
     * @var array
     */
    protected $dataAnonymize = [];

    // @codingStandardsIgnoreEnd

    /**
     * AbstractProcessor constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $dataExport
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $dataExport = [],
        array $dataAnonymize = []
    ) {
        $this->objectManager = $objectManager;
        $this->dataExport = $dataExport;
        $this->dataAnonymize = $dataAnonymize;
    }

    /**
     * Supported module version
     *
     * can be:
     *  core
     *  extended
     *  [1.2.3]
     *  [1.2.3-1.3.5]
     *  [1.2.3,1.2.4-1.3.5]
     *
     * @return array
     */
    abstract public function getSupportedVersions();
}
