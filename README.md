[inkifi.com](https://inkifi.com) (Magento 2).

## How to update all `inkifi/*` packages 
```                 
bin/magento maintenance:enable      
composer remove inkifi/consolidation
composer remove inkifi/core
composer remove inkifi/mediaclip
composer remove inkifi/missing-order  
composer remove inkifi/pwinty
composer remove inkifi/store   
rm -rf composer.lock
composer clear-cache
composer require inkifi/consolidation:*
composer require inkifi/core:*
composer require inkifi/mediaclip:*
composer require inkifi/missing-order:*  
composer require inkifi/pwinty:*
composer require inkifi/store:*  
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/code
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_GB
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme Infortis/ultimo \
	-f en_US en_GB
bin/magento maintenance:disable
```