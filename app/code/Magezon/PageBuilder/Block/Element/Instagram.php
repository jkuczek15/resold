<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilder\Block\Element;

class Instagram extends \Magezon\Builder\Block\Element
{
	/**
	 * @var \Magezon\PageBuilder\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var \Magezon\PageBuilder\Model\InstagramCacheManagerFactory
	 */
	protected $instagramCacheManagerFactory;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context        $context                      
	 * @param \Magezon\PageBuilder\Helper\Data                        $dataHelper                   
	 * @param \Magezon\PageBuilder\Model\InstagramCacheManagerFactory $instagramCacheManagerFactory 
	 * @param array                                                   $data                         
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magezon\PageBuilder\Helper\Data $dataHelper,
		\Magezon\PageBuilder\Model\InstagramCacheManagerFactory $instagramCacheManagerFactory,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->dataHelper                   = $dataHelper;
		$this->instagramCacheManagerFactory = $instagramCacheManagerFactory;
	}

	/**
	 * @return array
	 */
	public function getItems()
	{
		$element  = $this->getElement();
		$maxItems = (int) $element->getData('max_items');
		$type     = $element->getData('fetch_type');
		$key      = $element->getData('fetch_key');
		$items    = [];
		try {
			if ($type) {
				$cache     = $this->instagramCacheManagerFactory->create();
				$username  = $this->dataHelper->getConfig('instagram/username');
				$password  = $this->dataHelper->getConfig('instagram/password');
				$instagram = \InstagramScraper\Instagram::withCredentials($username, $password, $cache);
				$instagram->login();
				if ($type == 'username') {
					$items = $instagram->getMedias($key, $maxItems);
				}
				if ($type == 'hashtag') {
					$items = $instagram->getMediasByTag($key, $maxItems);
				}
			}
		} catch (\Exception $e) {
		}
		return $items;
	}

	/**
	 * @return string
	 */
	public function getFollowLink()
	{
		$element   = $this->getElement();
		$fetchType = $element->getData('fetch_type');
		$fetchKey  = $element->getData('fetch_key');
		if ($fetchType == 'hashtag') {
			return 'https://instagram.com/explore/tags/' . $fetchKey;
		} else {
			return 'https://www.instagram.com/' . $fetchKey;
		}
	}

	/**
	 * @return string
	 */
	public function getDataSize()
	{
		$element   = $this->getElement();
		$photoSize = $element->getData('photo_size');
		$size      = '1000x1000';
		switch ($photoSize) {
			case 'thumbnail':
				$size = '150x150';
				break;

			case 'small':
				$size = '320x320';
				break;

			case 'large':
				$size = '640x640';
				break;
		}
		return $size;
	}

	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml = '';
		$element   = $this->getElement();

		if ($gap = (int)$element->getData('gap')) {
			$styles = [];
			$styles['padding'] = $this->getStyleProperty($gap / 2);
			$styleHtml .= $this->getStyles([
				'.mgz-grid-item'
			], $styles);
		}

		$styles = [];
		$styles['font-size'] = $this->getStyleProperty($element->getData('text_size'));
		$styles['color'] = $this->getStyleColor($element->getData('text_color'));
		$styleHtml .= $this->getStyles(['.item-likes', '.item-comments'], $styles);

		$styles = [];
		$styles['background'] = $this->getStyleColor($element->getData('overlay_color'));
		$styleHtml .= $this->getStyles('.item-metadata', $styles);

		$styleHtml .= $this->getLineStyles();

		return $styleHtml;
	}
}