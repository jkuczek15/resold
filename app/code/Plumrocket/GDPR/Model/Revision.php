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

namespace Plumrocket\GDPR\Model;

/**
 * Class Revision
 *
 * @method null|int getRevisionId()
 * @method \Plumrocket\GDPR\Model\Revision setRevisionId($revisionId)
 * @method null|int getCmsPageId()
 * @method \Plumrocket\GDPR\Model\Revision setCmsPageId($revisionId)
 * @method null|boolean getEnableRevisions()
 * @method \Plumrocket\GDPR\Model\Revision setEnableRevisions($value)
 * @method null|boolean getNotifyViaPopup()
 * @method \Plumrocket\GDPR\Model\Revision setNotifyViaPopup($value)
 * @method \Plumrocket\GDPR\Model\Revision setDocumentVersion($version)
 * @method null|string getPopupContent()
 * @method \Plumrocket\GDPR\Model\Revision setPopupContent($version)
 */
class Revision extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Default value for version filed
     */
    const DEFAULT_VERSION = '1.0';

    /**
     * Version field name
     */
    const KEY_DOCUMENT_VERSION = 'document_version';

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()// @codingStandardsIgnoreLine we need to extend parent method
    {
        $this->_init(\Plumrocket\GDPR\Model\ResourceModel\Revision::class);
    }

    /**
     * @return string
     */
    public function getDocumentVersion()
    {
        if (null === $this->getData(self::KEY_DOCUMENT_VERSION)) {
            $this->setData(self::KEY_DOCUMENT_VERSION, self::DEFAULT_VERSION);
        }

        return (string)$this->getData(self::KEY_DOCUMENT_VERSION);
    }
}
