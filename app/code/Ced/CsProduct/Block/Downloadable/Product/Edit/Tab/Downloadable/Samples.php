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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProduct\Block\Downloadable\Product\Edit\Tab\Downloadable;

class Samples extends \Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples
{
    /**
     * @var string
     */
    protected $urlBuilder;

    protected $_template = 'Ced_CsProduct::downloadable/product/edit/downloadable/samples.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Downloadable\Helper\File $downloadableFile,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Downloadable\Model\Sample $sampleModel,
        \Magento\Backend\Model\UrlFactory $urlFactory,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreFileStorageDb = $coreFileStorageDatabase;
        $this->_downloadableFile = $downloadableFile;
        $this->_coreRegistry = $coreRegistry;
        $this->_sampleModel = $sampleModel;
        $this->_urlFactory = $urlFactory;
        $this->urlBuilder = $this->_urlBuilder;
        parent::__construct($context,$jsonEncoder,$coreFileStorageDatabase,$downloadableFile,$coreRegistry,$sampleModel,$urlFactory,$data);
    }

    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Add New Link'),
                'id' => 'add_sample_item',
                'class' => 'action-add',
                'data_attribute' => ['action' => 'add-sample'],
                'area'=>'adminhtml'
            ]
        );
        return $addButton->toHtml();
    }

    /**
     * Retrieve samples array
     *
     * @return array
     */
    public function getSampleData()
    {
        $samplesArr = [];
        if ($this->getProduct()->getTypeId() !== \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            return $samplesArr;
        }
        $samples = $this->getProduct()->getTypeInstance()->getSamples($this->getProduct());
        $fileHelper = $this->_downloadableFile;
        foreach ($samples as $item) {
            $tmpSampleItem = [
                'sample_id' => $item->getId(),
                'title' => $this->escapeHtml($item->getTitle()),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder(),
            ];

            $sampleFile = $item->getSampleFile();
            if ($sampleFile) {
                $file = $fileHelper->getFilePath($this->_sampleModel->getBasePath(), $sampleFile);

                $fileExist = $fileHelper->ensureFileInFilesystem($file);

                if ($fileExist) {
                    $name = '<a href="' . $this->getUrl(
                        'adminhtml/downloadable_product_edit/sample',
                        ['id' => $item->getId(), '_secure' => true]
                    ) . '">' . $fileHelper->getFileFromPathFile(
                        $sampleFile
                    ) . '</a>';
                    $tmpSampleItem['file_save'] = [
                        [
                            'file' => $sampleFile,
                            'name' => $name,
                            'size' => $fileHelper->getFileSize($file),
                            'status' => 'old',
                        ],
                    ];
                }
            }

            if ($this->getProduct() && $item->getStoreTitle()) {
                $tmpSampleItem['store_title'] = $item->getStoreTitle();
            }
            $samplesArr[] = new \Magento\Framework\DataObject($tmpSampleItem);
        }

        return $samplesArr;
    }

    /**
     * Check exists defined samples title
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUsedDefault()
    {
        return $this->getProduct()->getAttributeDefaultValue('samples_title') === false;
    }

    /**
     * Retrieve Default samples title
     *
     * @return string
     */
    public function getSamplesTitle()
    {
        return $this->getProduct()->getId()
        && $this->getProduct()->getTypeId() == 'downloadable' ? $this->getProduct()->getSamplesTitle() :
            $this->_scopeConfig->getValue(
                \Magento\Downloadable\Model\Sample::XML_PATH_SAMPLES_TITLE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Prepare layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'upload_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'id' => '',
                'label' => __('Upload Files'),
                'type' => 'button',
                'onclick' => 'Downloadable.massUploadByType(\'samples\')',
        		'area'=>'adminhtml'
            ]
        );
    }

    /**
     * Retrieve Upload button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChildBlock('upload_button')->toHtml();
    }

    /**
     * Retrieve config json
     *
     * @return string
     */
    public function getConfigJson()
    {
        $url = $this->urlBuilder->getUrl(
            'csproduct/downloadable_file/upload',
            ['type' => 'samples', '_secure' => true]
        );

        $this->getConfig()->setUrl($url);
        $this->getConfig()->setParams(['form_key' => $this->getFormKey()]);
        $this->getConfig()->setFileField('samples');
        $this->getConfig()->setFilters(['all' => ['label' => __('All Files'), 'files' => ['*.*']]]);
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);
        return $this->_jsonEncoder->encode($this->getConfig()->getData());
    }
}
