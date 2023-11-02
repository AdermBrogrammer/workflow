<?php
	session_start();
	require 'Connection.php';
	
    $Amount = $_GET["Amount"];
	$CustomerEmail = $_SESSION["CustomerEmail"];

	$ProductID = $_GET['ProductID'];
	$CustomerID = $_SESSION["CustomerID"];
	$ProductSize = $_GET["Size"];
	$ProductColor = $_GET["Color"];
	
	$email = $CustomerEmail;
	$amount = $Amount * 100; // Convert amount to Kobo

	$curl = curl_init();

	// url to go to after payment
	$callback_url = 'http://localhost/madasng/OrderAction.php?ProductID='.$ProductID.'&CustomerID='.$CustomerID.'&ProductSize='.$ProductSize.'&ProductColor='.$ProductColor.'';

	curl_setopt_array($curl, array(
	CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => json_encode([
		'amount'=>$amount,
		'email'=>$email,
		'callback_url' => $callback_url
	]),
	CURLOPT_HTTPHEADER => [
		"authorization: Bearer sk_test_76d513eaaaa16a63954b76b9b972b4681aacd0cd", //replace this with your own test key
		"content-type: application/json",
		"cache-control: no-cache"
	],
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	if($err){
	// there was an error contacting the Paystack API
	die('Curl returned error: ' . $err);
	}

	$tranx = json_decode($response, true);

	if(!$tranx['status']){
	// there was an error from the API
	print_r('API returned error: ' . $tranx['message']);
	}

	// comment out this line if you want to redirect the user to the payment page
	print_r($tranx);
	// redirect to page so User can pay
	// uncomment this line to allow the user redirect to the payment page
	header('Location: ' . $tranx['data']['authorization_url']);

	?>