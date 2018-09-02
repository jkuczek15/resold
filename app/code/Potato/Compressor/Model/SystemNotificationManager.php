<?php
namespace Potato\Compressor\Model;

use Potato\Compressor\Helper\Data as DataHelper;

class SystemNotificationManager
{
    /** @var DataHelper */
    protected $dataHelper;

    protected $list = [];

    /**
     * @param DataHelper $dataHelper
     */
    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function getMessageList()
    {
        $this->list = [];
        $this->checkFolderPermission();
        return $this->list;
    }

    /**
     * @return $this
     */
    protected function checkFolderPermission()
    {
        $path = $this->dataHelper->getRootCachePath();
        if (!is_writable($path)) {
            $this->list[] = __('Invalid permissions for folder: %1', $this->dataHelper->getRootCachePath());
        }
        return $this;
    }
}