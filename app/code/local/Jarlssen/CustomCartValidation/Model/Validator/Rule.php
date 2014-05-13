<?php
/**
 * @author      Tsvetan Stoychev <tsvetan.stoychev@jarlssen.de>
 * @website     http://www.jarlssen.de
 */

abstract class Jarlssen_CustomCartValidation_Model_Validator_Rule
{

    /**
     * This is the entry point where we must implement our rule logic
     * The good news is, that we have access the the quote item objects,
     * so this makes it possible to have access to different objects like:
     *  - custom options
     *  - Quote
     *  - Product
     *  - and etc.
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return bool
     */
    abstract public function validate($item);

    /**
     * In case we got validation error we have to specify and error
     * message, that will be show in the quote item line
     *
     * @return string
     */
    abstract public function getQuoteItemMessage();

    /**
     * In case we got validation error we have to specify and error
     * message, that will be show in the quote
     * ( usually show in the cart heading section or product heading section )
     *
     * @return string
     */
    abstract public function getQuoteMessage();


    /**
     * This is the entry point of our rule method and
     * here we fire all needed needed action to check
     * the validity and to prepare the validation error messages
     *
     * @return bool
     */
    public function isValid()
    {
        $isValid = $this->validate($this->_item);
        if(!$isValid) {
            $this->_quoteItemMessage = $this->getQuoteItemMessage();
            $this->_quoteMessage = $this->getQuoteMessage();
        }
        return $isValid;
    }

    /**
     * @var null|Mage_Sales_Model_Quote_Item
     */
    protected $_item = null;

    /**
     * @var null|string
     */
    protected $_origin = null;

    /**
     * @var null|string
     */
    protected $_code = null;

    /**
     * @var null|string
     */
    protected $_quoteItemMessage = null;

    /**
     * @var null|string
     */
    protected $_quoteMessage = null;

    /**
     * @param null|string $code
     * @return Jarlssen_CustomCartValidation_Model_Validator_Rule;
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param null|string $origin
     * @return Jarlssen_CustomCartValidation_Model_Validator_Rule;
     */
    public function setOrigin($origin)
    {
        $this->_origin = $origin;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getOrigin()
    {
        return $this->_origin;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item
     * @return Jarlssen_CustomCartValidation_Model_Validator_Rule;
     */
    public function setItem($item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Quote_Item|null
     */
    public function getItem()
    {
        return $this->_item;
    }

}