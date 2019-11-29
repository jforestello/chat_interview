<?php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use Illuminate\Container\Container;

$pool = new FilesystemAdapter('', 0, DOCUMENT_ROOT."/bootstrap/cache");

/* @var CacheItem $cachedItem */
$cachedItem = $pool->getItem('app.container');
if (! $cachedItem->isHit()) {
    $cachedItem->set(new Container);
    $pool->save($cachedItem);
}
$container = $cachedItem->get();

return [$container];