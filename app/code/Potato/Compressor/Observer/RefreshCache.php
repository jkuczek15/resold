<?php
namespace Potato\Compressor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Potato\Compressor\Helper\Data as DataHelper;
use Potato\Compressor\Helper\File as FileHelper;
use Magento\Framework\Cache\Core as CoreCache;

class RefreshCache implements ObserverInterface
{
    /** @var CoreCache */
    protected $coreCache;
    
    /** @var FileHelper  */
    protected $fileHelper;
    
    /** @var DataHelper  */
    protected $dataHelper;

    /**
     * RefreshCache constructor.
     * @param FileHelper $fileHelper
     * @param DataHelper $dataHelper
     * @param CoreCache $coreCache
     */
    public function __construct(
        FileHelper $fileHelper,
        DataHelper $dataHelper,
        CoreCache $coreCache
    ) {
        $this->fileHelper = $fileHelper;
        $this->dataHelper = $dataHelper;
        $this->coreCache = $coreCache;
        $this->coreCache->setBackend(new \Zend_Cache_Backend_File());
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $this->coreCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [DataHelper::COMPRESSOR_CACHE_TAG]);
        $this->fileHelper->removeDirectory(
            $this->dataHelper->getRootCachePath(),
            [$this->dataHelper->getImageMergeCachePath()]
        );
        return $this;
    }
}