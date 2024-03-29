<?php

/**
 * @file
 * Development environment settings.
 */

include "{$app_root}/{$site_path}/settings.shared.php";

use Drupal\Component\Utility\Crypt;

$db_driver = getenv('DB_DRIVER');
$databases['default']['default'] = [
  'database' => getenv('DB_NAME'),
  'username' => getenv('DB_USER'),
  'password' => getenv('DB_PASSWORD'),
  'host' => getenv('DB_HOST'),
  'driver' => $db_driver,
];

$settings['trusted_host_patterns'] = ['^webserver$', '^localhost$'];
$settings['file_private_path'] = '/mnt/files/private';

// Ideal for development and testing.
$settings['extension_discovery_scan_tests'] = true;

// If config sync directory is set already, use that, otherwise fallback to
// the default config/sync folder.
if (array_key_exists('config_sync_directory', $settings)) {
  $config_folder = dirname($settings['config_sync_directory']);
}
else {
  $config_folder = "{$app_root}/../config";
  $settings['config_sync_directory'] = $config_folder . '/sync';
}
// Config sync and hash salt must be set otherwise Drupal adds the database
// settings again to this file.
$hash_salt_file = $config_folder . '/hash_salt.txt';
if (!file_exists($hash_salt_file)) {
  if (!file_exists(basename($hash_salt_file))) {
    if (!mkdir($config_folder, 0777, TRUE) && !is_dir($config_folder)) {
      throw new \RuntimeException(sprintf('Directory "%s" was not created', $config_folder));
    }
  }
  if (file_put_contents($hash_salt_file, Crypt::randomBytesBase64(55)) === FALSE) {
    throw new \RuntimeException(sprintf('File with hash_salt could not be saved to %s.', $hash_salt_file));
  }
}
// Hash salt and config
$settings['hash_salt'] = file_get_contents($hash_salt_file);

// Some sane-defaults for development environment.
$settings['container_yamls'][] = $app_root . '/sites/development.services.yml.dist';

// Include settings.local.php with local overrides if exists.
$filename = "{$app_root}/{$site_path}/settings.local.php";
if ((!isset($drupal_dev_do_not_include_settings_local_php) || !$drupal_dev_do_not_include_settings_local_php) && file_exists($filename) && realpath($filename) !== realpath(__FILE__)) {
  include $filename;
}
