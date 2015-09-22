#!/usr/bin/env bash

apt-get update
apt-get install -y python-software-properties curl

# you can get latest php like this
add-apt-repository ppa:ondrej/php5

# now lets install it along with nginx
apt-get -q -y install php5 php5-cli php5-fpm php5-gd php5-curl php5-mysql nginx rabbitmq-server

rm -rf /var/www /usr/share/nginx/www
ln -s /vagrant /var/www
ln -s /vagrant /usr/share/nginx/www

cat /vagrant/deploy/default.conf > /etc/nginx/sites-available/default

/etc/init.d/nginx restart

# install composer
curl -sS https://getcomposer.org/installer | php

ln -s /home/vagrant/composer.phar /usr/bin/composer

# install MySQL
export DEBIAN_FRONTEND=noninteractive
debconf-set-selections <<< 'mysql-server mysql-server/root_password password'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password'
apt-get -q -y install mysql-server

mysql -u root < /vagrant/deploy/prod-database.sql
mysql -u root < /vagrant/deploy/test-database.sql

cd /vagrant
