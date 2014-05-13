<?php
/**
 * @author      Tsvetan Stoychev <tsvetan.stoychev@jarlssen.de>
 * @website     http://www.jarlssen.de
 */

class Jarlssen_CustomCartValidation_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * todo: Add configuration validation, that checks if all required xml config nodes are populated
     * 
     */

    const RULES_XML_PATH = 'global/jarlssen_custom_cart_validation/rules';

    /**
     * Fetch all rules from the global config
     *
     * @return array
     */
    public function getAllRules()
    {
        $rulesConfig = array();

        $rules = Mage::getConfig()
            ->getNode(self::RULES_XML_PATH);

        if(!empty($rules)) {
            $rulesConfig = $rules->asCanonicalArray();
        }

        return $rulesConfig;
    }
}