<?php 

class KL_BackInStock_Helper_Product
{
    public function isSalable(Mage_Catalog_Model_Product $product)
    {
        return Mage::getModel('cataloginventory/stock_item')
            ->loadByProduct($product->getId())->getQty() > 0
            &&
            $product->getStatus() === Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
    }
}