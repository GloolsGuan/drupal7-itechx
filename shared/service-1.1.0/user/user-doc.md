# Account Service (0.1)
    Base on ucenter system.

### Description
The account module is an full life circle user(account) support service, with full of RBAC supported.
We separated and independence the user system for supporting all platform account requirement.
This module is an client app side of "UCenter system".


### Basic informations
- Ucenter server :  uc.dteols.[cn(dev)|com.cn(life server)]
- An sample of Api controller for AccountService resources/UcSampleController.class.php

###### Depend on:
- Api Controller: /api/uc
- EasyYii2.x-0.2 https://github.com/GloolsGuan/EasyYii
- Yii2.x
- UCenter 1.6.0 Release 20110501
- php/Curl

###### Resources:
- UcSampleController.class.php


### Installation
1. Place resources/UcSampleController.class.php to your controllers folder and rename it as UcController.class.php.
2. Set currect namespace of UcController.class.php which you place it just now.
3. Set linux/hosts(location: /etc/hosts) specified the domain uc.dteols.[cn|com.cn] with internal IP address.Note: (UCetner Server) does not work for public internet. You must visit your ucenter server via internal net or local net.
4. Add and app in "UCenter system".
5. Running sample of test:
   Visit: [Your domain]/api/uc/sample


### Usage
``` php
$uc_params = array(
    'uc_key' => UC_KEY
    'uc_api' => UC_API
);
$sAccount = \Yii::loadService('accountAccount', $uc_params);
$sAccount->load($user_id | $user_code);
```
###### UC SETTING PARAMETERS
