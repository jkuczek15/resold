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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsMarketplace\Helper;
 
class Image extends Data
{

    public $data = [];

    public function UploadImage($attributes = false) 
    {
        if (!$attributes) {
            $imagefields = ['profile_picture','company_logo','company_banner'];
            foreach($imagefields as $fieldName) {
                $this->UploadImagebyName($fieldName);
            }
        } else {
            foreach($attributes as $attribute) {
                if ($attribute->getFrontendInput() == 'file')
                {
                    $this->UploadFilebyName($attribute->getAttributeCode());
                } else {
                    $this->UploadImagebyName($attribute->getAttributeCode());
                }
            }
        }
        return $this->data;
    }

    public function UploadImagebyName($fieldName) 
    {
        $vendorPost = $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getParam('vendor');
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = $mediaDirectory->getAbsolutePath('ced/csmaketplace/vendor');
        $imagePath = false;
        try {
            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => "vendor[{$fieldName}]"));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
            $uploader->setAllowRenameFiles(false);                 
            $uploader->setFilesDispersion(false);
            $fileData = $uploader->validateFile(); 
            $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
            $fileName = $fieldName.time().'.'.$extension;
            $flag = $uploader->save($path, $fileName);
            $imagePath = true;
            $this->data[$fieldName] = 'ced/csmaketplace/vendor/'.$fileName;
        } catch(\Exception $e) {

        }
        if (!$imagePath) {
            if (isset($vendorPost[$fieldName]['delete']) && $vendorPost[$fieldName]['delete'] == 1) {                    
                $this->data[$fieldName] = '';
                $imageName = explode('/', $vendorPost[$fieldName]['value']);
                $imageName = $imageName[count($imageName)-1];
                unlink($path.'/'.$imageName);
            } else {
                unset($this->data[$fieldName]);
            }
        }
    }

    public function UploadFilebyName($fieldName)
    {
        $vendorPost = $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getParam('vendor');
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = $mediaDirectory->getAbsolutePath('ced/csmaketplace/vendor');
        $imagePath = false;
        try {
            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => "vendor[{$fieldName}]"));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','pdf','doc','docx')); // or pdf or anything
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $fileData = $uploader->validateFile();
            $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
            $fileName = $fieldName.time().'.'.$extension;
            $flag = $uploader->save($path, $fileName);
            $imagePath = true;
            $this->data[$fieldName] = 'ced/csmaketplace/vendor/'.$fileName;
        } catch(\Exception $e) {

        }
        if (!$imagePath) {
            if (isset($vendorPost[$fieldName]['delete']) && $vendorPost[$fieldName]['delete'] == 1) {
                $this->data[$fieldName] = '';
                $imageName = explode('/', $vendorPost[$fieldName]['value']);
                $imageName = $imageName[count($imageName)-1];
                unlink($path.'/'.$imageName);
            } else {
                unset($this->data[$fieldName]);
            }
        }
    }
    
}
