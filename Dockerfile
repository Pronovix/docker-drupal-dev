ARG PHP_IMAGE="wodby/drupal-php:8.1-dev"

FROM ${PHP_IMAGE}

USER root

RUN set -xe; \
  # Wodby releases a new PHP image every time there is a new version of PHP. But, a new version of Composer might be
  # released between two PHP version releases. That means that we have to wait for a new PHP version for Wodby to create
  # a new PHP image, that will contain the new Composer. By running `composer self-update` here, the fresh PHP container
  # will always have the latest Composer version.
  composer self-update

USER wodby
