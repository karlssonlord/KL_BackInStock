<?php

class KL_BackInStock_Block_Cta extends Mage_Catalog_Block_Product_View
{
    /**
     * @var bool
     */
    protected $secureUrls = false;

    /**
     * @var array
     */
    protected $outOfStockLinks = array();

    /**
     * @return bool
     */
    public function allSizesInStock()
    {
        $this->checkChildProducts();
        return !$this->containedMissingChildren();
    }

    /**
     * @return array
     */
    public function getOutOfStockLinks()
    {
        return $this->outOfStockLinks;
    }

    /**
     * Make urls secure
     */
    public function setSecureUrls()
    {
        $this->secureUrls = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSecureUrls()
    {
        return $this->secureUrls;
    }

    /**
     *
     */
    protected function checkChildProducts()
    {
        $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $this->getProduct());
        foreach($childProducts as $child) {
            $this->checkStockStatus($child);
        }
    }

    /**
     * @return bool
     */
    protected function containedMissingChildren()
    {
        return count($this->outOfStockLinks) > 0;
    }

    /**
     * @return bool
     */
    public function isConfigurable()
    {
        return $this->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isOutOfStock($product)
    {
        return (int)Mage::getModel('cataloginventory/stock_item')
            ->loadByProduct($product->getId())->getQty() < 1;
    }

    /**
     * @return bool
     */
    public function isSalable()
    {
        return $this->getProduct()->isSaleable();
    }

    /**
     * @return bool
     */
    public function isSimple()
    {
        return $this->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
    }

    /**
     * @param Mage_Catalog_Model_Product $child
     * @return string
     */
    public function generateStockNotificationLink(Mage_Catalog_Model_Product $child, $secure = false)
    {
        return Mage::getUrl('backinstock/notification/subscribe', array(
            'product_id'    => $child->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core/url')->getEncodedUrl(),
            '_secure' => $secure ? : $this->secureUrls
        ));
    }

    /**
     * @param $child
     */
    protected function checkStockStatus(Mage_Catalog_Model_Product $child)
    {
        if ($this->isOutOfStock($child)) {
            $this->registerItem($child);
        }
    }

    /**
     * @param $child
     */
    protected function registerItem(Mage_Catalog_Model_Product $child)
    {
        $child = $child->load($child->getId());
        $this->outOfStockLinks[] = array(
            'id' => $child->getId(),
            'link' => $this->generateStockNotificationLink($child, $this->secureUrls),
            'name' => $child->getAttributeText('size')
        );
    }

}