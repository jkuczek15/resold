<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.100
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Index\Magento\Cms\Page;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Mirasvit\Search\Api\Data\Index\DataMapperInterface;
use Magento\Cms\Model\Template\FilterProvider as CmsFilterProvider;
use Magento\Email\Model\TemplateFactory as EmailTemplateFactory;

class DataMapper implements DataMapperInterface
{
    /**
     * @var AppEmulation
     */
    private $emulation;

    /**
     * @var CmsFilterProvider
     */
    private $filterProvider;

    /**
     * @var EmailTemplateFactory
     */
    private $templateFactory;

    public function __construct(
        AppEmulation $emulation,
        CmsFilterProvider $filterProvider,
        EmailTemplateFactory $templateFactory
    ) {
        $this->emulation = $emulation;
        $this->filterProvider = $filterProvider;
        $this->templateFactory = $templateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function map(array $documents, $dimensions, $indexIdentifier)
    {
        $storeId = current($dimensions)->getValue();

        $this->emulation->startEnvironmentEmulation($storeId);

        foreach ($documents as $id => $doc) {
            $template = $this->templateFactory->create();
            $template->emulateDesign($storeId);

            foreach ($doc as $key => $value) {
                $template->setTemplateText($value)
                    ->setIsPlain(false);
                $template->setTemplateFilter($this->filterProvider->getPageFilter());
                $processed = $template->getProcessedTemplate([]);

                $documents[$id][$key] .= $processed;
            }
        }

        $this->emulation->stopEnvironmentEmulation();

        return $documents;
    }
}
