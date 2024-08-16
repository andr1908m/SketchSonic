FROM debian:latest
ENV USERNAME=example
WORKDIR /var/www/html
COPY . .
RUN /var/www/html/ci.sh
USER $USERNAME
RUN composer install
RUN composer analyse
RUN composer coverage

# EXPOSE 80

# CMD ["php", "-S", "0.0.0.0:80"]