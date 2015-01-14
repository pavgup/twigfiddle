twigfiddle
==========

twigfiddle.com provides a small development environment to develop, run, store and access Twig code online.


# Installation

Here are instructions to get started with the application. Replace the path names and

```sh
# Clone the project
sudo mkdir -p /fuz/twigfiddle.com
sudo chown www-data:www-data /fuz/twigfiddle.com
sudo su www-data
git clone https://github.com/ninsuo/twigfiddle.git ./

# install Composer
php -r "readfile('https://getcomposer.org/installer');" | php
sudo mv composer.phar /usr/bin/composer
sudo chmod 755 /usr/bin/composer

# Configure apache
# do not forget to edit apache2/005-twigfiddle-com.conf to change host and dirs first!
sudo mkdir -p /fuz/twigfiddle.com/files /fuz/twigfiddle.com/logs /fuz/twigfiddle.com/sessions.com /fuz/twigfiddle.com/tmp
sudo cp install/005-twigfiddle-com.conf /etc/apache2/sites-available

# Install the fiddle runner and prepare all twig versions
cd cli
composer update
cd twig
sh prepare.sh
cd ../../

# Install the web application
# composer update will fail when trying to remove apc cache, that's normal at this step
cd web
composer update

# If you want to use SensioLabs Connect, you need to patch HWIOAuthBundle
# See https://github.com/hwi/HWIOAuthBundle/pull/657
patch -p9 vendor/hwi/oauth-bundle/HWI/Bundle/OAuthBundle/OAuth/ResourceOwner/AbstractResourceOwner.php < ../install/HWIOAuthBundle_AbstractResourceOwner.patch

# Install the database
# You should create it yourself, from mysql, type:
# CREATE DATABASE twigfiddle
# GRANT ALL PRIVILEGES ON twigfiddle.* To '<user>'@'127.0.0.1' IDENTIFIED BY '<password>';
php app/console doctrine:schema:drop --force
php app/console doctrine:schema:create
php app/console twigfiddle:import ../samples/*

# Enable the application
sudo ln -s /etc/apache2/sites-available/005-twigfiddle-com.conf /etc/apache2/sites-enabled/005-twigfiddle-com.conf
sudo service apache2 restart

# Check that everything is working properly
composer update
php app/check.php
```

# Configure OAuth resource owners

Google: https://console.developers.google.com/project
Facebook: https://developers.facebook.com/apps/
Twitter: https://apps.twitter.com/
SensioLabs Connect: https://connect.sensiolabs.com/account/apps

