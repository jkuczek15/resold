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
 * @package   mirasvit/module-search-elastic
 * @version   1.2.26
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchElastic\Controller\Adminhtml\Command;

use Mirasvit\SearchElastic\Controller\Adminhtml\Command;

class Reset extends Command
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $success = true;
        $note = '';
        $message = '';

        try {
            $this->engine->reset($note);
            $message .= __('Done.');
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $success = false;
        }

        $jsonData = json_encode([
            'message' => nl2br($message),
            'note'    => '',
            'success' => $success,
        ]);

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_SearchElastic::command_reset');
    }
}
