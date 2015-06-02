<?php
require_once Mage::getModuleDir('controllers', 'Mage_ProductAlert') . DS . 'AddController.php';
class KL_BackInStock_NotificationController extends Mage_Core_Controller_Front_Action
{
    protected $validator;

    public function _construct()
    {
        $this->validator = Mage::getModel('backinstock/validator_subscriptionRequest');
    }

    public function subscribeAction()
    {
        try
        {
            $this->validator->validate($this->getRequest());
            $this->createSubscription();
        }
        catch (KL_BackInStock_Model_Exceptions_IncompleteForm $e)
        {
            $this->getResponse()->setHeader('Content-type', 'application/json', 400);
            $this->getResponse()->setHeader('HTTP/1.0','400', true);
            return $this->getResponse()->setBody($this->__($e->getMessage()));
        }
        catch (KL_BackInStock_Model_Exceptions_AlreadySubscribed $e)
        {
            $this->getResponse()->setHeader('Content-type', 'application/json', 400);
            $this->getResponse()->setHeader('HTTP/1.0','400', true);
            return $this->getResponse()->setBody($this->__($e->getMessage()));
        }
        catch (KL_BackInStock_Model_Exceptions_Unauthorized $e)
        {
            $this->getResponse()->setHeader('Content-type', 'application/json', 401);
            $this->getResponse()->setHeader('HTTP/1.0','401', true);
            return $this->getResponse()->setBody($this->__($e->getMessage()));
        }
        catch (Exception $e)
        {
            $this->getResponse()->setHeader('Content-type', 'application/json', 400);
            $this->getResponse()->setHeader('HTTP/1.0','400', true);
            return $this->getResponse()->setBody($this->__($e->getMessage()));
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', 200);
        $this->getResponse()->setHeader('HTTP/1.0','200', true);
        $this->getResponse()->setBody($this->__('Thank you. You will be notified once the product is back in stock.'));

    }

    protected function createSubscription()
    {
        Mage::getModel('productalert/stock')
            ->setCustomerId(
                Mage::helper('backinstock/customer')
                    ->loadByEmail($this->getRequest()->getPost('email'))
                    ->getId() ? : null
            )
            ->setProductId($this->getRequest()->getPost('product_id'))
            ->setEmail($this->getRequest()->getPost('email'))
            ->setName($this->getRequest()->getPost('name'))
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->save();
    }

}