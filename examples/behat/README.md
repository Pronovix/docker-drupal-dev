# Behat integration example

`docker-compose.yml` contains some sane default Behat settings which makes it possibly to easily run Behat tests
inside the PHP container. Check `BEHAT_PARAMS` environment variable in the definition of the `php` container.

## Usage

1. Copy `behat.yml.dist` to one folder above then `build`.
2. Copy `login.feature` to `build/behat`.
3. Run `docker-compose exec php ./vendor/bin/behat --config=../behat.yml.dist`

Note: the site must be in installed state for a successful test run.
