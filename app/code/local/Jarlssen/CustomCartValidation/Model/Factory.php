<?php
/**
 * @author      Tsvetan Stoychev <tsvetan.stoychev@jarlssen.de>
 * @website     http://www.jarlssen.de
 */

class Jarlssen_CustomCartValidation_Model_Factory
{
    /**
     * Spawn the correct validator object and init it
     *
     * @param $rulesConfig array
     * @param Mage_Sales_Model_Quote_Item $item
     * @return Jarlssen_CustomCartValidation_Model_Factory
     */
    public function getRuleModels($rulesConfig, $item)
    {
        $ruleModels = array();
        $productType = $item->getProductType();

        foreach($rulesConfig as $config) {
            if('*' == $config['product_type'] || $config['product_type'] == $productType) {
                try {
                    $ruleModel = Mage::getModel($config['model']);

                    $ruleModel
                        ->setItem($item)
                        ->setCode($config['code'])
                        ->setOrigin($config['origin']);

                    $ruleModels[] = $ruleModel;
                } catch(Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        return $ruleModels;
    }
}
