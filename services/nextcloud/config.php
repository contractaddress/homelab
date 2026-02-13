# config for the heavier nextcloud compose
<?php
$CONFIG = array (
  'datadirectory' => '/data',
  'instanceid' => '',
  'memcache.locking' => '\\OC\\Memcache\\Redis',
  'redis' => 
  array (
    'host' => 'nextcloud-redis',
    'port' => 6379,
    'timeout' => 0.0,
  ),
  'passwordsalt' => '',
  'secret' => '',
  'trusted_domains' => 
  array (
    0 => '',
    1 => '',
    2 => '',
  ),
  'dbtype' => 'mysql',
  'version' => '32.0.5.0',
  'overwrite.cli.url' => '',
  'dbname' => 'nextcloud',
  'dbhost' => 'nextcloud-db:3306',
  'dbtableprefix' => 'oc_',
  'mysql.utf8mb4' => true,
  'dbuser' => 'nextcloud',
  'dbpassword' => '',
  'installed' => true,
  'memcache.local' => '\\OC\\Memcache\\APCu',
  'filelocking.enabled' => true,
  'upgrade.disable-web' => true,
);
