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
	'game\play\UltimatPassword' => APPPATH.'game/play/ultimatPassword.php',
    'game\ws\WebSocket' => APPPATH.'game/ws/webSocket.php',
    'game\ws\Pusher' => APPPATH.'game/ws/pusher.php',
    'game\ws\WampServerSocket' => APPPATH.'game/ws/wampServerSocket.php',
    'game\ws\WsPublish' => APPPATH.'game/ws/wsPublish.php',
    'game\play\GamesProcess' => APPPATH.'game/play/gamesProcess.php',
    'game\play\GamePusher' => APPPATH.'game/play/gamePusher.php',
    'game\ws\WsController' => APPPATH.'game/ws/wsController.php',
	
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
