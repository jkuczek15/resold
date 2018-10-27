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



namespace Mirasvit\SearchElastic\Adapter\Query\Filter;

use Magento\Framework\Search\Request\Filter\Term;

class TermFilter
{
    /**
     * @param Term $filter
     * @return array
     */
    public function build(Term $filter)
    {
        $query = [];
        if ($filter->getValue()) {
            $value = $filter->getValue();

            $condition = is_array($value) ? 'terms' : 'term';

            if (is_array($value)) {
                if (key_exists('in', $value)) {
                    $value = $value['in'];
                }

                $value = array_values($value);
            }

            $query[] = [
                $condition => [
                    $filter->getField() . '_raw' => $value,
                ],
            ];
        }

        return $query;
    }
}
