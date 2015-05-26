<?php

trait HandleAttributes
{
    /**
     * @param Mage_Catalog_Model_Product $simple
     * @param Mage_Catalog_Model_Product $configurable
     * @param array                      $attributeIds
     * @param array                      $data
     * @return mixed
     */
    public function associateWithConfigurable(Mage_Catalog_Model_Product $simple, Mage_Catalog_Model_Product $configurable, $attributeIds = [92], $data = [])
    {
        $configurable->getTypeInstance()->setUsedProductAttributeIds($attributeIds);
        $configurableAttributesData = $configurable->getTypeInstance()->getConfigurableAttributesAsArray($configurable);
        $configurable->setCanSaveConfigurableAttributes(true);
        $configurable->setConfigurableAttributesData($configurableAttributesData);

        if (!count($data)) {
            $data[$simple->getId()] = [
                '0' => [
                    'label' => 'Green', //attribute label
                    'attribute_id' => '92', //attribute ID of attribute 'color'
                    'value_index' => '3', //value of 'Green' index of the attribute 'color'
                    'is_percent' => '0', //fixed/percent price for this option
                    'pricing_value' => '21' //value for the pricing
                ]
            ];
        }
        $configurable->setConfigurableProductsData($data);
        return $configurable->save();
    }

    public function addAttributeToAttributeSet($attributeId, $attributeSetId, $attributeGroupName = 'General')
    {
        $model = Mage::getModel('eav/entity_setup','core_setup');
        $attributeGroupId = $model->getAttributeGroup('catalog_product', $attributeSetId, $attributeGroupName);
        $model->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
    }

    public function addAttributeOption($attributeCode, $attributeValue)
    {
        $attributeModel = Mage::getModel('eav/entity_attribute');
        $attributeOptionsModel= Mage::getModel('eav/entity_attribute_source_table') ;

        $attributeId = $attributeModel->getIdByCode('catalog_product', $attributeCode);
        $attribute = $attributeModel->load($attributeId);

        $attributeOptionsModel->setAttribute($attribute);
        $options = $attributeOptionsModel->getAllOptions(false);

        foreach($options as $option) {
            // checking if already exists
            if ($option['label'] == $attributeValue) {
                $optionId = $option['value'];
                return $optionId;
            }
        }

        $value['option'] = array($attributeValue, $attributeValue);
        $result = array('value' => $value);
        $attribute->setData('option', $result);
        return $attribute->save();
    }

}
