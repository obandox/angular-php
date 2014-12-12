<?php
require 'vendor/autoload.php';

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessphpFilter;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\JSMinFilter;


$am = new AssetManager();
$js = new JSMinFilter();




$am->set('vendorjs', new AssetCollection(array(
    new FileAsset(__DIR__.'/app/vendor/angularjs/angular.js'),
    new FileAsset(__DIR__.'/app/vendor/jquery/jquery.js'),
), array($js)));


$am->set('appjs', new AssetCollection(array(
    new FileAsset(__DIR__.'/app/app.js'),
    new GlobAsset(__DIR__.'/app/config/*'),
    new GlobAsset(__DIR__.'/app/services/*'),
    new GlobAsset(__DIR__.'/app/directives/*'),
    new GlobAsset(__DIR__.'/app/controllers/*'),
), array($js)));




$cache = new FilesystemCache(__DIR__ . '/dist/cache');
foreach ($am->getNames() as $name) {
    $filename = $name;
    if (preg_match('/^(.+)(js|css)$/', $name, $matches)) {
        $filename = $matches[1] . '.' . $matches[2];
    }
    $asset = $am->get($name);
    $asset = new AssetCache($asset, $cache);
    $asset->setTargetPath($filename);
    $am->set($name, $asset);
}
$writer = new AssetWriter(__DIR__ . '/dist');
$writer->writeManagerAssets($am);
