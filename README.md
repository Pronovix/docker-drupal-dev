# Drupal development & testing environment

Docker based Drupal environment for local development and testing. 

Supported testing frameworks: PHPUnit, Behat

It is recommended to use in combination with the `pronovix/drupal-qa` Composer plugin.

**Notice: Always pull a specific tag or commit from this repository! There could be breaking changes!**

## Requirements

- [Docker >= 18.03](https://www.docker.com/get-docker)
- [Docker Compose](https://docs.docker.com/compose/)

## Usage

```sh
$ git clone https:/github.com/Pronovix/drupal-dev.git
$ mkdir build;
$ ln -s drupal-dev/docker-compose.yml
$ ln -s Dockerfile
$ docker-compose up -d --build
```

## Installing Drupal inside the running PHP container

```sh
$ docker-compose exec php composer create-project drupal-composer/drupal-project:8.x-dev ../build -n
```

Now continue with an optional, but highly recommended step. Symlink `settings*.php` files and `development.services.yml.dist`
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

If you successfully symlinked these files your environment is ready to be used. You can install the site on the UI or with Drush.

You can install the site with the minimal install profile like this with Drush: `docker-compose exec php drush si minimal -y`.
If the `build/config` folder does not exist you have to add `--db-url=$SIMPLETEST_DB` to the end of the command because
Drush is not going to read the configuration from the symlinked settings.php. (This is probably a bug.)

## Accessing to the dev environment inside a browser

Check the _current_ port of the running webserver container with `docker-compose ps webserver`:

```sh
              Name                            Command               State           Ports        
-------------------------------------------------------------------------------------------------
my_module_webserver   /docker-entrypoint.sh sudo ...   Up      0.0.0.0:32794->80/tcp
```

**Note: The exposed port changes every time when the `webserver` container restarts.**

If you generate a login URL with Drush then replace the `webserver` in the login url with `localhost:[PORT]`, like: http:/localhost:32794/user/reset/1/1561455114/sKYjEf26WZ6bzuh-KrNY425_3KCppiCHI8SxKZ158Lw/login.
