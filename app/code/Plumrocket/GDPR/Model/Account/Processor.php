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

namespace Plumrocket\GDPR\Model\Account;

use Magento\Framework\Model\AbstractModel;
use Plumrocket\GDPR\Api\DataProcessorInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Archive\Zip;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Export customer data.
 */
class Processor extends AbstractModel
{
    const INTEGRATED_PREFIX = 'prgdpr_';

    const CORE_PREFIX = 'Magento_';

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Csv
     */
    protected $csvWriter;

    /**
     * @var Zip
     */
    protected $zip;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var array
     */
    protected $processors;

    /**
     * Export constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param File $file
     * @param Csv $csvWriter
     * @param Zip $zip
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param array $processors
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        DateTime $dateTime,
        FileFactory $fileFactory,
        File $file,
        Csv $csvWriter,
        Zip $zip,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        array $processors = []
    ) {
        parent::__construct($context, $registry);

        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
        $this->file = $file;
        $this->csvWriter = $csvWriter;
        $this->zip = $zip;
        $this->moduleList = $moduleList;
        $this->processors = $processors;
    }

    /**
     * This function return .zip file with customer data.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function exportData(CustomerInterface $customer)
    {
        $date = $this->getDateStamp();
        $zipFileName = 'customer_data_' . $date . '.zip';
        $processors = $this->combineProcessors($this->processors);

        foreach ($processors as $processorData) {
            $processor = $processorData['processor'];
            $file = $processorData['file'];
            $dataExport = $processor->export($customer);

            if (!is_array($file)) {
                $file = ['key1' => $file];
                $dataExport = ['key1' => $dataExport];
            }

            foreach ($file as $key => $name) {
                $fileName = $name . '_' . $date . '.csv';
                if (isset($dataExport[$key])) {
                    $this->createFile($fileName, $dataExport[$key]);
                    $this->zip->pack($fileName, DirectoryList::PUB . DIRECTORY_SEPARATOR . $zipFileName);
                    $this->deleteFile($fileName);
                }
            }
        }

        $this->fileFactory->create(
            $zipFileName,
            [
                'type' => 'filename',
                'value' => $zipFileName,
                'rm' => true,
            ],
            DirectoryList::PUB,
            'zip',
            null
        );
    }

    /**
     * Process data deletion or anonymization.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function deleteData(CustomerInterface $customer)
    {
        $processors = $this->combineProcessors($this->processors);
        foreach ($processors as $processorData) {
            $processor = $processorData['processor'];
            $processor->delete($customer);
        }
    }

    /**
     * Create .csv file.
     *
     * @param string $fileName
     * @param array $data
     *
     * @return void
     */
    private function createFile($fileName, $data)
    {
        if (!$data) {
            return null;
        }

        $this->csvWriter
            ->setEnclosure('"')
            ->setDelimiter(',')
            ->saveData($fileName, $data);
    }

    /**
     * Delete .csv file.
     *
     * @param string $fileName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function deleteFile($fileName)
    {
        if ($this->file->isExists($fileName)) {
            $this->file->deleteFile($fileName);
        }
    }

    /**
     * Return current date.
     *
     * @return false|string
     */
    private function getDateStamp()
    {
        return date('Y-m-d_H-i-s', $this->dateTime->gmtTimestamp());
    }

    public function combineProcessors($data)
    {
        $processorsArray = ['external' => [], 'integrated' => [], 'core' => []];
        $processors = [];

        foreach ($data as $key => $value) {
            // check module exist and enabled
            $moduleName = str_replace(self::INTEGRATED_PREFIX, '', $key);
            $fileName = $key;

            if (is_array($value)) {
                if (!empty($value['module_name'])) {
                    $moduleName = $value['module_name'];
                }
                if (!empty($value['export_file_name'])) {
                    $fileName = $value['export_file_name'];
                }
            }

            if (! $this->moduleList->has($moduleName)) {
                continue;
            }

            $processor = $this->getProcessor($value);

            if (null !== $processor) {
                $processorData = ['file' => $fileName, 'processor' => $processor];

                if ($this->isIntegrated($key) && $this->checkVersion($moduleName, $processor)) {
                    $processorsArray['integrated'][$moduleName] = $processorData;
                } elseif ($this->isCore($moduleName)) {
                    $processorsArray['core'][$key] = $processorData;
                } else {
                    $processorsArray['external'][$moduleName] = $processorData;
                }

                $processorData = null;
            }
        }

        foreach ($processorsArray as $data) {
            foreach ($data as $name => $processorData) {
                if (! array_key_exists($name, $processors)) {
                    $processors[$name] = $processorData;
                }
            }
        }

        return $processors;
    }

    /**
     * @param $data
     * @return mixed|null
     */
    private function getProcessor($data)
    {
        if ($data instanceof DataProcessorInterface) {
            return $data;
        } elseif (is_array($data)
            && ! empty($data['processor'])
            && $data['processor'] instanceof DataProcessorInterface
        ) {
            return $data['processor'];
        }

        return null;
    }

    /**
     * @param $moduleName
     * @param $processor
     * @return bool
     */
    public function checkVersion($moduleName, $processor)
    {
        if (method_exists($processor, 'getSupportedVersions')) {
            $module = $this->moduleList->getOne($moduleName);
            $moduleVersion = $module['setup_version'];
            $supportedVersions = $processor->getSupportedVersions();

            foreach ($supportedVersions as $value) {
                $dashPos = strpos($value, "-");

                if (false !== $dashPos && $dashPos > 0) {
                    $version = explode("-", $value);

                    if (version_compare($moduleVersion, $version[0], '>=')
                        && version_compare($moduleVersion, $version[1], '<=')
                    ) {
                        return true;
                    }
                } elseif (version_compare($moduleVersion, $value, '=')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isIntegrated($key)
    {
        return ((strpos($key, self::INTEGRATED_PREFIX) === false) ? false : true);
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function isCore($moduleName)
    {
        return ((strpos($moduleName, self::CORE_PREFIX) === false) ? false : true);
    }
}
