# Membership for Magento 2
[![Join the chat at https://gitter.im/magefox/membership](https://badges.gitter.im/magefox/membership.svg)](https://gitter.im/magefox/membership?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://img.shields.io/travis/magefox/membership.svg?style=flat)](https://travis-ci.org/magefox/membership)
[![Latest Stable Version](https://poser.pugx.org/magefox/membership/v/stable)](https://packagist.org/packages/magefox/membership)
[![Total Downloads](https://poser.pugx.org/magefox/membership/downloads)](https://packagist.org/packages/magefox/membership)
[![Software License](https://img.shields.io/badge/license-MIT-green.svg?style=flat)](https://opensource.org/licenses/MIT)
## Description

Membership extension for Magento 2 brings comfort and convenience for the owner stores to create and manage their membership packages. It offers a complete membership solution, allowing you to accept member payments, manage members, create subscription plans
<!--- [Try Demo](http://demo.magefox.com) --->

[![IMAGE ALT TEXT HERE](https://raw.githubusercontent.com/magefox/membership/master/.github/images/with-player.png)](http://www.youtube.com/watch?v=sGdYDtqSssE)

## Features
- 100% open source.
- Easily switch On/Off the Membership functionality.
- Assign user to a group when come to membership.
- Daily check membership status.
- Supports Magento Multi-stores.
- Compatible with CE: 2.2

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

## Release Notes
### 1.0.3
- Fix issue Update Qty in Backend
- Improved performance
- Bug fixes

### 1.0.2
- Move membership section page to "My Membership" page
- Add cancel subscription action
- Improved performance
- Bug fixes

### 1.0.1
- Remove custom options in membership product type
- Verify Magento Extension Quality Program Coding Standard
- Add note to member expiry date
- Fix Uninstall scripts

### 1.0.0
* Initial version.
