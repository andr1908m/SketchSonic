#!/bin/sh
apt update
apt install -y apt-transport-https ca-certificates wget lsb-release
echo "deb https://packages.sury.org/php/ $(lsb_release -cs) main" > /etc/apt/sources.list.d/php.list
wget -qO - https://packages.sury.org/php/apt.gpg | apt-key add -
apt update
apt install -y php-xml php-xdebug php-cli unzip
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php'); if (hash_file('sha384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified. '; } else { echo 'Installer corrupt. '; unlink('composer-setup.php'); exit(1); } exec('php composer-setup.php --install-dir=/usr/local/bin --filename=composer'); unlink('composer-setup.php'); echo 'Composer installed successfully.';"