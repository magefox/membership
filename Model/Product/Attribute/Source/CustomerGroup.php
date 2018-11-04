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
class CustomerGroup extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface
     */
    protected $groupSourceLoggedInOnly;

    public function __construct(
        \Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface $groupSourceLoggedInOnly
    ) {
        $this->groupSourceLoggedInOnly = $groupSourceLoggedInOnly;
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->options) {
            $this->options = $this->groupSourceLoggedInOnly->toOptionArray();
            array_unshift($this->options, ['value' => '', 'label' => __('-- Please Select --')]);
        }

        return $this->options;
    }
}
