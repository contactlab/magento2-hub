![Public previe](https://img.shields.io/badge/release-Public%20preview-yellow.svg)

# Contacthub Connect for Magento 2.x  
### Version 1.0.0
# Installation and Configuration Guide  

----------

## Contents

- [Introduction](#Introduction)  
- [Requirements](#Requirements)
- [Installing the Magento plug-in](#InstallingPlugIn)  
- [Configuring the Magento plug-in](#ConfiguringPlugIn)
- [Appendix A: Which customer data and activities does the Magento plug-in save in Contacthub?](#AppendixA)
- [Appendix B: How to set up automated emails for abandoned shopping cart](https://explore.contactlab.com/do-you-want-to-remind-your-customers-that-they-have-an-abandoned-cart/?lang=en) 

<a name="Introduction"/>

## Introduction  

The Contacthub Magento 2 plug-in enables you to automatically send all the activities your customers undertake on the e-commerce platform to Contacthub, without the need to write any code. The extension automatically collects individual customer information such as:
- The pages they visit.
- The products they add to, or remove from, their shopping cart.
- The orders they complete.

It also ensures potentially valuable information is not lost, by retaining events and actions that are related to customers who have not yet been identified because, for example, they are not logged in, or they have not yet registered. The data is stored by Contacthub, ready to be associated with the customer as soon as they are identified later.

Installing the plug-in is very simple, while configuring it requires just a few minutes.  


<a name="Requirements"/>

## Requirements  

The Contacthub Magento 2 plug-in requires Magento CE version 2.x. Further details about Magento 2 system requirements are available on the [Magento website](https://devdocs.magento.com/guides/v2.0/install-gde/system-requirements2.html).

An alternative version of the Contacthub Magento plug-in for Magento CE version 1.9 is available on GitHub, at [https://github.com/contactlab/contacthub-connect-magento](https://github.com/contactlab/contacthub-connect-magento).

<a name="InstallingPlugIn"/>

## Installing the Magento 2 plug-in

- Log in to **Magento Admin**, click **System** > **Cache Management** and enable all cache types.  

- It is recommended that you install the plug-in using composer, by running the following command in the Magento 2 root folder:

```
composer require contactlab/magento2-hub
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```
- Click **System** > **Cache Management** and then click **Flush Cache Storage**.  

Once you have done this, the extension installation is complete.

- Log out of **Magento Admin** and then log back in again, to allow Magento to refresh permissions.  


<a name="ConfiguringPlugIn"/>

## Configuring the Magento plug-in

To configure the plug-in, do the following:  

- Log in to **Magento Admin**, then click **Contactlab** > **Configuration** > **Hub**.  

- Under **Settings**, enter or paste the **APIToken**, **API Workspace ID** and **API Node ID** details in the appropriate fields.

- Under **Javascript tracking** > **Enable JS tracking**, select **Yes**, then enter or paste the **Untrusted token** from Hub. 

- Under **Behavior** and **Logging**, select the required options. 

- Under **Events**, do the following:  

    - Enable the customer events that you want to trace.  

    - Enter a name for the **Newsletter Campaign**.

- Under **Exchange Rates**, select the appropriate **Base Currency**, or click **Use system value**.

- Under **Abandoned Carts**, do the following:

    - Select whether you want to **Send Abandoned Cart events from non-subscribed customers**.  
    
      If you select **No**, Contacthub only tracks Abandoned Cart events for customers who are subscribed to a newsletter.  
      
    - Enter the **Minimum number of minutes before sending an Abandoned Cart event**.  
    
    - Under **Maximum number of minutes before sending an Abandoned Cart event**, enter the maximum number of minutes that an Abandoned cart event should be tracked.  
    
      Required to avoid tracking older events.  

- Under **Customer Extra Properties**, do the following:

    - Under **External ID**, select the appropriate attribute to use as an identifier, if required.
    
    - To map a Hub attribute to a Magento one, do the following, as required:
    
        - Under **CustomerAttribute**, click **Add**.
        
        - Enter the name of the **Hub Attribute**, then select the **Hub Type**, for example, a **Base** property.
        
        - Select the matching **Magento Attribute**.
        
        - Map further attributes as required, by clicking **Add** and repeating the above steps.

- Under **Cron Export Events Settings** > **Limit Events to export** set the appropriate limit, for example, 30.

- Under **Cron Previous Customers Settings**, do the following, as required, to configure the export of earlier customer settings to Hub:

    - Under **Enabled**, select **Yes** to activate exporting, or **No** to disable it.
    
    - Under **Export Previous Orders**, select the appropriate setting.
    
    - Under **Limit Customers to export**, set the appropriate limit.
    
    - Click **Reset Previous Customers** to reset their data.

      **Note:**
      This action cannot be undone.

- When you are finished, click **Save Config**.

- Click **Contactlab** > **Event Log** to see the events that have been processed, or **Dashboard** for a summary analysis of key indicators.

 <a name="AppendixA"/>

 # Appendix A: Which customer data and activities does the Magento plug-in save in Contacthub?

 ## Customer Profile Data

| Contacthub        | Magento           | Note  |
| :------------- |:-------------| :-----|
| title      | getModel("customer/customer")->getPrefix() |
| firstName      | getModel("customer/customer")->getFirstname()|   
| lastName |getModel("customer/customer")->getLastname() |   
| gender | getModel("customer/customer")->getGender() == 1 ? 'Male' : 'Female'
| dob | date('Y-m-d', strtotime(getModel("customer/customer")->$customer->getDob()))
| locale | getStoreConfig('general/locale/code', $this->getStoreId())
| contacts.email | getIdentityEmail()
| address.street | getModel('customer/address')->getStreet()| Address data loaded from getDefaultBilling()|
| address.city | getModel('customer/address')->getRegion()| Address data loaded from getDefaultBilling()|
| address.country | getModel('directory/country')->load(getModel('customer/address')->getCountry())->getName()| Address data loaded from getDefaultBilling()|
| address.province | getModel('customer/address')->getRegion()| Address data loaded from getDefaultBilling()|
| address.zip | getModel('customer/address')->getPostcode()| Address data loaded from getDefaultBilling()|


## Activities


| Contacthub                        | Magento           |
| :------------------------------- |:-----------------------|
| Viewed product                  | When the customer views a product.|
| Viewed product category         | When the customer views a product listing belonging to a specific category.|
| Added product                   | When the customer adds a product to their shopping cart.|
| Removed product                 | When the customer removes a product from their shopping cart.|
| Added product to wishlist       | When the customer adds a product to their wishlist.|
| Removed product from wishlist   | When the customer removes a product from their wishlist.|
| [Order completed](#OrderCompleted)                 | [When the customer completes an order.](#OrderCompleted)
| Logged in                       | When the customer logs in to their account.|
| Logged out                      | When the customer logs out of their account.|
| Subscribed to newsletter        | When the customer subscribes to your newsletter.|
| Unsubscribed from newsletter    | When the customer unsubscribes from your newsletter.|
| Order shipped                   | When your company ships the products in the order.|
| Abandoned cart                  | When the customer added a product to their cart, but did not complete the order/transaction.|


<a name="OrderCompleted"/>

**Order Completed**

To identify the order data use: $order = Mage::getModel('sales/order')->loadByIncrementId($eventData->increment_id)<br />
To export each individual product (item) included in the order use: $order->getAllItems() as $item<br />
To export all the details of each individual product (item) use: $product = Mage::getModel('catalog/product')->load($product_id)

| Contacthub        | Magento           | Note  |
| :------------- |:-------------| :-----|
| Contacthub      |$order->getIncrementId()|
| type      | sale|   
| storeCode |$order->getStoreId()|
| paymentMethod | Not available  |
| amount.local.exchangeRate | $order->getStoreToOrderRate()| This is the exchange rate used when the data is loaded.|
| amount.local.currency | $order->getOrderCurrencyCode()
| amount.total | $order->getGrandTotal()
| amount.revenue | $order->getGrandTotal() - $order->getShippingAmount() - $order->getShippingTaxAmount()
| amount.shipping | $order->getShippingAmount() + $order->getShippingTaxAmount()
| amount.tax | $order->getTaxAmount()
| amount.discount | $order->getDiscountAmount()
| products.type | sale
| products.id | $product->getEntityId()
| products.sku | $product->getSku()
| product.name | $product->getName()
| products.linkUrl | Mage::getUrl($product->getUrlPath())
| products.imageUrl | Mage::helper('catalog/image')->init($product, 'image')
| products.shortDescription | $product->getShortDescription()
| products.category | $this->getCategoryNamesFromIds($product->getCategoryIds())
| products.price | $item->getPrice()
| products.subtotal | $item->getRowTotal()
| products.quantity | $item->getQtyOrdered()
| products.discount | $item->getDiscountAmount()
| products.tax | $item->getTaxAmount()
| product.coupon | $order->getCouponCode()
| context.store.id | Mage::app()->getStore()->getStoreId()
| context.store.name | Mage::app()->getStore()->getName()
| context.store.country | Mage::getStoreConfig('general/country/default')
| context.store.website | Mage::getUrl('', array('store' => Mage::app()->getStore()->getStoreId()))
| context.store.type | ECOMMERCE


**IMPORTANT:**

Any customizations in the Magento path will not be exported to, or available in Contacthub, as these could compromise the plug-in operation.



----------
