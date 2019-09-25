<?php
// Bootstrap the framework DO NOT edit this
require COREPATH.'bootstrap.php';


Autoloader::add_classes(array(
	// Add classes you want to override here
	// Example: 'View' => APPPATH.'classes/view.php',
	'Controller_Base' => APPPATH. 'classes/controller/base/base.php',
	'Controller_Apibase' => APPPATH. 'classes/controller/base/apibase.php',
	'game\GamePlay' => APPPATH. 'game/gamePlay.php',
	'game\BetImpl' => APPPATH. 'game/betImpl.php',
	'game\play\SDPlay' => APPPATH.'game/play/sDPlay.php',
	'game\play\NumberPlay' => APPPATH.'game/play/numberPlay.php',
	'game\play\Deal' => APPPATH.'game/play/deal.php',
	
));

// Register the autoloader
Autoloader::register();

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
