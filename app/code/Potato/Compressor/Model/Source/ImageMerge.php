<?php
namespace Potato\Compressor\Model\Source;

class ImageMerge implements \Magento\Framework\Option\ArrayInterface
{
    const DO_NOT_USE_VALUE = 0;
    const BOTH_OPTIONS_VALUE = 1;
    const ONLY_IMAGE_MERGE = 2;
    const ONLY_CSS_MERGE = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::BOTH_OPTIONS_VALUE => __("Yes"),
            self::ONLY_IMAGE_MERGE => __("Only merge of inline images"),
            self::ONLY_CSS_MERGE => __("Only merge of CSS images"),
            self::DO_NOT_USE_VALUE => __("No"),
        ];
    }
}