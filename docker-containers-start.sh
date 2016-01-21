#!/bin/bash
docker start \
    rksd-proxy-php-apache \
    rksd-proxy-mariadb \
    rksd-origin-php-apache \
    rksd-origin-mariadb
