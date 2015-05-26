<?php 

class KL_BackInStock_Model_Observer extends Mage_ProductAlert_Model_Observer
{
    /**
     * Run process send product alerts
     *
     * @return Mage_ProductAlert_Model_Observer
     */
    public function process()
    {
        $email = Mage::getModel('productalert/email');
        /* @var $email Mage_ProductAlert_Model_Email */
        $this->_processPrice($email);
        $this->_processStock($email);
        $this->_sendErrorEmail();

        return $this;
    }
    /**
     * Process stock emails
     *
     * @param Mage_ProductAlert_Model_Email $email
     * @return Mage_ProductAlert_Model_Observer
     */
    protected function _processStock(Mage_ProductAlert_Model_Email $email)
    {
        $email->setType('stock');

        foreach ($this->_getWebsites() as $website) {

            /* @var $website Mage_Core_Model_Website */
            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            if (!Mage::getStoreConfig(self::XML_PATH_STOCK_ALLOW, $website->getDefaultGroup()->getDefaultStore()->getId())) {
                continue;
            }

            try {
                $collection = Mage::getModel('productalert/stock')
                    ->getCollection()
                    ->addWebsiteFilter($website->getId())
                    ->addStatusFilter(0)
                    ->setCustomerOrder();
            }
            catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                try {
                    if ($alert->getCustomerId() == 0) {
                        $customer = Mage::getModel('customer/customer')
                            ->setEmail($alert->getEmail())
                            ->setName($alert->getName())
                            ->setCustomerGroupId(0)
                        ;
                        $email->setCustomer($customer);
                        $previousCustomer = $customer;
                    } elseif (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                        $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                        if ($previousCustomer) {
                            $email->send();
                        }
                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                    }
                    else {
                        $customer = $previousCustomer;
                    }

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());
                    /* @var $product Mage_Catalog_Model_Product */
                    if (!$product) {
                        continue;
                    }

                    $product->setCustomerGroupId($customer->getGroupId());

//                    if (Mage::helper('backinstock/product')->isSalable($product)) {
                    if ($product->isSalable()) {
                        $email->addStockProduct($product);
                        $alert->setSendDate(Mage::getModel('core/date')->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    }
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            if ($previousCustomer) {
                try {
                    $email->send();
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }

        return $this;
    }
}