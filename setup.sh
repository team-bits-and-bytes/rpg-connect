#!/bin/bash

# copy apache config file
sudo cp ./001-cloud9.conf /etc/apache2/sites-enabled/001-cloud9.conf

# install dependencies
composer install

# start the project databases
mysql-ctl start
sudo service redis-server start

# run our application migrations
php tasks.php migrate