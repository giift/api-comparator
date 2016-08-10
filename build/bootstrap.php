<?php
// bloody facebook requires session to be started
session_start();
date_default_timezone_set('Europe/London');

// Load in the Autoloader
require SRCPATH.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';
// class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

// Bootstrap the framework DO NOT edit this
require SRCPATH.'bootstrap.php';


// Autoloader::add_classes(array(
// 	// Add classes you want to override here
// 	// Example: 'View' => APPPATH.'classes/view.php',
// 	'XMLTransactionHandler'=>APPPATH.'classes/xmltransactionhandler.php',
// ));

// // Register the autoloader
// Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */
Fuel::$env = (isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : Fuel::DEVELOPMENT);

// Initialize the framework with the config file.
Fuel::init('config.php');
