<?php
/**
 * @author      Tsvetan Stoychev <tsvetan.stoychev@jarlssen.de>
 * @website     http://www.jarlssen.de
 */

class Jarlssen_CustomCartValidation_Model_Validator
{

    /**
     * @var array
     */
    protected $_ruleModels = array();

    /**
     * @var Varien_Data_Collection
     */
    protected $_errors;

    /**
     * Here we keep the origins and codes of all validations, that were passe,
     * because we may need them after the validation.
     *
     * For example when we remove a validation error for the "checkout session" or somewhere else
     *
     * @var Varien_Data_Collection
     */
    protected $_passedValidations;

    /**
     * Checks the error flag
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->_errors) > 0;
    }

    /**
     * Getter for errors collection
     *
     * @return null|Varien_Data_Collection
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Getter for passed validations collection
     *
     * @return null|Varien_Data_Collection
     */
    public function getPassedValidations()
    {
        return $this->_passedValidations;
    }

    /**
     * Add an error to the errors collection
     *
     * @param string $origin
     * @param string $code
     * @param string $itemMessage
     * @param string $quoteMessage
     *
     * @return Jarlssen_CustomCartValidation_Model_Validator
     */
    public function addError($origin, $code, $itemMessage, $quoteMessage)
    {
        $error = new Varien_Object(
            array(
                'origin'             => $origin,
                'code'               => $code,
                'quote_item_message' => $itemMessage,
                'quote_message'      => $quoteMessage
            )
        );

        $this->_errors->addItem($error);

        return $this;
    }

    /**
     * Add passed valition to passed validations collection
     *
     * @param string $origin
     * @param string $code
     *
     * @return Jarlssen_CustomCartValidation_Model_Validator
     */
    public function addPassedValidation($origin, $code)
    {
        $passedValidation = new Varien_Object(
            array(
                'origin' => $origin,
                'code'   => $code
            )
        );

        $this->_passedValidations->addItem($passedValidation);

        return $this;
    }

    /**
     * Init the validator and setting the rules
     *
     * @param $item
     * @return Jarlssen_CustomCartValidation_Model_Validator
     */
    public function init($item)
    {
        $this->_errors = new Varien_Data_Collection();
        $this->_passedValidations = new Varien_Data_Collection();

        $this->_initRuleModels($item);
        $this->_validate();

        Mage::dispatchEvent('jarlssen_custom_cart_validation_validate_after',
            array('validator' => $this, 'item' => $item)
        );

        return $this;
    }

    /**
     * Init rules models needed to process the validation
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return Jarlssen_CustomCartValidation_Model_Validator
     */
    protected function _initRuleModels($item)
    {
        $rulesConfig = Mage::helper('jarlssen_custom_cart_validation')->getAllRules();

        $rulesFactory = Mage::getModel('jarlssen_custom_cart_validation/factory');
        $this->_ruleModels = $rulesFactory->getRuleModels($rulesConfig, $item);

        return $this;
    }

    /**
     * In this function we iterate over all rules and mark the validation as:
     * - passed
     * - and not passed
     *
     * We store the both cases ( passed and not passed ) in Varien_Data_Collection,
     * because later we will need the errors to add them in the quote and quote items
     * and the passed elements, because we need to remove the errors from the quote and quote items
     *
     * @return Jarlssen_CustomCartValidation_Model_Validator
     */
    protected function _validate()
    {
        foreach($this->_ruleModels as $ruleModel) {
            /** @var Jarlssen_CustomCartValidation_Model_Validator_Rule $ruleModel */
            if(!$ruleModel->isValid()) {
                $this->addError($ruleModel->getOrigin(), $ruleModel->getCode(), $ruleModel->getQuoteItemMessage(), $ruleModel->getQuoteMessage());
            } else {
                $this->addPassedValidation($ruleModel->getOrigin(), $ruleModel->getCode());
            }
        }
        return $this;
    }

}