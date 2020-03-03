[inkifi.com](https://inkifi.com) (Magento 2).

## How to update all `inkifi/*` packages 
```
bin/magento maintenance:enable      
php -d memory_limit=-1 /usr/bin/composer remove inkifi/consolidation
php -d memory_limit=-1 /usr/bin/composer remove inkifi/core
php -d memory_limit=-1 /usr/bin/composer remove inkifi/mediaclip
php -d memory_limit=-1 /usr/bin/composer remove inkifi/mediaclip-legacy
php -d memory_limit=-1 /usr/bin/composer remove inkifi/missing-order
php -d memory_limit=-1 /usr/bin/composer remove inkifi/pwinty
php -d memory_limit=-1 /usr/bin/composer remove inkifi/store
rm -rf composer.lock
php -d memory_limit=-1 /usr/bin/composer clear-cache
php -d memory_limit=-1 /usr/bin/composer require inkifi/consolidation:*
php -d memory_limit=-1 /usr/bin/composer require inkifi/core:*
php -d memory_limit=-1 /usr/bin/composer require inkifi/mediaclip:*
php -d memory_limit=-1 /usr/bin/composer require inkifi/mediaclip-legacy:*
php -d memory_limit=-1 /usr/bin/composer require inkifi/missing-order:*
php -d memory_limit=-1 /usr/bin/composer require inkifi/pwinty:*
php -d memory_limit=-1 /usr/bin/composer require inkifi/store:*
rm -rf var/di var/generation generated/code
bin/magento setup:upgrade
bin/magento cache:enable
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