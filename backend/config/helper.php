<?php
require_once('conn.php');

function generateResponseBody($code, $data = [], $message) {
    $statusType = 'Error';
    if ($code == 200) {
        $statusType = 'Success';
    }
    $result['status']['code']       = $code;
    $result['status']['type']       = $statusType;
    $result['status']['message']    = $message;
    $result['dataset']              = $data;

    $response['response']           = $result;

    return json_encode($response);
}

function getAuthenticatedUser($headerToken){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $isLoggedIn = 0;

    if ( isset($_SESSION['_token'], $_SESSION['_token_expire']) ) {
        $token          = $_SESSION['_token'];
        $tokenExpire    = $_SESSION['_token_expire'];

		if ( time() >= $tokenExpire ) {
			unset($_SESSION['_token'], $_SESSION['_token_expire'], $_SESSION['_token_expire_date_time']);
		} else {
            
           if (!hash_equals($token, $headerToken)) {
				logWrite('auth', 'Invalid token: header=' . $headerToken . ' session=' . $token);
			} else {
                $isLoggedIn = 1;

                // if ($userId != null) {
                //     $getUserDetails = $conn->prepare("
                //             SELECT * FROM ".TABLE_PREFIX."users
                //             WHERE
                //             id = ? AND usertype = ?");
                //     $getUserDetails->bind_param('is', $userId, $userType);

                //     $getUserDetails->execute();
                //     $result = $getUserDetails->get_result();

                //     if ($result && $result->num_rows > 0) {
                //         return 1;
                //     } else {
                //         return 0;
                //     }
                // }
            }
		}
	} else {
		logWrite('auth', 'No token found.');
	}

    return $isLoggedIn;
}

function logWrite($pageName = 'index', $logMessage){
    $logFolder = "logs";
    if (!file_exists($logFolder)) {
        mkdir($logFolder, 0777, true);
    }

    $logFileData = $logFolder . '/logs_' . $pageName . '_' . date('Y-m-d') . '.log';

    $logEntry = '[' . date('Y-m-d H:i:s') . '] ' . print_r($logMessage, true) . "\n";

    file_put_contents($logFileData, $logEntry, FILE_APPEND);
}