<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Setup;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    )
    {
        /**
         * @var \Magento\Eav\Setup\EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory
            ->create(['setup' => $setup]);

        /**
         * Add price attributes to the Membership product type.
         */
        $this->addAttributesToMembershipType($eavSetup);

        /**
         * Add product membership attributes
         */
        $this->addProductAttributes($eavSetup);

        /**
         * Create customer attributes (membership_expiry, membership_order_id)
         */
        $this->addCustomerAttributes($setup, $eavSetup);
    }

    /**
     * Add product membership attributes
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     */
    public function addProductAttributes(\Magento\Eav\Setup\EavSetup $eavSetup)
    {
        $attributeSetId = $eavSetup->getDefaultAttributeSetId(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE);
        $eavSetup->addAttributeGroup(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSetId,
            'Membership',
            11
        );

        /**
         * Create membership_length, and membership_length_unit attributes to membership product type.
         */
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'membership_length', [
            'type' => 'int',
            'label' => 'Length',
            'input' => 'text',
            'required' => true,
            'sort_order' => 8,
            'searchable' => true,
            'used_in_product_listing' => false,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
            'apply_to' => \Magefox\Membership\Model\Product\Type\Membership::TYPE_CODE
        ]);

        $eavSetup->addAttributeToGroup(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeSetId,
            'Membership',
            'membership_length',
            1
        );

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'membership_length_unit', [
            'type' => 'varchar',
            'label' => 'Length Unit',
            'input' => 'select',
            'source' => 'Magefox\Membership\Model\Product\Attribute\Source\LengthUnit',
            'sort_order' => 9,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
            'searchable' => false,
            'required' => true,
            'used_in_product_listing' => false,
            'apply_to' => \Magefox\Membership\Model\Product\Type\Membership::TYPE_CODE
        ]);

        $eavSetup->addAttributeToGroup(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeSetId,
            'Membership',
            'membership_length_unit',
            2
        );

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'membership_group_to_assign', [
            'type' => 'int',
            'label' => 'Group to Assign',
            'input' => 'select',
            'source' => 'Magefox\Membership\Model\Product\Attribute\Source\CustomerGroup',
            'sort_order' => 9,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
            'searchable' => false,
            'required' => true,
            'used_in_product_listing' => false,
            'apply_to' => \Magefox\Membership\Model\Product\Type\Membership::TYPE_CODE,
            'note' => 'When a customer becomes a Membership, which customer group do you want them moved to.'
        ]);

        $eavSetup->addAttributeToGroup(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeSetId,
            'Membership',
            'membership_group_to_assign',
            3
        );
    }

    /**
     * Add customer membership attributes
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     */
    public function addCustomerAttributes(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Eav\Setup\EavSetup $eavSetup
    ) {
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'membership_expiry', [
            'type' => 'datetime',
            'label' => 'Membership Expiry',
            'input' => 'date',
            'frontend' => 'Magento\Eav\Model\Entity\Attribute\Frontend\Datetime',
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\Datetime',
            'required' => false,
            'visible' => true,
            'system' => false,
            'input_filter' => 'date',
            'validate_rules' => '{"input_validation":"date"}',
            'position' => 200,
            'note' => 'Expire time in GMT (UTC).'
        ]);

        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'membership_order_id', [
            'type' => 'varchar',
            'label' => 'Membership Order ID',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'system' => false,
            'validate_rules' => '{"max_text_length":255}',
            'position' => 201,
        ]);

        $data = [
            [
                'form_code' => 'adminhtml_customer',
                'attribute_id' => $eavSetup->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'membership_expiry', 'attribute_id')
            ],
            [
                'form_code' => 'adminhtml_customer',
                'attribute_id' => $eavSetup->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'membership_order_id', 'attribute_id')
            ],
        ];

        $setup->getConnection()
            ->insertMultiple($setup->getTable('customer_form_attribute'), $data);
    }

    /**
     * Add price attributes to the Membership product type.
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     */
    public function addAttributesToMembershipType(\Magento\Eav\Setup\EavSetup $eavSetup)
    {
        $attributes = [
            'minimal_price',
            'msrp',
            'msrp_display_actual_price_type',
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
        ];

        foreach ($attributes as $attributeCode) {
            $relatedProductTypes = explode(
                ',',
                $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode, 'apply_to')
            );
            if (!in_array(\Magefox\Membership\Model\Product\Type\Membership::TYPE_CODE, $relatedProductTypes)) {
                $relatedProductTypes[] = \Magefox\Membership\Model\Product\Type\Membership::TYPE_CODE;
                $eavSetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeCode,
                    'apply_to',
                    implode(',', $relatedProductTypes)
                );
            }
        }
    }
}
