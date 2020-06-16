# -*- mode: ruby -*-
# vi: set ft=ruby :

BOX_IP = "192.168.10.12"
DB_NAME = "nanocenter"

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/xenial64"
  # config.vm.box_check_update = true
  config.vm.network "private_network", ip: BOX_IP
  config.vm.synced_folder "./", "/var/www/html/"
  config.vm.network "forwarded_port",guest:3306, host:3306

  config.vm.provision "shell", inline: <<-SHELL

    sudo apt-add-repository ppa:ondrej/php

    apt-get update -y
    apt-get install apache2 -y
    sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password password'
    sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password password'

    sudo apt-get install php7.2 -y
    sudo apt-get install php7.2-mbstring
    sudo apt-get install mysql-server php7.2-mysqli -y
    sudo apt-get install php-xdebug -y

    sudo a2dismod php7.0
    sudo a2enmod php7.2
    sudo phpenmod mysqli
    sudo service apache2 restart

    mysql -u root -ppassword < /var/www/html/install/install.sql

    openssl genrsa -out local.nanocenter.key 2048

    a2enmod ssl
    sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf.bak
    sudo cp /var/www/html/vagrant_vhost.conf /etc/apache2/sites-available/000-default.conf

    sudo cp /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf.bak
    sudo cp /var/www/html/vagrant_vhost_ssl.conf /etc/apache2/sites-available/default-ssl.conf

    sudo cp /etc/php/7.1/mods-available/xdebug.ini /etc/php/7.1/mods-available/xdebug.ini.bak
    sudo cp /var/www/html/xdebug.ini /etc/php/7.1/mods-available/xdebug.ini

    a2ensite default-ssl
    apache2ctl restart

  SHELL
end
