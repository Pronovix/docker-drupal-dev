# Drupal development & testing environment

> **Warning! Inform the pods and the infra team about the changes you're about to make in this repository. Every change affects downstream projects, and our builds depend on it.**

Docker based Drupal environment for local development and testing.

Supported testing frameworks: PHPUnit, Behat

It is recommended to use in combination with the `pronovix/drupal-qa` Composer plugin.

**Notice: Always pull a specific tag or commit from this repository! There could be breaking changes!**

## Requirements

- [Docker >= 18.03](https://www.docker.com/get-docker)
- [Docker Compose](https://docs.docker.com/compose/)

## Usage

```sh
$ git clone https://github.com/Pronovix/docker-drupal-dev.git drupal-dev
$ mkdir build;
$ ln -s drupal-dev/docker-compose.yml
$ ln -s drupal-dev/Dockerfile
$ printf "COMPOSE_PROJECT_NAME=[YOUR_PROJECT_NAME]\n#You can find examples for available customization in the drupal-dev/examples/.env file.\n" > .env && source .env
$ docker-compose up -d --build
```

**Replace [YOUR_PROJECT_NAME] with a string that only contains lowercase letters and dashes. It must not contain spaces
or any special characters. Ex.: my_awesome_project**

## Installing Drupal 8 inside the running PHP container

```sh
$ docker-compose exec php composer create-project drupal-composer/drupal-project:8.x-dev ../build -n
```

Now continue with an _optional_, but highly recommended step. Symlink `settings*.php` files and `development.services.yml.dist`
from the `drupal` folder to `build/web/sites`. Symlink `settings*.php` files and `development.services.yml.dist` from
the `drupal` folder to `build/web/sites`. **Symlinks must be relative to the destination path in a way that they could
be accessed inside the `php` container as well.**

```sh
$ ln -s ../../../../drupal-dev/drupal/settings.php build/web/sites/default/settings.php
$ ln -s ../../../../drupal-dev/drupal/settings.shared.php build/web/sites/default/settings.shared.php
$ ln -s ../../../../drupal-dev/drupal/settings.testing.php build/web/sites/default/settings.testing.php
$ ln -s ../../../drupal-dev/drupal/development.services.yml.dist build/web/sites/development.services.yml.dist
```

(If `settings.php` file already exists then delete it or rename it to `settings.local.php`.)

If you successfully symlinked these files then your environment is ready to be used. You can install the site on the UI
or with Drush.

You can install the site with the minimal install profile like this with Drush: `docker-compose exec php drush si minimal -y`.
If the `build/config` folder does not exist you have to add `--db-url=$SIMPLETEST_DB` to the end of the command because
Drush is not going to read the configuration from the symlinked settings.php. (This is probably a bug.)

## Accessing to the dev environment inside a browser

Check the _current_ port of the running webserver container with `docker-compose ps webserver`:

```sh
              Name                            Command               State           Ports
--------------------------------------------------------------------------------------------------
       my_module_webserver           /docker-entrypoint.sh sudo ...   Up      0.0.0.0:32794->80/tcp
```

**Note: The exposed port changes every time when the `webserver` container restarts.**

If you generate a login URL with Drush then replace the `webserver` in the login url with `localhost:[PORT]`, like: http:/localhost:32794/user/reset/1/1561455114/sKYjEf26WZ6bzuh-KrNY425_3KCppiCHI8SxKZ158Lw/login.

## Running tests

### PHPUnit

In this configuration everything is preconfigured for running any Drupal 8's PHPUnit tests, included but not limited to
PHPUnit Javascript tests. If you have PHPUnit installed as a Composer dependency in your project, then running PHPUnit
tests is simple, here is a few examples:

```sh
$ docker-compose run --rm php ./vendor/bin/phpunit -c web/core -v --debug --printer '\Drupal\Tests\Listeners\HtmlOutputPrinter' web/core/modules/node # Run all tests of the node module.
$ docker-compose run --rm php ./vendor/bin/phpunit -c web/core -v --debug --printer 'Drupal\Tests\Listeners\HtmlOutputPrinter' web/core/modules/node/tests/src/Functional/NodeCreationTest.php # Run one specific test from the node module.
$ docker-compose run --rm php ./vendor/bin/phpunit -c web/core -v --debug --printer '\Drupal\Tests\Listeners\HtmlOutputPrinter' --testsuite kernel # Run all kernel tests.
```

You can find more information about available PHPUnit CLI parameters and configuration options in the official [PHPUnit
documentation](https://phpunit.de/manual/6.5/en).

### Behat

`docker-compose.yml` contains some sane default Behat settings that make it possible to run Behat tests
inside the PHP container smoothly. Check `BEHAT_PARAMS` environment variable in the definition of the `php` container.

## Usage

1. Copy the `behat.yml.dist` file to the `build` folder with `cp drupal-dev/examples/behat/behat.yml.dist build/behat.yml.dist`.
You can also add your `behat.yml` with overrides to this directory if you have one.
2. For demonstration purposes copy the `drupal-dev/examples/behat/login.feature` to `build/behat` with `mkdir build/behat && cp drupal-dev/examples/behat/login.feature build/behat`.
3. Make sure the site is in an installed state. You can ensure that by importing a database or installing a site
manually of with Config Installer.
4. Run all Behat tests from the `build/behat` folder: `docker-compose exec php php vendor/bin/behat`.

You can find more information about available Behat CLI parameters and configuration options in the official [Behat](https://docs.behat.org)
and [Drupal Behat extension](https://behat-drupal-extension.readthedocs.io/) documentation.

### Notes

It is recommended to install the `pronovix/drupal-qa` package with Composer because it ships a bunch of useful Behat
extensions that could make your life easier when you are testing a site.

## Checking outgoing emails

MailHog captures all outgoing emails from this development environment. (Unless you override the default mail system
configuration in Drupal.)

If you would like to review all sent emails then check the local port of the `mailhog` container with `docker-compose ps mailhog`:

```sh
             Name             Command   State                 Ports
----------------------------------------------------------------------------------
      my_module_mailhog       MailHog     Up      1025/tcp, 0.0.0.0:32772->8025/tcp
```

and open the MailHog admin UI in your browser, e.g., `http://localhost:32772`.
