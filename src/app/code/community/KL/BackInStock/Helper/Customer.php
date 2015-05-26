<?php 

class KL_BackInStock_Helper_Customer extends Mage_Core_Helper_Abstract
{
    /**
     * @param $email
     * @return mixed
     */
    public function loadByEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please provide a valid email address.');
        }

        return Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getWebsite()->getId())
            ->loadByEmail($email)
        ;
    }

}
