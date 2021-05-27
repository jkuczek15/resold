<?php
namespace Mirasvit\Search\Repository\IndexRepository;

/**
 * Interceptor class for @see \Mirasvit\Search\Repository\IndexRepository
 */
class Interceptor extends \Mirasvit\Search\Repository\IndexRepository implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\EntityManager\EntityManager $entityManager, \Mirasvit\Search\Api\Data\IndexInterfaceFactory $indexFactory, \Mirasvit\Search\Model\ResourceModel\Index\CollectionFactory $indexCollectionFactory, \Magento\Framework\ObjectManagerInterface $objectManager, $indices = [])
    {
        $this->___init();
        parent::__construct($entityManager, $indexFactory, $indexCollectionFactory, $objectManager, $indices);
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Mirasvit\Search\Api\Data\IndexInterface $index)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save($index);
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }
}
