<?php
namespace Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit\Tab\Gdpr;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit\Tab\Gdpr
 */
class Interceptor extends \Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit\Tab\Gdpr implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Config\Model\Config\Source\Yesno $yesno, \Plumrocket\GDPR\Helper\Data $dataHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $yesno, $dataHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }
}
