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
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GeoIPLookup\Model\Data\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Plumrocket\GeoIPLookup\Helper\Config;
use Magento\Framework\App\ResourceConnection;

class AbstractModel
{
    /**
     * @var int
     */
    public $steps = 1;

    /**
     * @var int
     */
    public $step = 1;

    /**
     * @var string
     */
    public $dataName = '';

    /**
     * @var string
     */
    public $xmlPathSourceFile = '';

    /**
     * @var string
     */
    public $fileName = '';

    /**
     * @var array
     */
    public $dataMapping = [];

    /**
     * @var string
     */
    public $tableName = '';

    /**
     * @var string
     */
    public $dataLabel = '';

    /**
     * @var string
     */
    public $pathSourceFile = '';

    /**
     * @var string
     */
    public $pathVersionsFile = Config::PATH_VERSION_FILE;

    /**
     * @var string
     */
    public $versionsFileName = 'versions.csv';

    /**
     * @var string
     */
    public $progressFileName = 'progress.csv';

    /**
     * @var null
     */
    public $installedVersions = null;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csv;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Plumrocket\GeoIPLookup\Model\InstalledVersions
     */
    private $installedVersionsModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * AbstractModel constructor.
     *
     * @param \Magento\Framework\Filesystem\Io\File           $ioFile
     * @param \Magento\Framework\Filesystem                   $filesystem
     * @param \Magento\Framework\File\Csv                     $csv
     * @param \Magento\Framework\Stdlib\DateTime\DateTime     $date
     * @param ResourceConnection                              $resourceConnection
     * @param \Plumrocket\GeoIPLookup\Model\InstalledVersions $installedVersionsModel
     * @param \Plumrocket\GeoIPLookup\Helper\Data             $dataHelper
     * @param \Magento\Framework\HTTP\Client\Curl             $curl
     */
    public function __construct(
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Plumrocket\GeoIPLookup\Model\InstalledVersions $installedVersionsModel,
        \Plumrocket\GeoIPLookup\Helper\Data $dataHelper,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->ioFile = $ioFile;
        $this->filesystem = $filesystem;
        $this->csv = $csv;
        $this->resourceConnection = $resourceConnection;
        $this->installedVersionsModel = $installedVersionsModel;
        $this->date = $date;
        $this->dataHelper = $dataHelper;
        $this->curl = $curl;
    }

    /**
     * @param $url
     * @return float|int
     */
    public function retrieveRemoteFileSize($url)
    {
        $contentLength = 0;
        $this->curl->setOptions([
            CURLOPT_NOBODY         => true,
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        $this->curl->get($url);
        $data = $this->curl->getBody();

        if ($data) {
            $status = 404;
            if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches)) {
                $status = (int)$matches[1];
            }
            if (preg_match("/Content-Length: (\d+)/", $data, $matches)
                && $status == 200 || ($status > 300 && $status <= 308)
            ) {
                $contentLength = (int)$matches[1];
            }
        }

        return $contentLength;
    }

    /**
     * @return array
     */
    public function autoImportData()
    {
        set_time_limit(0);
        $errorMessage = null;
        $copyFile = false;

        $importProgress = [
            'status' => 'process',
            'total' => 100,
            'exec' => 1,
            'step' => $this->step,
            'message' => __('Download in Progress...'),
            'remote_file_size' => $this->retrieveRemoteFileSize($this->pathSourceFile),
            'local_file_name' => $this->getDataFile()
        ];

        $this->setProgress($importProgress);

        if ($this->ioFile->fileExists($this->getDataFile(), true)) {
            $copyFile = true;
        } else {
            $this->ioFile->setAllowCreateFolders(true);
            if ($this->ioFile->createDestinationDir($this->getDataPath())) {
                $copyFile = $this->ioFile->cp($this->getPathSourceFile(), $this->getDataFile());
            }
        }

        if ($copyFile) {
            $result = $this->importData();
        } else {
            $lastError = error_get_last();
            $result = ['status' => 'fail', 'message' => $lastError['message']];
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getPathSourceFile()
    {
        if (!empty($this->pathSourceFile)) {
            $this->pathSourceFile .= '?t=' . $this->date->gmtTimestamp();
        }
        return $this->pathSourceFile;
    }

    /**
     * @return array|bool
     */
    public function manualImportData()
    {
        set_time_limit(0);
        $importProgress = [
            'status' => 'process',
            'total' => 100,
            'exec' => 1,
            'step' => $this->step,
            'message' => __('Installation in Progress...')
        ];
        $this->setProgress($importProgress);

        $isDataFileValid = $this->isDataFileValid($this->getDataFile());
        if ($isDataFileValid === true) {
            $result = $this->importData();
        } else {
            $result = $isDataFileValid;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function importData()
    {
        ini_set('memory_limit','4096M');
        $process = false;
        $s = 5000;
        $totalItems = 0;
        $totalExecItems = 0;
        $allCsvData = $this->csv->getData($this->getDataFile());

        if (!empty($allCsvData)) {
            $totalItems = count($allCsvData);
            $connection = $this->resourceConnection->getConnection(
                ResourceConnection::DEFAULT_CONNECTION
            );
            $connection->truncateTable(
                $this->resourceConnection->getTableName($this->tableName)
            );
            $csvDataChunk = array_chunk($allCsvData, $s, true);

            foreach ($csvDataChunk as $csvData) {
                $importData = [];
                $totalExecItems += count($csvData);
                foreach ($csvData as $i => $item) {
                    if ($item[0][0] == '#' || !((int)$item[0][0])) {
                        continue;
                    }

                    foreach ($this->dataMapping as $key => $index) {
                        $importData[$i][$key] = $this->dataMapping($key, $item[$index]);
                    }
                }

                if (!empty($importData) && !empty($this->tableName)) {
                    $connection->insertMultiple(
                        $this->resourceConnection->getTableName($this->tableName),
                        $importData
                    );
                    $process = true;
                    $importProgress = [
                        'status'  => 'process',
                        'total'   => $totalItems,
                        'exec'    => $totalExecItems,
                        'step'    => $this->step,
                        'message' => __('Importing GeoIP Database... ')
                    ];
                    $this->setProgress($importProgress);
                } else {
                    $process = false;
                    break;
                }
            }
        }

        if ($process) {
            $this->saveAutomaticInstalledVersion();
            if ($this->steps == 1) {
                $this->ioFile->rm($this->getDataFile());
            }
            if ($this->step == $this->steps) {
                $importProgress = [
                    'status' => 'success',
                    'total' => $totalItems,
                    'exec' => $totalExecItems,
                    'step' => $this->step,
                    'message' => $this->dataHelper->formatInstalledVersion($this->getInstalledVersion())
                ];
                $this->setProgress($importProgress);
            }
            $result = ['status' => 'process', 'total' => 100, 'exec' => 100, 'message' => __('Done!')];
        } else {
            $result = ['status' => 'fail', 'message' => __('Data Import Error')];
        }

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function dataMapping($key, $value)
    {
        return $value;
    }

    /**
     * @param $file
     * @return array|bool
     */
    public function isDataFileValid($file)
    {
        if ($this->ioFile->fileExists($file, true)) {
            return true;
        } else {
            $status = 'fail';
            if ($this->isLatestInstalledVersion()) {
                $status = 'success';
            }

            return [
                'status'  => $status,
                'error'   => __('GeoIP CSV Files Missing:'),
                'alert'   => __(
                    "We couldn't find %1 CSV files in 'media/prgeoiplookup' folder.
                    \nPlease read our online manual for more info about manual installation method.",
                    $this->getDataName()
                ),
                'message' => $this->dataHelper->formatInstalledVersion($this->getInstalledVersion())
            ];
        }
    }

    /**
     * @param $importProgress
     */
    public function setProgress($importProgress)
    {
        $this->ioFile->setAllowCreateFolders(true);
        if ($this->ioFile->createDestinationDir($this->getDataPath())) {
            $this->csv->saveData(
                $this->getProgressFile(),
                [$importProgress]
            );
        }
    }

    /**
     * @return array
     */
    public function getProgress()
    {
        if ($this->ioFile->fileExists($this->getProgressFile(), true)) {
            $csvData = $this->csv->getData($this->getProgressFile());

            if (isset($csvData[0][6])
                && isset($csvData[0][5])
                && $this->ioFile->fileExists($csvData[0][6])
            ) {
                $total = $csvData[0][5];
                $exec = filesize($csvData[0][6]);
            } else {
                $total = (int)$csvData[0][1];
                $exec = (int)$csvData[0][2];
            }

            $importProgress = [
                'status' => $csvData[0][0],
                'total' => $total,
                'exec' => $exec,
                'step' => (int)$csvData[0][3],
                'message' => $csvData[0][4]
            ];
        } else {
            $importProgress = [
                'status' => 'process',
                'total' => 100,
                'exec' => 1,
                'step' => $this->step,
                'message' => __('Download in Progress...')
            ];
        }

        return $importProgress;
    }

    /**
     * @return mixed
     */
    public function getInstalledVersion()
    {
        if (null == $this->installedVersions || empty($this->installedVersions[$this->dataName])) {
            $collection = $this->installedVersionsModel->getCollection();
            foreach ($collection->getItems() as $item) {
                $this->installedVersions[$item['data_name']] = $item->getData();
            }
        }

        return (isset($this->installedVersions[$this->dataName]))
            ? $this->installedVersions[$this->dataName]
            : null;
    }

    /**
     *
     */
    public function deleteInstalledVersion()
    {
        $version = $this->installedVersionsModel
            ->load($this->dataName, 'data_name');
        if (!empty($version->getData())) {
            $version->delete();
        }
    }

    /**
     * @return bool|mixed
     */
    public function isLatestInstalledVersion()
    {
        $result = true;
        $installedVersion = $this->getInstalledVersion();
        if (!$installedVersion) {
            return false;
        }

        $existVersion = null;
        $copyFile = false;
        $this->ioFile->setAllowCreateFolders(true);
        if ($this->ioFile->createDestinationDir($this->getDataPath())) {
            $copyFile = $this->ioFile->cp($this->pathVersionsFile, $this->getVersionsFile());
        }
        if ($copyFile) {
            $csvData = $this->csv->getData($this->getVersionsFile());
            foreach ($csvData as $data) {
                if ($data[0] == $this->dataName) {
                    $existVersion = [
                        'data_name'         => $data[0],
                        'file_version'      => $data[1],
                        'file_date'         => $data[2]
                    ];
                    break;
                }
            }

            $this->ioFile->rm($this->getVersionsFile());
            if ($existVersion) {
                $result = version_compare($installedVersion['file_version'], $existVersion['file_version'], '>=');
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function saveAutomaticInstalledVersion()
    {
        if ($this->step != $this->steps) {
            return false;
        }

        $insertData = null;
        $copyFile = false;
        $this->ioFile->setAllowCreateFolders(true);
        if ($this->ioFile->createDestinationDir($this->getDataPath())) {
            $copyFile = $this->ioFile->cp($this->pathVersionsFile, $this->getVersionsFile());
        }
        if ($copyFile) {
            $csvData = $this->csv->getData($this->getVersionsFile());
            foreach ($csvData as $data) {
                if ($data[0] == $this->dataName) {
                    $insertData = [
                        'data_name'      => $data[0],
                        'file_version'   => $data[1],
                        'installed_date' => $this->date->gmtDate()
                    ];
                    break;
                }
            }
        }

        if ($insertData) {
            $version = $this->installedVersionsModel->load($insertData['data_name'], 'data_name');

            if (!empty($version->getData())) {
                $version->addData($insertData)->save();
            } else {
                $version->setData($insertData)->save();
            }
        }

        $this->ioFile->rm($this->getVersionsFile());
    }

    /**
     * @return string
     */
    public function getDataName()
    {
        return $this->dataName;
    }

    /**
     * @return string
     */
    public function getDataLabel()
    {
        return $this->dataLabel;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getDataPath()
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath(Config::LOCAL_PATH);
    }

    /**
     * @return string
     */
    public function getDataFile()
    {
        return $this->getDataPath() . $this->fileName;
    }

    /**
     * @return string
     */
    public function getVersionsFile()
    {
        return $this->getDataPath() . $this->versionsFileName;
    }

    /**
     * @return string
     */
    public function getProgressFile()
    {
        return $this->getDataPath() . $this->progressFileName;
    }

    /**
     * @return \Magento\Framework\Filesystem\Io\File
     */
    public function getIoFileModel()
    {
        return $this->ioFile;
    }

}