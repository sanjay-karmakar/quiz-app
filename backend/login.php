<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:4200");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

require_once('config/conn.php');
require_once('config/helper.php');

try {
    $dataset = [];

    $postData = file_get_contents("php://input");
    $request = json_decode($postData, true);

    if (!$request || !isset($request['username'])) {
        throw new Exception('Username required');
    }

    $userName = $request['username'];

    $getUser = $conn->prepare("SELECT id, username, usertype FROM ".TABLE_PREFIX."users WHERE username = ?");
    $getUser->bind_param('s', $userName);
    $getUser->execute();
    $result = $getUser->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $currentTimestamp = time();
        $futureTimestamp  = $currentTimestamp + 3600; // 1 hour

        $formattedLoggedInDateTime = date('Y-m-d H:i:s', $currentTimestamp);
        $formattedDateTime         = date('Y-m-d H:i:s', $futureTimestamp);

        $_SESSION['_token']         = bin2hex(random_bytes(32)); // 32 bytes = stronger
        $_SESSION['_token_expire']  = $futureTimestamp;
        
        $_SESSION['_user_id']       = $row['id'];
        $_SESSION['_username']      = $row['username'];
        $_SESSION['_usertype']      = $row['usertype'];        

        $dataset[] = [
            'username'      => $row['username'],
            'usertype'      => $row['usertype'] === 'SA' ? 'Super Admin' : 'User',
            '_auth_token'   => $_SESSION['_token'],
        ];
        
        echo generateResponseBody(200, $dataset, 'Users found.');
    } else {
        echo generateResponseBody(400, $result, 'No user found.');
    }
    
} catch(Exception $e) {
    logWrite('users', $e->getMessage());

    echo generateResponseBody(400, [], $e->getMessage());
}