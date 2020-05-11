ARG PHP_IMAGE="wodby/drupal-php:7.2-dev"

FROM ${PHP_IMAGE}

USER root

RUN set -xe; \
  # Composer version is locked because certain versions have worse performance than others.
  # Currently known good versions: 1.7.3, 1.10.5
  # https://github.com/composer/composer/issues/7051
  wget -qO- https://getcomposer.org/installer | php -- --version=1.10.5 --install-dir=/usr/local/bin --filename=composer

USER wodby
