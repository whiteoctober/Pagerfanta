<?php

require_once(__DIR__.'/../vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php');
require_once(__DIR__.'/../vendor/propel/runtime/lib/Propel.php');

use Symfony\Component\ClassLoader\UniversalClassLoader;

$classLoader = new UniversalClassLoader();
$classLoader->registerNamespaces(array(
    'Pagerfanta'           => __DIR__.'/../src',
    'Mandango'             => __DIR__.'/../vendor/mandango/src',
    'Doctrine\Common'      => __DIR__.'/../vendor/doctrine-common/lib',
    'Doctrine\MongoDB'     => __DIR__.'/../vendor/doctrine-mongodb/lib',
    'Doctrine\ODM\MongoDB' => __DIR__.'/../vendor/doctrine-mongodb-odm/lib',
));
$classLoader->register();
