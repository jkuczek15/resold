<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_CsMarketplace
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsMarketplace\Model\System\Config\Backend\LoginSignup;
 
class Logo extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * Save uploaded file before saving config value
     *
     * @return \Magento\Config\Model\Config\Backend\File
     */
    public function beforeSave()
    {
        $filevalue = $this->getValue();
        $tmpName = $this->_requestData->getTmpName($this->getPath());
        $file = [];
        if ($tmpName) {
            $file['tmp_name'] = $tmpName;
            $file['name'] = $this->_requestData->getName($this->getPath());
        } elseif (!empty($filevalue['tmp_name'])) {
            $file['tmp_name'] = $filevalue['tmp_name'];
            $file['name'] = $filevalue['name'];
        }
    
        if (isset($file['tmp_name'])) {

            $uploadDir = $this->_getUploadDir();
            try {
                $csUploader = $this->_uploaderFactory->create(['fileId' => $file]);
                $csUploader->setAllowedExtensions($this->_getAllowedExtensions());
                $csUploader->setAllowRenameFiles(true);
                $csUploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $csUploader->save($uploadDir);

            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
            $csFileName = $result['file'];
            if ($csFileName) {
                if ($this->_addWhetherScopeInfo()) {
                    $csFileName = $this->_prependScopeInfo($csFileName);
                }
                $this->setValue($csFileName);
            }
        } else {
            if (is_array($filevalue) && !empty($filevalue['delete'])) {
                $this->setValue('');
            } else {
                $this->unsValue();
            }
        }        
        return $this;
    }
}
