<?php
require './firebase/firebaseLib.php';

use firebase\auth\tokenGenerator;

const DEFAULT_URL = 'https://firebaseio.com/';
const DEFAULT_TOKEN = 'Toekn';					   

send_push();

function get_token($user_id){

	$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
	try{
		$token = $firebase->get('/tokens/'.$user_id.'/token');
	}catch(Exception $ex){
		echo $ex.getMessage();
	}
	return $token;
}

function send_push(){
	define( 'API_ACCESS_KEY', 'AccessKey' );
	$token = get_token($_REQUEST['user_id']);
	$token = substr($token,1,strlen($token) - 2);
	$registrationIds = [$token];
	$msg = array
	(
		'body' 	=> $_REQUEST['body'],
		'title'		=> $_REQUEST['title'],
		'vibrate'	=> 1,
		'sound'		=> 1,
	);
	$fields = array
	(
		'registration_ids' => $registrationIds,
		'notification' => $msg
	);
	 
	$headers = array
	(
		'Authorization: key=' . API_ACCESS_KEY,
		'Content-Type: application/json'
	);
	 
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch );
	curl_close( $ch );
	print_r($result);
}
