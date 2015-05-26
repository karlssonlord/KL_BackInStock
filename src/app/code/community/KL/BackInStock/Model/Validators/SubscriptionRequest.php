<?php 

class KL_BackInStock_Model_Validators_SubscriptionRequest implements KL_BackInStock_Model_Validators_Validator
{
    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @throws KL_BackInStock_Model_Exceptions_AlreadySubscribed
     * @throws KL_BackInStock_Model_Exceptions_Unauthorized
     * @return void
     */
    public function validate(Mage_Core_Controller_Request_Http $request)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn() && $request->getParam('customer_id')) {
            throw new KL_BackInStock_Model_Exceptions_Unauthorized('You are not authorized to make such request.');
        }

        if ($this->emailIsSubscribed($request)) {
            throw new KL_BackInStock_Model_Exceptions_AlreadySubscribed('You are already subscribed to this notification.');
        }
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    protected function emailIsSubscribed(Mage_Core_Controller_Request_Http $request)
    {
        // TODO: implement
        return false;
    }
}