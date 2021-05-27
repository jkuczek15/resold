<?php
namespace Magento\ImportExport\Block\Adminhtml\Import\Edit\Form;

/**
 * Interceptor class for @see \Magento\ImportExport\Block\Adminhtml\Import\Edit\Form
 */
class Interceptor extends \Magento\ImportExport\Block\Adminhtml\Import\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\ImportExport\Model\Import $importModel, \Magento\ImportExport\Model\Source\Import\EntityFactory $entityFactory, \Magento\ImportExport\Model\Source\Import\Behavior\Factory $behaviorFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $importModel, $entityFactory, $behaviorFactory, $data);
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
