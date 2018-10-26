# Magento 2 Membership
[![Join the chat at https://gitter.im/magefox/membership](https://badges.gitter.im/magefox/membership.svg)](https://gitter.im/magefox/membership?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://img.shields.io/travis/magefox/membership.svg?style=flat)](https://travis-ci.org/magefox/membership)
[![Latest Stable Version](https://poser.pugx.org/magefox/membership/v/stable)](https://packagist.org/packages/magefox/membership)
[![Total Downloads](https://poser.pugx.org/magefox/membership/downloads)](https://packagist.org/packages/magefox/membership)
[![Software License](https://img.shields.io/badge/license-MIT-green.svg?style=flat)](http://opensource.org/licenses/gpl-2.0.php)
## Description

Membership functionality for Magento 2.
<!--- [Try Demo](http://demo.magefox.com) --->

## Features
- 100% open source.
- Easily switch On/Off the Membership functionality.
- Supports Magento Multi-stores.

## Installation
### Manual (without composer)
- Download zip file of thelast version of this extension under release tab
- Extract files in the Magento root directory in the folder app/code/Magefox/Membership
- Enable the extension
```
php bin/magento --clear-static-content module:enable Magefox_Membership
```
- Upgrade Magento setup
```
php bin/magento setup:upgrade
```

### With Composer
In the Magento root directory
- Install the module
```
composer require magefox/membership
php bin/magento module:enable Magefox_Membership 
php bin/magento setup:upgrade
```

### Cleaning
- Upgrade Magento setup
```
php bin/magento setup:upgrade
```
- Clear cache
```
php bin/magento cache:flush
```

## Settings
The Settings of the extension are available at Stores > Configuration. At the Magefox tab, the "Membership" link.

There you will be able to fill several sections:
- **Enable Membership**. To enable or disable the Membership functionality.
- **Revoke Customer Group**. When membership expired, which customer group do you want them moved to.
- **Confirmed Order Status**. When a Membership product is purchased, which order status should used to enable Membership status.
- And another options depend your provider you selected.  

## Release Notes
### 1.0.0
* Initial version.
