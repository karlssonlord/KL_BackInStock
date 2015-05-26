<?php

use Laracasts\Integrated\Extensions\Selenium;
use MageTest\Manager\Factory;

class ModalTest extends Selenium
{
    use HandleAttributes;

    protected $configurableProduct;

    public function setUp()
    {
        Mage::init();

        $this->addAttributeToAttributeSet(92, 4);
        $this->attribute = $this->addAttributeOption('color', 'foo_' . rand(10000,1000000));
        $this->configurableProduct = $this->prepareConfigurableProduct();
    }
    
    public function tearDown()
    {
        //
    }
    
    /**
     * @test
     */
    public function it_does_something()
    {
        $this
            ->visit('/catalog/product/view/id/95')
            ->andSee('Test-hans')
            ->click('Monitor size')
            ->andSee('Notifications')
        ;
    }

    protected function prepareConfigurableProduct()
    {
        $simpleProduct = Factory::make('catalog/product', ['color' => '4']);

        // Create configurable product and assign simple product to it
        $configProduct = Mage::getModel('catalog/product');

        $configProduct
            ->setStoreId(1) //you can set data in store scope
            ->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
            ->setAttributeSetId(4) //ID of a attribute set named 'default'
            ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) //product type
            ->setCreatedAt(strtotime('now')) //product creation time
            ->setUpdatedAt(strtotime('now')) //product update time
            ->setSku('configurable96-'.time().rand(100,999))//SKU
            ->setName('Testproduct_' . rand(123123,123123123123)) //product name
            ->setWeight(0)
            ->setStatus(1) //product status (1 - enabled, 2 - disabled)
            ->setTaxClassId(2) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
            ->setManufacturer(28) //manufacturer id
            ->setNewsFromDate('06/26/2014') //product set as new from
            ->setNewsToDate('06/30/2014') //product set as new to
            ->setCountryOfManufacture('AF') //country of manufacture (2-letter country code)
            ->setPrice(11.22) //price in form 11.22
            ->setCost(22.33) //price in form 11.22
            ->setSpecialPrice(00.44) //special price in form 11.22
            ->setSpecialFromDate('06/1/2014') //special price from (MM-DD-YYYY)
            ->setSpecialToDate('06/30/2014') //special price to (MM-DD-YYYY)
            ->setMsrpEnabled(1) //enable MAP
            ->setMsrpDisplayActualPriceType(1) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
            ->setMsrp(99.99) //Manufacturer's Suggested Retail Price
            ->setMetaTitle('test meta title 2')
            ->setMetaKeyword('test meta keyword 2')
            ->setMetaDescription('test meta description 2')
            ->setDescription('This is a long description')
            ->setShortDescription('This is a short description')
            ->setMediaGallery(array('images' => array(), 'values' => array())) //media gallery initialization
            ->setCategoryIds(array(2)) //assign product to categories
            ->setCanBeSubscribedTo(1)
        ;
        /**/
        /** assigning associated product to configurable */
        /**/
        $configProduct->getTypeInstance()->setUsedProductAttributeIds(array(92)); //attribute ID of attribute 'color' in my store
        $configurableAttributesData = $configProduct->getTypeInstance()->getConfigurableAttributesAsArray();

        $configProduct->setCanSaveConfigurableAttributes(true);
        $configProduct->setConfigurableAttributesData($configurableAttributesData);
        $configurableProductsData = array();
        $configurableProductsData[$simpleProduct->getId()] = array( // id of a simple product associated with this configurable
            '0' => array(
                'label' => $this->attribute->getLabel(), //attribute label
                'attribute_id' => '92', //attribute ID of attribute 'color' in my store
                'value_index' => $this->attribute->getValueIndex(), //value of 'Green' index of the attribute 'color'
                'is_percent' => '0', //fixed/percent price for this option
                'pricing_value' => '21' //value for the pricing
            )
        );

        $configProduct->setConfigurableProductsData($configurableProductsData);

        $configProduct->setStockData(array(
                'use_config_manage_stock' => 0, //'Use config settings' checkbox
                'manage_stock' => 1, //manage stock
                'is_in_stock' => 1, //Stock Availability
            )
        );

        $configProduct->save();

        return $configProduct;
    }
}
