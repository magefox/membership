<?php
/******************************************************
 * @package   Magento 2 Membership
 * @author    http://www.magefox.com
 * @copyright (C) 2020 - Magefox.Com
 * @license   MIT
 *******************************************************/

namespace Magefox\Membership\Observer;

use Magefox\Membership\Model\Product\Type\Membership;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;

class ValidateQuoteItemQty implements ObserverInterface
{
    /**
     * Execute when trigger 'sales_quote_item_qty_set_after' event
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Item $item */
        $item = $observer->getData('item');
        $product = $item->getProduct();

        if ($product->getTypeId() === Membership::TYPE_CODE && $item->getData(Item::KEY_QTY) > Membership::MAX_BUY_QTY) {
            throw new LocalizedException(__("Can't place greater than 1 membership items at a time."));
        }
    }
}
