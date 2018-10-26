<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Product status functionality model
 */
class LengthUnit extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    const UNIT_DAYS = 'D';
    const UNIT_WEEKS = 'W';
    const UNIT_MONTHS = 'M';
    const UNIT_YEARS = 'Y';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::UNIT_DAYS => __('Days'),
            self::UNIT_WEEKS => __('Weeks'),
            self::UNIT_MONTHS => __('Months'),
            self::UNIT_YEARS => __('Years'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
