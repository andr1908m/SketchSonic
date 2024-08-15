#!/bin/sh
apt update
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php
apt install -y composer
composer install