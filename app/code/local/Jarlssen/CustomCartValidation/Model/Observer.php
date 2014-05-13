<?php
/**
 * @author      Tsvetan Stoychev <tsvetan.stoychev@jarlssen.de>
 * @website     http://www.jarlssen.de
 */

class Jarlssen_CustomCartValidation_Model_Observer
{

    /**
     * Tries to apply validation rules to quote item
     *
     * @param  Varien_Event_Observer $observer
     * @return Jarlssen_CustomCartValidation_Model_Observer
     */
    public function quoteItemValidations($observer)
    {
        $quoteItem = $observer->getEvent()->getItem();

        /* @var $quoteItem Mage_Sales_Model_Quote_Item */
        if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote()
            || $quoteItem->getQuote()->getIsSuperMode()) {
            return $this;
        }

        $validator = Mage::getModel('jarlssen_custom_cart_validation/validator')->init($quoteItem);

        if($validator->hasErrors()) {
            foreach($validator->getErrors() as $error) {
                $quoteItem->addErrorInfo(
                    $error->getOrigin(),
                    $error->getCode(),
                    $error->getQuoteItemMessage()
                );

                $quoteItem->getQuote()->addErrorInfo(
                    'custom_validation_error',
                    $error->getOrigin(),
                    $error->getCode(),
                    $error->getQuoteMessage()
                );
            }
        } else {
            foreach($validator->getPassedValidations() as $passedValidation) {
                $this->_removeErrorsFromQuoteAndItem($quoteItem, $passedValidation->getOrigin(), $passedValidation->getCode());
            }
        }

        return $this;
    }

    /**
     * Removes error statuses from quote and item, set by this observer
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param string $origin
     * @param int $code
     * @return Jarlssen_CustomCartValidation_Model_Observer
     */
    protected function _removeErrorsFromQuoteAndItem($item, $origin, $code)
    {
        if ($item->getHasError()) {
            $params = array(
                'origin' => $origin,
                'code' => $code
            );
            $item->removeErrorInfosByParams($params);
        }

        $quote = $item->getQuote();
        $quoteItems = $quote->getItemsCollection();
        $canRemoveErrorFromQuote = true;

        foreach ($quoteItems as $quoteItem) {
            if ($quoteItem->getItemId() == $item->getItemId()) {
                continue;
            }

            $errorInfos = $quoteItem->getErrorInfos();
            foreach ($errorInfos as $errorInfo) {
                if ($errorInfo['code'] == $code) {
                    $canRemoveErrorFromQuote = false;
                    break;
                }
            }

            if (!$canRemoveErrorFromQuote) {
                break;
            }
        }

        if ($quote->getHasError() && $canRemoveErrorFromQuote) {
            $params = array(
                'origin' => $origin,
                'code' => $code
            );
            $quote->removeErrorInfosByParams(null, $params);
        }

        return $this;
    }
}