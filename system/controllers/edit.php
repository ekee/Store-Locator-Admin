<?php

if ( isset( $_POST['geocode'] ) ) {
	$req = Request::factory( URL_ROOT_ABSOLUTE . '/api/geocode?' . http_build_query( $_POST ) );
	$resp = $req->execute();
	if ( $resp->status == 200 ) {
		$req2 = Request::factory( URL_ROOT_ABSOLUTE . '/api/edit/' . $_POST[$config['column_map']['id']] );
		$req2->post = array(
			$config['column_map']['id'] => $registry->request->params->location_id,
			$config['column_map']['lat'] => $resp->data->lat,
			$config['column_map']['lng'] => $resp->data->lng
		);
		$req2->method = 'post';
		$resp2 = $req2->execute();
		if ( $resp2->status == 200 ) {
			$status_message->setStatus( 'success' );
			$status_message->setMessage( sprintf( '<p>%s</p>', $resp2->data->message ) );
		}
		else {
			header("HTTP/1.1 500 Internal Server Error");
			$status_message->setStatuses( array( 'error', 'remain' ) );
			$status_message->setMessage( sprintf( '<p>%s</p>', $resp2->data->message ) );
		}
	}
	else {
		$status_message->setStatus( 'error' );
		$status_message->setMessage( sprintf( '<p>%s</p>', $resp->data->message ) );
	}
}

if ( isset( $_POST['save'] ) ) {
	$req = Request::factory( URL_ROOT_ABSOLUTE . '/api/edit/' . $_POST[$config['column_map']['id']] );
	$req->post = array_intersect_key( $_POST, array_flip( $registry->columns ) );
	$req->method = 'post';
	$resp = $req->execute();
	if ( $resp->status == 200 ) {
		$status_message->setStatus( 'success' );
		$status_message->setMessage( sprintf( '<p>%s</p>', $resp->data->message ) );
	}
	else {
		header("HTTP/1.1 500 Internal Server Error");
		$status_message->setStatuses( array( 'error', 'remain' ) );
		$status_message->setMessage( sprintf( '<p>%s</p>', $resp->data->message ) );
	}
}

$location = $stg->getLocation( $registry->request->getParam( 'location_id' ) );

if ( !$location ) {
	header("HTTP/1.1 404 Not Found");
	$status_message->setStatuses( array( 'error', 'block-message', 'remain' ) );
	$status_message->setMessage( "<p><strong>This locationdoes not exist</strong></p>" );
	require( DIR_VIEWS . '/pages/error.php' );
	exit;
}
require( DIR_VIEWS . '/pages/edit.php' );

?>