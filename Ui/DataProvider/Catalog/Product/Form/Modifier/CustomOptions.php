<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2020 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Ui\DataProvider\Catalog\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions as CustomOptionsModifier;
use Magefox\Membership\Model\Product\Type\Membership;

class CustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * CustomOptions constructor.
     *
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Modify data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();

        if ($product->getTypeId() === Membership::TYPE_CODE) {
            $data = $this->arrayManager->remove(
                $this->arrayManager->findPath(CustomOptionsModifier::FIELD_ENABLE, $data),
                $data
            );
        }

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
            $meta = $this->arrayManager->remove(CustomOptionsModifier::GROUP_CUSTOM_OPTIONS_NAME, $meta);
        }

        return $meta;
    }
}
