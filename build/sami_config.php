<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__.'/../vendor/giift')
    ->in(__DIR__.'/../vendor/d2g')
;

return new Sami($iterator, array(
	'title'                => 'API Comparator documentation',
	'build_dir'            => __DIR__.'/docs',
	'cache_dir'            => __DIR__.'/cache',
	'default_opened_level' => 2,
));