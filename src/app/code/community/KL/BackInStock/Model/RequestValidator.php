<?php

interface KL_BackInStock_Model_RequestValidator
{
    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @return mixed
     */
    public function validate(Mage_Core_Controller_Request_Http $request);
}