<?php

use WHMCS\Database\Capsule;

/**
 * cPanel AutoSSL Module for WHMCS
 *
 * Create an SSL Certificate addon that utilizes cPanel's AutoSSL feature for free SSL Certificates. 
 *
 * @package    WHMCS
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT
 * @version    0.0.1
 * @link       https://leemahoney.dev
 */

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}


/**
 * autossl_MetaData
 *
 * @return void
 */
function autossl_MetaData() {

    return [
        'DisplayName'       => 'cPanel AutoSSL',
        'APIVersion'        => '1.1',
        'RequiresServer'    => false,
    ];

}


/**
 * autossl_ConfigOptions
 *
 * @return void
 */
function autossl_ConfigOptions() {

}


/**
 * autossl_CreateAccount
 *
 * @param  mixed $params
 * @return void
 */
function autossl_CreateAccount(array $params) {

    try {
    
        # Get the server that the parent hosting account is on
        $serverID   = $params['service']['server'];
        $server     = Capsule::table('tblservers')->where('id', $serverID)->first();

        # Define a couple of details related to the server
        $serverHostname     = $server->hostname;
        $serverUsername     = $server->username;
        $serverAccessHash   = $server->accesshash;

        # Make two calls to the servers API, one to override the users featurelist to enable AutoSSL and then another to run an AutoSSL check straight after.
        cPanelCall("add_override_features_for_user?api.version=1&user={$params['username']}&features=%7B%22autossl%22%3A%221%22%7D", $serverHostname, $serverUsername, $serverAccessHash);
        cPanelCall("start_autossl_check_for_one_user?api.version=1&username={$params['username']}", $serverHostname, $serverUsername, $serverAccessHash);

    } catch (Exception $e) {

        # Record the error in WHMCS's module log.
        logModuleCall(
            'autossl',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();

    }

    return 'success';
    
}


/**
 * autossl_SuspendAccount
 *
 * @param  mixed $params
 * @return void
 */
function autossl_SuspendAccount(array $params) {

    try {

        # Get the server that the parent hosting account is on
        $serverID   = $params['service']['server'];
        $server     = Capsule::table('tblservers')->where('id', $serverID)->first();

        # Define a couple of details related to the server
        $serverHostname     = $server->hostname;
        $serverUsername     = $server->username;
        $serverAccessHash   = $server->accesshash;

        cPanelCall("remove_override_features_for_user?api.version=1&user={$params['username']}&features=%5B%22autossl%22%5D", $serverHostname, $serverUsername, $serverAccessHash);

    } catch (Exception $e) {
        
        # Record the error in WHMCS's module log.
        logModuleCall(
            'autossl',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();

    }

    return 'success';

}


/**
 * autossl_CheckStatus
 *
 * @param  mixed $params
 * @return void
 */
function autossl_CheckStatus($params) {

    try {

        # Get the server that the parent hosting account is on
        $serverID   = $params['service']['server'];
        $server     = Capsule::table('tblservers')->where('id', $serverID)->first();

        # Define a couple of details related to the server
        $serverHostname     = $server->hostname;
        $serverUsername     = $server->username;
        $serverAccessHash   = $server->accesshash;

        $call = cPanelCall("verify_user_has_feature?api.version=1&user={$params['username']}&feature=autossl", $serverHostname, $serverUsername, $serverAccessHash);
        
        $check = json_decode($call, true);

        if ($check['data']['has_feature']) {
            return [
                "success" => "AutoSSL is enabled for {$params['username']}",
            ];
        } else {
            return "AutoSSL is not enabled for cPanel User {$params['username']}";
        }

    } catch (Exception $e) {
        
        # Record the error in WHMCS's module log.
        logModuleCall(
            'autossl',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();

    }

    return 'success';

}


/**
 * autossl_TerminateAccount
 *
 * @return void
 */
function autossl_TerminateAccount() {

    try {

        # Get the server that the parent hosting account is on
        $serverID   = $params['service']['server'];
        $server     = Capsule::table('tblservers')->where('id', $serverID)->first();

        # Define a couple of details related to the server
        $serverHostname     = $server->hostname;
        $serverUsername     = $server->username;
        $serverAccessHash   = $server->accesshash;

        cPanelCall("remove_override_features_for_user?api.version=1&user={$params['username']}&features=%5B%22autossl%22%5D", $serverHostname, $serverUsername, $serverAccessHash);

    } catch (Exception $e) {
        
        # Record the error in WHMCS's module log.
        logModuleCall(
            'autossl',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();

    }

    return 'success';

}


/**
 * autossl_Renew
 *
 * @return void
 */
function autossl_Renew() {

    try {
    
        # Get the server that the parent hosting account is on
        $serverID   = $params['service']['server'];
        $server     = Capsule::table('tblservers')->where('id', $serverID)->first();

        # Define a couple of details related to the server
        $serverHostname     = $server->hostname;
        $serverUsername     = $server->username;
        $serverAccessHash   = $server->accesshash;

        cPanelCall("start_autossl_check_for_one_user?api.version=1&username={$params['username']}", $serverHostname, $serverUsername, $serverAccessHash);

    } catch (Exception $e) {

        # Record the error in WHMCS's module log.
        logModuleCall(
            'autossl',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();

    }

    return 'success';

}

/**
 * autossl_AdminCustomButtonArray
 *
 * @return void
 */
function autossl_AdminCustomButtonArray() {
    $buttonarray = array(
	 'Check AutoSSL Status' => 'CheckStatus',
	);
	return $buttonarray;
}



/**
 * cPanelCall
 *
 * @param  mixed $query
 * @param  mixed $serverHostname
 * @param  mixed $serverUsername
 * @param  mixed $serverAccessHash
 * @return void
 */
function cPanelCall($query, $serverHostname, $serverUsername, $serverAccessHash) {
    
    $call = "https://{$serverHostname}:2087/json-api/" . $query;

    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

    $header[0] = "Authorization: whm $serverUsername:$serverAccessHash";

    curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
    curl_setopt($curl, CURLOPT_URL, $call);

    $result = curl_exec($curl);

    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if ($http_status != 200) {
        
        return "Error: Status code " . $http_status . " returned\n";
    
    } else {
    
        return $result;
    
    }

    curl_close($curl);

}