<?php 

class KL_BackInStock_Model_Validator_SubscriptionRequest implements KL_BackInStock_Model_RequestValidator
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
        // Check the email column. This column is populated by non-logged-in customers
        foreach (Mage::getModel('productalert/stock')->getCollection() as $alert) {
            if ($alert->getEmail() == $request->getParam('email')) {
                return true;
            }
        }

        // In case logged in customer subscribe to alerts, we need to run through the customer collection too
        foreach (Mage::getModel('productalert/stock')->getCustomerCollection() as $customer) {
            if ($customer->getEmail() == $request->getParam('email')) {
                return true;
            }
        }
    }
}