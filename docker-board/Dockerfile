# Dockerfile
FROM php:8.0-apache

# MySQLi 확장 설치
# docker-php-ext-install 명령어는 PHP 이미지에서 제공하는 확장 설치 스크립트입니다.
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# 필요한 경우, 다른 확장도 이곳에 추가할 수 있습니다.
# 예: pdo_mysql, gd 등
# RUN docker-php-ext-install pdo_mysql gd
