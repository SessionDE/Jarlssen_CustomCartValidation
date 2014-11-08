Jarlssen_CustomCartValidation
=============================

Simple Magento extension that allows you to create cart validation rules by adding a few lines in your module config.xml and creating a simple class with three required methods.

You also can read a blog post about this extension: http://www.jarlssen.de/blog/2014/05/13/magento-create-custom-cart-validation-rules-module-for-developers

This is module is created for developers and basically the best way how you can use it is if you create your own module and you extend the functionality from this module.

First you have to create a dependecy to Jarlssen_CustomCartValidation in your module

```xml
<?xml version="1.0"?>
<config>
    <modules>
        <MyCompany_ExampleValidation>
            <active>true</active>
            <codePool>local</codePool>
            <depends>
                <Jarlssen_CustomCartValidation />
            </depends>
        </MyCompany_ExampleValidation>
    </modules>
</config>
```

After that add configuration in your module config.xml similar to:

```xml
<global>
 
        <jarlssen_custom_cart_validation>
            <rules>
                <different_manufacturer_not_allowed>
                    <product_type>simple</product_type>
                    <model>example_validation/validator_validateAllowedManufacturers</model>
                    <origin>example_validation</origin>
                    <code>1</code>
                </different_manufacturer_not_allowed>
                <multiple_quantity_validation>
                    <product_type>*</product_type>
                    <model>example_validation/validator_validateMultipleQty</model>
                    <origin>example_validation</origin>
                    <code>2</code>
                </multiple_quantity_validation>
            </rules>
        </jarlssen_custom_cart_validation>
 
</global>
```

Details about the example you need to follow:

 * If you want to add validation rule you need to your rules in *global/jarlssen_custom_cart_validation/rules*
you can use arbitrary names for the rules. In the example I called the first rule "different_manufacturer_not_allowed"
 * There are four required nodes in the rule configuration:
   * *product_type* - you can define for which products the rule will work or you can use "*" for all products
   * *model* - this is the path to the validation rule model (this is the place where you implement the validation logic)
   * *origin* - this can be a unique identifier, but most importantly it should be unique in combination with the code config node
   * *code* - this must be unique in combination with the origin

Create a model class in your module extending Jarlssen_CustomCartValidation_Model_Validator_Rule and create 3 methods in this class:

 * validate()
 * getQuoteItemMessage()
 * getQuoteMessage()
Basically the methods listed above are abstract methods in Jarlssen_CustomCartValidation_Model_Validator_Rule, so you are forced to create them, otherwise PHP will throw a fatal error.

The *validate()* method accepts as parameter a Quote item object and returns a boolean value, it should look like this example:

```php
/**
 * Implementation of the validation logic
 *
 * @param Mage_Sales_Model_Quote_Item $item
 * @return bool
 */
public function validate($item)
{
    $qty = $item->getQty();
 
    if($qty % 10 != 0) {
        return false;
    }
 
    return true;
}
```

In this example I check if the quantity of the quote item is a multiple of 10.

The method *getQuoteItemMessage()* returns the text we usually show under the product name in the cart. Example:

```php
/**
 * In case we got validation error we have to specify and error
 * message, that will be show in the quote item line
 *
 * @return string
 */
public function getQuoteItemMessage()
{
    return "The quantity must be multiple times of 10";
}
```

The method *getQuoteMessage()* returns the text we usually show as a general error message in the cart. Example:

```php
/**
 * In case we got validation error we have to specify an error
 * message, that will be shown in the quote
 * (usually shown in the cart heading section or product heading section 
 *
 * @return string
 */
public function getQuoteMessage()
{
    return "Not allowed product quantity in the cart";
}
```
