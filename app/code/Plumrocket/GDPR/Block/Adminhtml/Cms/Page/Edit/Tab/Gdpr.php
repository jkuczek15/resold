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

namespace Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit\Tab;

use Plumrocket\GDPR\Helper\Data as DataHelper;
use Plumrocket\GDPR\Model\Revision;

/**
 * Class Gdpr
 *
 * @package Plumrocket\GDPR\Block\Adminhtml\Cms\Edit\Tab
 */
class Gdpr extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $yesno;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * Gdpr constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->yesno = $yesno;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('GDPR Settings');
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __('GDPR Settings');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return $this->dataHelper->moduleEnabled();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return null|int
     */
    public function getCurrentCmsPageId()
    {
        /** @var null|\Magento\Cms\Model\Page $currentModel */
        $currentModel = $this->_coreRegistry->registry('cms_page');

        return $currentModel ? $currentModel->getId() : null;
    }

    /**
     * Initialize the form.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }

        $cmsPageId = $this->getCurrentCmsPageId();
        $form = $this->_formFactory->create();
        $prefix = DataHelper::SECTION_ID . '_';
        $fieldsetName = 'revision';
        $fieldset = $form->addFieldset($fieldsetName, ['legend' => __('GDPR Settings')]);

        $fieldset->addField('revision_enabled', 'select', [
            'name'      => 'revision_enabled',
            'label'     => __('Enable Revisions'),
            'values'    => $this->yesno->toOptionArray(),
            'value'     => 1,
            'data-form-part' => $this->getData('target_form'),
            'note'      => '',
        ]);

        $fieldset->addField('revision_doc_version', 'text', [
            'name' => 'revision_doc_version',
            'label' => __('Document Version'),
            'value' => Revision::DEFAULT_VERSION,
            'data-form-part' => $this->getData('target_form'),
            'required' => false,
            'note' => '',
        ]);

        $note = __('If enabled, this option will display popup notification to all customers upon successful login.'
            . ' This is useful when asking customers to agree to the updated version of the "Privacy Policy",'
            . ' TOS, Cookie Policy or any other agreement.');

        $fieldset->addField('revision_notify_enabled', 'select', [
            'name'      => 'revision_notify_enabled',
            'label'     => __('Notify All Customers via Popup'),
            'values'    => $this->yesno->toOptionArray(),
            'value'     => 0,
            'data-form-part' => $this->getData('target_form'),
            'note'      => $this->escapeHtml($note),
        ]);

        $this->setForm($form);

        return $this;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()// @codingStandardsIgnoreLine we need to extend parent method
    {
        if ($this->canShowTab()) {
            $this->initForm();
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
