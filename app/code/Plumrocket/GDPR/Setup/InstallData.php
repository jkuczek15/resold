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

namespace Plumrocket\GDPR\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Block as BlockResource;

class InstallData implements InstallDataInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var BlockResource
     */
    private $blockResource;

    /**
     * InstallData constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param BlockFactory $blockFactory
     * @param BlockResource $blockResource
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        BlockFactory $blockFactory,
        BlockResource $blockResource
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->blockFactory = $blockFactory;
        $this->blockResource = $blockResource;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(// @codingStandardsIgnoreLine
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        // @codingStandardsIgnoreStart
        $cmsBlockData = [
            'title' => 'Privacy FAQs',
            'identifier' => 'prgdpr_privacy_faqs',
            'content' => '<div class="prgdpr__faq">
                <h2 class="prgdpr__title">Privacy FAQs</h2>
                <p class="prgdpr__descr">Learn how our policies put transparency, simplicity, and control into action for you.</p>
                <ul class="prgdpr-accordion">
                    <li class="prgdpr-accordion__item">
                        <input class="prgdpr-accordion__toggle" checked="checked" type="checkbox" />
                        <i class="prgdpr-accordion__btn"></i>
                        <b class="prgdpr__name prgdpr-accordion__name">What information do you collect about me?</b>
                        <div class="prgdpr-accordion__descr">
                            <p>Initially, we collect basic information from you to create your account, including your full name, email and password. We collect further data, such as address, and billing information or other information you provide when you checkout at our store. We also collect some information about users who visit our websites using cookies and similar technologies. See the Privacy Policy and/or Cookie Policy for the particular website(s) or service(s) you are using for more details about the specific types of information we may collect and your choices related to that data.</p>
                        </div>
                    </li>
                    <li class="prgdpr-accordion__item">
                        <input class="prgdpr-accordion__toggle" checked="checked" type="checkbox" />
                        <i class="prgdpr-accordion__btn"></i>
                        <b class="prgdpr__name prgdpr-accordion__name">How do you use information about me?</b>
                        <div class="prgdpr-accordion__descr">
                            <p>We primarily use information about you to provide, personalize, improve, update and expand and improve our services to you. For example, we use information about you to create your account and deliver our products and services. We may also use your information to improve or develop new products and services, and for internal business purposes. We may also use personal information about you to verify your identity, communicate with you and to deliver advertisements, issuing surveys and questionnaires to collect additional user information for use in the services, marketing new products and offers from us or our business partners, detecting and protecting against error, fraud, or other criminal or malicious activity. See our Privacy Policy for more details.</p>
                        </div>
                    </li>
                    <li class="prgdpr-accordion__item">
                        <input class="prgdpr-accordion__toggle" checked="checked" type="checkbox" />
                        <i class="prgdpr-accordion__btn"></i>
                        <b class="prgdpr__name prgdpr-accordion__name">Do you share information about me?</b>
                        <div class="prgdpr-accordion__descr">
                            <p>We do not share your personal information with third-parties without your additional consent other than as described in our Privacy Policy. Otherwise, we share information as described in our Privacy Policy, such as with our affiliated companies, our service providers, our advertising partners, and in other limited scenarios, such as in response to valid legal process or as appropriate to protect the rights, property, safety, confidentiality, or reputation of our company, our affiliates, and our users. You can read the Privacy Policy for the particular website(s) or service(s) you are using for more details about how we share your data and your choices related to that sharing.</p>
                        </div>
                    </li>
                    <li class="prgdpr-accordion__item">
                        <input class="prgdpr-accordion__toggle" checked="checked" type="checkbox" />
                        <i class="prgdpr-accordion__btn"></i>
                        <b class="prgdpr__name prgdpr-accordion__name">How can I review, update, or delete information about me?</b>
                        <div class="prgdpr-accordion__descr">
                            <p>You can review, update, or delete most information about yourself through your account page on our website. If you have questions about how to review, update, or delete information, you should contact our Data Protection Office or Customer Services team.</p>
                        </div>
                    </li>
                    <li class="prgdpr-accordion__item">
                        <input class="prgdpr-accordion__toggle" checked="checked" type="checkbox" />
                        <i class="prgdpr-accordion__btn"></i>
                        <b class="prgdpr__name prgdpr-accordion__name">What happens to my information if I delete my account?</b>
                        <div class="prgdpr-accordion__descr">
                            <p>When you delete your account, we remove your information from our store (it may take up to 30 days to make sure everything is deleted). During that period, you won\'t be able to use your account. Our company also retains certain data to comply with local laws, such as sales receipts to comply with local tax laws.</p>
                            <p>Please keep in mind that once your account has been deleted you won\'t be able to reactivate your account or retrieve anything you\'ve added, including:</p>
                            <ul>
                                <li>Your billing information</li>
                                <li>Your orders, invoices, shipments</li>
                                <li>Any history of interactions with our support</li>
                                <li>Any content you have created associated with your store account</li>
                                <li>You also will no longer be able to return any products or services and will lose any existing store rewards or credits</li>
                            </ul>
                            <p>If you decide that you no longer wish to delete your store account, you can cancel your deletion request within 24 hours of making the deletion request by login in to your account.</p>
                        </div>
                    </li>
                    <li class="prgdpr-accordion__item">
                        <input class="prgdpr-accordion__toggle" checked="checked" type="checkbox" />
                        <i class="prgdpr-accordion__btn"></i>
                        <b class="prgdpr__name prgdpr-accordion__name">What are you doing to protect my personal information?</b>
                        <div class="prgdpr-accordion__descr">
                            <p>We work hard to protect our customers from unauthorized access, use and disclosure of information. We store and process the information we collect on computer systems with limited access, which are located in controlled facilities. Any sensitive information is protected through the use of best-practice physical, environmental and digital security systems.</p>
                        </div>
                    </li>
                    <li class="prgdpr-accordion__item">
                        <input class="prgdpr-accordion__toggle" checked="checked" type="checkbox" />
                        <i class="prgdpr-accordion__btn"></i>
                        <b class="prgdpr__name prgdpr-accordion__name">What can I do to protect my personal information?</b>
                        <div class="prgdpr-accordion__descr">
                            <p>There are a number of measures that you can take to protect your personal information, including:</p>
                            <ul>
                                <li>Use strong passwords for all your online accounts</li>
                                <li>Do keep passwords securely (never written down, or shared with anyone) and changed periodically</li>
                                <li>Don\'t automatically trust every website or email which asks you to provide your personal information. Take time to check that the request is valid, and that the personal information requested is absolutely necessary for the services that you are looking to use</li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="prgdpr__footer">
                <p class="prgdpr__footer-text-top">Please contact our Data Protection Officer with any questions or concerns via email at <a href="mailto:privacy@domain.com">privacy@domain.com</a> or via postal mail at:</p>
                <p class="prgdpr__address">Data Protection Officer<br /> Your Company<br /> 123 Street, Suite 444<br /> New York, NY 10001<br /> USA</p>
            </div>',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];
        // @codingStandardsIgnoreEnd

        $block = $this->blockFactory->create()->setData($cmsBlockData);
        $this->blockResource->save($block);
    }
}
