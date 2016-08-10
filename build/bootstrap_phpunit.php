<?php

/**
 * Set error reporting and display errors settings.  You will want to change these when in production.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$src_path = rtrim($_SERVER['src_path'], '/').'/';

/**
 * Website docroot
 */
define('DOCROOT', realpath(__DIR__.DIRECTORY_SEPARATOR.$_SERVER['doc_root']).DIRECTORY_SEPARATOR);

( ! is_dir($src_path) and is_dir(DOCROOT.$src_path)) and $src_path = DOCROOT.$src_path;

define('SRCPATH', realpath($src_path).DIRECTORY_SEPARATOR);

unset($src_path, $_SERVER['src_path']);

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

// Boot the app
require_once SRCPATH.'bootstrap.php';

// Set test mode
Fuel::$is_test = true;

// Import the TestCase class
import('testcase');
