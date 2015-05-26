<?php 

class KL_BackInStock_Block_Modal extends Mage_Catalog_Block_Product_View
{
    const BACKINSTOCK_NOTIFICATION_SUBSCRIBE = '/backinstock/notification/subscribe';

    public function getSubscribeUrl()
    {
        return self::BACKINSTOCK_NOTIFICATION_SUBSCRIBE;
    }
}