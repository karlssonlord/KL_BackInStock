<?php 

class KL_BackInStock_Block_Modal extends Mage_Catalog_Block_Product_View
{
    public function getSubscribeUrl($secure = false)
    {
        return Mage::getUrl('backinstock/notification/subscribe', array('_secure' => $secure));
    }
}