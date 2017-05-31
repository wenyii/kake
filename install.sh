#! /bin/bash

# composer
composer install

# add write
sudo chmod -R a+w common/config/
sudo chmod -R a+w console/config/
sudo chmod -R a+w mixed/
sudo chmod -R a+w backend/runtime/
sudo chmod -R a+w frontend/runtime/
sudo chmod -R a+w backend/web/assets/
sudo chmod -R a+w frontend/web/assets/

# create config files
sudo cp common/config/main-local.php.backup common/config/main-local.php
sudo cp common/config/params-local.php.backup common/config/params-local.php

sudo cp backend/config/main-local.php.backup backend/config/main-local.php
sudo cp backend/config/params-local.php.backup backend/config/params-local.php

sudo cp frontend/config/main-local.php.backup frontend/config/main-local.php
sudo cp frontend/config/params-local.php.backup frontend/config/params-local.php

sudo cp console/config/main-local.php.backup console/config/main-local.php
sudo cp console/config/params-local.php.backup console/config/params-local.php

echo
read -p "Please choose environment. [dev/prod]: " env
if [ "${env}" != "dev" -a "${env}" != "prod" ]
then
    alert 31 'Environment must be dev/prod!'
    exit 1
fi

sudo cp yii-${env} yii
sudo chmod a+x yii

sudo cp backend/web/index-${env}.php backend/web/index.php
sudo cp frontend/web/index-${env}.php frontend/web/index.php