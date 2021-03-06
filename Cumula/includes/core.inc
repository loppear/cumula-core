<?php 
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

//TODO: Replace with function to dynamically load everything in the core folder

//Load Constants
require_once dirname(__FILE__) . '/constants.inc';

//Load utility functions
require_once dirname(__FILE__) . '/inflect.php';
require_once dirname(__FILE__) . '/utils.php';

require_once dirname(__FILE__) .'/../Application/Error.php';

register_shutdown_function('handle_shutdown');
set_exception_handler('handle_exception');
set_error_handler('handle_error');

function handle_exception($e) {
	\Cumula\Application\Error::handleException($e);
}

function handle_error($code, $message, $filepath, $line) {
	\Cumula\Application\Error::handleError($code, $message, $filepath, $line);
}

function handle_shutdown() {
	\Cumula\Application\Error::handleShutdown();
}