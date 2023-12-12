# Use the official PHP 7.4 Alpine image as the base image
FROM php:7.4-alpine

# Install required extensions
RUN docker-php-ext-install pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Expose port 80 for the PHP built-in server
EXPOSE 80

# Command to run the PHP built-in server
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]
