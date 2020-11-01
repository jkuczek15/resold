<?php
namespace Potato\Compressor\Controller\Js;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Potato\Compressor\Model\RequireJsManager;

class Collect extends \Magento\Framework\App\Action\Action
{
    /** @var  JsonFactory */
    protected $jsonFactory;

    /** @var  RequireJsManager */
    protected $requireJsManager;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        RequireJsManager $requireJsManager
    ) {
        parent::__construct($context);

        $this->jsonFactory = $jsonFactory;
        $this->requireJsManager = $requireJsManager;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $key = $this->_request->getParam('key', null);
        $list = $this->_request->getParam('list', null);
        if (null === $key || null === $list) {
            return $this->jsonFactory->create([
                'result' => false
            ]);
        }
        $result = $this->requireJsManager->saveUrlList($list, $key);
        return $this->jsonFactory->create([
            'result' => $result
        ]);
    }
}
