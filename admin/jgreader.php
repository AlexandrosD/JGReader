<?php
/*------------------------------------------------------------------------
# com_jgreader - J! GoogleReader
# ------------------------------------------------------------------------
# @author    Alexandros D
# @copyright Copyright (C) 2011 Alexandros D. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @Website: http://alexd.mplofa.com
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title("Joomla! GReader");

//Check if we're in authorization proccess.
if ( JRequest::getInt("authorization") == 1 ) {
	require_once '../components/com_jgreader/OAuthLib/OAuthSimple.php';
	$oauthObject = new OAuthSimple();
	
	$scope = 'http://www.google.com/reader/api/';
	$callback = JURI::base() . 'index.php?option=com_jgreader&authorization=1';
	
	// Fill in API key/consumer key
	$signatures = array( 'consumer_key'     => 'anonymous',
	                     'shared_secret'    => 'anonymous');
	
	// In step 3, a verifier will be submitted.  If it's not there, we must be
	// just starting out. Let's do step 1 then.
	if (!isset($_GET['oauth_verifier'])) {
		// Step 1: Get a Request Token
		$result = $oauthObject->sign(array(
		'path'      =>'https://www.google.com/accounts/OAuthGetRequestToken',
	        'parameters'=> array(
	            'scope'         => $scope,
	            'oauth_callback'=> $callback),
	        'signatures'=> $signatures));
	
	    // The above object generates a simple URL that includes a signature
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
		$r = curl_exec($ch);
		curl_close($ch);
	
		// We parse the string for the request token and the matching token secret.
		parse_str($r, $returned_items);
		$request_token = $returned_items['oauth_token'];
		$request_token_secret = $returned_items['oauth_token_secret'];
	
		// We will need the request token and secret after the authorization.
		// Google will forward the request token, but not the secret.
		// Set a cookie, so the secret will be available once we return to this page.
		setcookie("oauth_token_secret", $request_token_secret, time()+3600);
		
		// Step 2: Authorize the Request Token
		$result = $oauthObject->sign(array(
		'path'      =>'https://www.google.com/accounts/OAuthAuthorizeToken',
		'parameters'=> array(
		'oauth_token' => $request_token),
		'signatures'=> $signatures));
	
	    // Redirect the user to google in order to perform the authorization
		header("Location:$result[signed_url]");

	}
	else {
		// Step 3: Exchange the Authorized Request Token for a Long-Term Access Token.
	
		// Fetch the cookie and amend our signature array with the request
		// token and secret.
		$signatures['oauth_secret'] = $_COOKIE['oauth_token_secret'];
		$signatures['oauth_token'] = $_GET['oauth_token'];

		// Build the request-URL...
		$result = $oauthObject->sign(array(
		'path'      => 'https://www.google.com/accounts/OAuthGetAccessToken',
		'parameters'=> array(
		'oauth_verifier' => $_GET['oauth_verifier'],
		'oauth_token'    => $_GET['oauth_token']),
		'signatures'=> $signatures));
	
	    // ... and grab the resulting string again. 
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_URL, $result['signed_url']);
		$r = curl_exec($ch);
	
		//Check for long-term token
		parse_str($r, $returned_items);
		$access_token = isset ($returned_items['oauth_token']) ? $returned_items['oauth_token'] : NULL;
		$access_token_secret = isset ($returned_items['oauth_token_secret']) ? $returned_items['oauth_token_secret'] : NULL;
		
		if (!@$access_token) {
			echo "<h2>Authorization Failed.</h2>";
			echo "<h3>You may either selected to deny access to your feeds or a technical error occured.</h3>";
			echo "<p>In case you believe that a technical error is preventing your request to be fullfilled, please contact us providing the following details:</p>";
			echo "<textarea rows='5' cols='50' readonly='1'>" . json_encode($signatures) . "</textarea>";
		}
		else {
			echo "<h2>Authorization Succeed.</h2>";
		
			$signatures['oauth_token'] = $access_token;
			$signatures['oauth_secret'] = $access_token_secret;
	
			//Store the token in databases
			if ( ! storeSignatures($signatures) ) {
				echo "<h3>Error: Unable to store authorization data. Please try again.</h3>";
				echo "<p>In case you believe that a technical error is preventing your request to be fullfilled, please contact us providing the following details:</p>";
				echo "<textarea rows='5' cols='50' readonly='1'>" . json_encode($signatures) . "</textarea>";
			} 
			else {
				echo "<h3>Success! Authorization data stored. You may now use this component.</h3>";
			}		
		}
	}
}

function storeSignatures($signatures) {
	$params = json_encode($signatures);
	
	$db = JFactory::getDbo();
	$query = $db->getQuery(TRUE);
	
	$query->update("#__extensions");
	$query->where("element='com_jgreader'");
	$query->set("params='$params'");
	
	$db->setQuery($query);
	return $db->query();
}


?>
<h3>Instructions</h3>
<p>In order this component to function correctly and fetch your Google Reader reading list items, you shall authorize it by selecting the button below. You will be redirected to Google where you can select to authorize this extension.</p>
<form action="index.php?option=com_jgreader" method="post" name="adminForm" id="adminForm" >
	<input type="hidden" id="authorization" name="authorization" value="1" />
	<button type="submit">Authorize component</button>
</form>
<br />
<p>For maximum security, this extension does not store your username or password, but only a security token provided by Google that can only be used by this website. This is done by using the OAuth standard. You may read more about OAuth here: <a href="http://en.wikipedia.org/wiki/OAuth" target="_blank">Wikipedia OAuth page</a></p>
<br />
