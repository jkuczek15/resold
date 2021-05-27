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

namespace Plumrocket\GDPR\Model\Account\Processors\Plumrocket;

use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Model\Account\Processors\AbstractProcessor;
use Plumrocket\SocialLoginPro\Helper\Data as DataHelper;
use Plumrocket\SocialLoginPro\Model\ResourceModel\Account\Collection as AccountCollection;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Processor for Plumrocket SocialLoginPro.
 */
class SocialLoginPro extends AbstractProcessor
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $file;

    /**
     * SocialLoginPro constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param array $dataExport
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $file,
        array $dataExport = []
    ) {
        $this->filesystem = $filesystem;
        $this->file = $file;
        parent::__construct($objectManager, $dataExport);
    }

    /**
     * Supported module version
     * can be:
     *  core
     *  extended
     *  [1.2.3]
     *  [1.2.3-1.3.5]
     *  [1.2.3,1.2.4-1.3.5]
     *
     * @return array
     */
    public function getSupportedVersions()
    {
        return ['2.0.0-2.4.0'];
    }

    /**
     * Executed upon exporting customer data.
     *
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function export(CustomerInterface $customer)
    {
        $customerId = $customer->getId();
        $returnData = [];
        $i=0;

        $accountCollection = $this->objectManager->create(AccountCollection::class);
        $dataHelper = $this->objectManager->create(DataHelper::class);
        if (! $accountCollection || ! $dataHelper) {
            return;
        }

        $collection = $accountCollection->addFieldToFilter('customer_id', $customerId);

        if (! $collection->getSize()) {
            return;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($collection as $item) {
            $itemData = $item->getData();
            $itemData['image'] = $dataHelper->getPhotoPath(false, $customerId, $itemData['type']);

            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = (isset($itemData[$key]) ? $itemData[$key] : '');
            }

            $i++;
        }

        return $returnData;
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function delete(CustomerInterface $customer)
    {
        $accountCollection = $this->objectManager->create(AccountCollection::class);
        if (! $accountCollection) {
            return;
        }

        $customerId = $customer->getId();
        $collection = $accountCollection->addFieldToFilter('customer_id', $customerId);
        $mediaRootDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $accountModel = $this->objectManager->get(\Plumrocket\SocialLoginPro\Model\Account::class);
        $fileExt = $accountModel::PHOTO_FILE_EXT;
        $DS = DIRECTORY_SEPARATOR;

        if (! $collection->getSize()) {
            return;
        }

        foreach ($collection as $item) {
            $path = 'pslogin' . $DS .'photo' . $DS . $item['type'] . $DS . $customerId . '.' . $fileExt;
            if ($this->file->isExists($mediaRootDir . $path)) {
                $this->file->deleteFile($mediaRootDir . $path);
            }
        }

        $path = 'pslogin' . $DS . 'photo' . $DS . $customerId . '.' . $fileExt;
        if ($this->file->isExists($mediaRootDir . $path)) {
            $this->file->deleteFile($mediaRootDir . $path);
        }

        $collection->walk('delete');
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function anonymize(CustomerInterface $customer)
    {
        return null;
    }
}
