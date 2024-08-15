FROM ubuntu:latest

WORKDIR /var/www/html

COPY . .

RUN chmod 777 /var/www/html/install.sh
RUN /var/www/html/install.sh
RUN apt install -y php-xml php-xdebug
RUN composer analyse
RUN composer coverage

# EXPOSE 80

# CMD ["php", "-S", "0.0.0.0:80"]