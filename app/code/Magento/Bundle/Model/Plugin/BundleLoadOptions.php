<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Bundle\Model\Plugin;

class BundleLoadOptions
{
    /**
     * @var \Magento\Bundle\Model\Product\OptionList
     */
    protected $productOptionList;

    /**
     * @var \Magento\Framework\Api\AttributeValueFactory
     */
    protected $customAttributeFactory;

    /**
     * @param \Magento\Bundle\Model\Product\OptionList $productOptionList
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     */
    public function __construct(
        \Magento\Bundle\Model\Product\OptionList $productOptionList,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
    ) {
        $this->productOptionList = $productOptionList;
        $this->customAttributeFactory = $customAttributeFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param callable $proceed
     * @param int $modelId
     * @param null $field
     * @return \Magento\Catalog\Model\Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(
        \Magento\Catalog\Model\Product $subject,
        \Closure $proceed,
        $modelId,
        $field = null
    ) {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $proceed($modelId, $field);
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return $product;
        }
        $customAttribute = $this->customAttributeFactory->create()
            ->setAttributeCode('bundle_product_options')
            ->setValue($this->productOptionList->getItems($product));
        $attributes = array_merge($product->getCustomAttributes(), ['bundle_product_options' => $customAttribute]);
        $product->setData('custom_attributes', $attributes);
        return $product;
    }
}
