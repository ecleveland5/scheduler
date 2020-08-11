# -*- mode: ruby -*-
# vi: set ft=ruby :

BOX_IP = "192.168.10.12"
DB_NAME = "nanocenter"

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/bionic64"
  config.vm.box_check_update = true
  config.vm.network "private_network", ip: BOX_IP
  config.vm.synced_folder "./", "/var/www/html/"
  config.vm.network "forwarded_port",guest:3306, host:3306

  config.vm.provision "shell", inline: <<-SHELL

    sudo apt-get update -y

    sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password password'
    sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password password'
    sudo apt-get install -y mysql-server
    mysql -u root -ppassword < /var/www/html/install/install.sql

    sudo apt-get install -y apache2
    sudo apt-get install -y libapache2-mod-php
    sudo apt-get install -y php-mysql
    sudo apt-get install -y php-curl
    sudo apt-get install -y php-xml
    sudo apt-get install -y php-xdebug

    sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /var/www/html/ssl/nanocenter.key -out /var/www/html/ssl/nanocenter.crt
    sudo openssl dhparam -out /etc/ssl/certs/nanocenter.pem 2048

    sudo a2enmod ssl
    sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf.bak
    sudo cp /var/www/html/dev_env/vagrant_vhost.conf /etc/apache2/sites-available/000-default.conf

    sudo cp /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf.bak
    sudo cp /var/www/html/dev_env/vagrant_vhost_ssl.conf /etc/apache2/sites-available/default-ssl.conf

    sudo cp /etc/php/7.2/mods-available/xdebug.ini /etc/php/7.2/mods-available/xdebug.ini.bak
    sudo cp /var/www/html/dev_env/xdebug.ini /etc/php/7.2/mods-available/xdebug.ini

    sudo systemctl is-enabled apache2
    sudo a2ensite default-ssl
    sudo service apache2 restart

  SHELL
end
