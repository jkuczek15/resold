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

class Links extends \Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links
{
    protected $_template = 'Ced_CsProduct::downloadable/product/edit/downloadable/links.phtml';

    protected $urlBuilder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Downloadable\Helper\File $downloadableFile,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Config\Model\Config\Source\Yesno $sourceModel,
        \Magento\Downloadable\Model\Link $link,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Backend\Model\UrlFactory $urlFactory,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $coreRegistry;
        $this->_coreFileStorageDb = $coreFileStorageDatabase;
        $this->_downloadableFile = $downloadableFile;
        $this->_sourceModel = $sourceModel;
        $this->_link = $link;
        $this->_attributeFactory = $attributeFactory;
        $this->_urlFactory = $urlFactory;
        $this->urlBuilder = $this->_urlBuilder;
        parent::__construct($context,$jsonEncoder,$coreFileStorageDatabase,$downloadableFile,$coreRegistry,$sourceModel,$link,$attributeFactory,$urlFactory,$data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Add New Link'),
                'id' => 'add_link_item',
                'class' => 'action-add',
                'data_attribute' => ['action' => 'add-link'],
                'area'=>'adminhtml'
            ]
        );
        return $addButton->toHtml();
    }

    /**
     * Retrieve default links title
     *
     * @return string
     */
    public function getLinksTitle()
    {
        return $this->getProduct()->getId() &&
            $this->getProduct()->getTypeId() ==
            'downloadable' ? $this->getProduct()->getLinksTitle() : $this->_scopeConfig->getValue(
                \Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Check exists defined links title
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUsedDefault()
    {
        return $this->getProduct()->getAttributeDefaultValue('links_title') === false;
    }

    /**
     * Return true if price in website scope
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsPriceWebsiteScope()
    {
        $scope = (int)$this->_scopeConfig->getValue(
            \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE) {
            return true;
        }
        return false;
    }

    /**
     * Return array of links
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getLinkData()
    {
        $linkArr = [];
        if ($this->getProduct()->getTypeId() !== \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            return $linkArr;
        }
        $links = $this->getProduct()->getTypeInstance()->getLinks($this->getProduct());
        $priceWebsiteScope = $this->getIsPriceWebsiteScope();
        $fileHelper = $this->_downloadableFile;
        foreach ($links as $item) {
            $tmpLinkItem = [
                'link_id' => $item->getId(),
                'title' => $this->escapeHtml($item->getTitle()),
                'price' => $this->getCanReadPrice() ? $this->getPriceValue($item->getPrice()) : '',
                'number_of_downloads' => $item->getNumberOfDownloads(),
                'is_shareable' => $item->getIsShareable(),
                'link_url' => $item->getLinkUrl(),
                'link_type' => $item->getLinkType(),
                'sample_file' => $item->getSampleFile(),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder(),
            ];

            $linkFile = $item->getLinkFile();
            if ($linkFile) {
                $file = $fileHelper->getFilePath($this->_link->getBasePath(), $linkFile);

                $fileExist = $fileHelper->ensureFileInFilesystem($file);

                if ($fileExist) {
                    $name = '<a href="' . $this->getUrl(
                        'adminhtml/downloadable_product_edit/link',
                        ['id' => $item->getId(), 'type' => 'link', '_secure' => true]
                    ) . '">' . $fileHelper->getFileFromPathFile(
                        $linkFile
                    ) . '</a>';
                    $tmpLinkItem['file_save'] = [
                        [
                            'file' => $linkFile,
                            'name' => $name,
                            'size' => $fileHelper->getFileSize($file),
                            'status' => 'old',
                        ],
                    ];
                }
            }

            $sampleFile = $item->getSampleFile();
            if ($sampleFile) {
                $file = $fileHelper->getFilePath($this->_link->getBaseSamplePath(), $sampleFile);

                $fileExist = $fileHelper->ensureFileInFilesystem($file);

                if ($fileExist) {
                    $name = '<a href="' . $this->getUrl(
                        'adminhtml/downloadable_product_edit/link',
                        ['id' => $item->getId(), 'type' => 'sample', '_secure' => true]
                    ) . '">' . $fileHelper->getFileFromPathFile(
                        $sampleFile
                    ) . '</a>';
                    $tmpLinkItem['sample_file_save'] = [
                        [
                            'file' => $item->getSampleFile(),
                            'name' => $name,
                            'size' => $fileHelper->getFileSize($file),
                            'status' => 'old',
                        ],
                    ];
                }
            }

            if ($item->getNumberOfDownloads() == '0') {
                $tmpLinkItem['is_unlimited'] = ' checked="checked"';
            }
            if ($this->getProduct()->getStoreId() && $item->getStoreTitle()) {
                $tmpLinkItem['store_title'] = $item->getStoreTitle();
            }
            if ($this->getProduct()->getStoreId() && $priceWebsiteScope) {
                $tmpLinkItem['website_price'] = $item->getWebsitePrice();
            }
            $linkArr[] = new \Magento\Framework\DataObject($tmpLinkItem);
        }
        return $linkArr;
    }

    /**
     * Prepare block Layout
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
                'onclick' => 'Downloadable.massUploadByType(\'links\');Downloadable.massUploadByType(\'linkssample\')',
        		'area'=>'adminhtml'
            ]
        );
    }

    /**
     * Retrieve Upload URL
     *
     * @param string $type
     * @return string
     */
    public function getUploadUrl($type)
    {
        return $this->urlBuilder->getUrl(
            'csproduct/downloadable_file/upload',
            ['type' => $type, '_secure' => true]
        );
    }

    /**
     * Retrieve config json
     *
     * @param string $type
     * @return string
     */
    public function getConfigJson($type = 'links')
    {
        $this->getConfig()->setUrl($this->getUploadUrl($type));
        $this->getConfig()->setParams(['form_key' => $this->getFormKey()]);
        $this->getConfig()->setFileField($this->getFileFieldName($type));
        $this->getConfig()->setFilters(['all' => ['label' => __('All Files'), 'files' => ['*.*']]]);
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);
        return $this->_jsonEncoder->encode($this->getConfig()->getData());
    }
}
