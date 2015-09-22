<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/config.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

// Doctrine ORM configuration

$conn = array(
	'driver'   => 'mysqli',
  'user'     => $config['mysql']['user'],
  'password' => $config['mysql']['pass'],
  'host'     => $config['mysql']['host'],
  'dbname'   => $config['mysql']['database'],
);

$isDevMode = false;
$ormconfig = new Doctrine\ORM\Configuration();

$cache = new Doctrine\Common\Cache\ArrayCache();
$ormconfig->setQueryCacheImpl($cache);
$ormconfig->setProxyDir(__DIR__.'/../src/EntityProxy');
$ormconfig->setProxyNamespace('EntityProxy');
$ormconfig->setAutoGenerateProxyClasses(true);

Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');
$driver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
    new Doctrine\Common\Annotations\AnnotationReader(),
    array(__DIR__.'/../src/Entity')
);
$ormconfig->setMetadataDriverImpl($driver);
$ormconfig->setMetadataCacheImpl($cache);

$entityManager = EntityManager::create($conn, $ormconfig);
