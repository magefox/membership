<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2020 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Ui\DataProvider\Catalog\Product\Form\Modifier;

use Magefox\Membership\Model\Product\Type\Membership;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class MembershipLength extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * MembershipLength constructor.
     *
     * @param LocatorInterface $locator
     */
    public function __construct(
        LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }

    /**
     * Modify data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() === Membership::TYPE_CODE) {
            $meta = array_merge_recursive($meta, [
                'membership' => [
                    'children' => [
                        'container_membership_length' => [
                            'children' => [
                                'membership_length' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'validation' => [
                                                    'validate-greater-than-zero' => true
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        }

        return $meta;
    }
}
