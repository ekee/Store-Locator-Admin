<?php

error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

//Check for a config file
if( !@include( 'system/config/config.php' ) ){
	header("HTTP/1.1 500 Internal Server Error");
	echo "<p><strong>No config file found.</strong> Rename system/config/config_sample.php to config.php and edit it to reflect your needs.</p>";
	exit;
}

// Only need these if this isn't an AJAX request
if ( !REQUEST_IS_AJAX ) {
	require( DIR_LIB . '/FormStatusMessage/FormStatusMessage.php' );
	require( DIR_HELPERS . '/view_helpers.php' );
	$status_message = new FormStatusMessage;
}

// Require necessary files
require( DIR_CORE . '/Router.php' );
require( DIR_CONFIG . '/routes.php' );
require( DIR_CORE . '/Request.php' );
require( DIR_CORE . '/Response.php' );

if ( $vars['request'] = Router::route( REQUEST ) ) {

	// Connect to the database
	require( DIR_CORE . '/Db.php' );
	if ( !$db = Db::connect( $config['db_user'], $config['db_password'], $config['db_name'], $config['db_host'], $config['db_type'] ) ) {
		header("HTTP/1.1 500 Internal Server Error");
		$status_message->setStatuses( array( 'error', 'block-message', 'remain' ) );
		$status_message->setMessage( "<p><strong>Unable to connect to the database</strong>.Please check your config and try again.</p>" );
		require( DIR_VIEWS . '/pages/error.php' );
		exit;
	}

	// We will use a store table gateway on every page so we will create one here
	require( DIR_MODELS . '/StoreTableGateway.php' );
	require( DIR_MODELS . '/Store.php' );
	$stg = new StoreTableGateway( $db, $config['db_table'], $config['column_map'] );

	if ( !$stg->validateTable() ) {
		header("HTTP/1.1 500 Internal Server Error");
		$status_message->setStatuses( array('error', 'block-message', 'remain' ) );
		$status_message->setMessage( "<p><strong>Invalid table setup</strong>. Please check your config and try again.</p>" );
		require( DIR_VIEWS . '/pages/error.php' );
		exit;
	}

	// Set variables
	$vars['controller'] = $vars['request']->controller;
	$vars['column_info'] = $stg->getColumns();
	$vars['columns'] = array_keys( $vars['column_info'] );
	$vars['columns_list'] = array_values( array_diff( $vars['columns'], array( $config['column_map']['id'], $config['column_map']['lat'], $config['column_map']['lng'] ) ) );
	$vars['columns_edit'] = array_values( array_diff( $vars['columns'], array( $config['column_map']['id'] ) ) );

	if ( isset( $_GET['status'], $_GET['message'] ) ) {
		$status_message->setStatus( $_GET['status'] );
		$status_message->setMessage($_GET['message'] );
	}

	// Require the controller and exit
	require( DIR_CONTROLLERS . '/' . $vars['controller'] . '.php' );
	exit;
}

// No route found, send 404
header("HTTP/1.1 404 Not Found");
$status_message->setStatuses( array( 'error', 'block-message', 'remain' ) );
$status_message->setMessage( "<p><strong>Page not found</strong></p>" );
require( DIR_VIEWS . '/pages/error.php' );
exit;

