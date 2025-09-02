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
    $headers = getallheaders();
    
    if (array_key_exists('Authorization', $headers) === false) {
        throw new Exception('Authorization header is missing.');
    }
    
    if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        $incomingToken = $matches[1];
        if ($incomingToken !== $_SESSION['_token']) {
            throw new Exception('Invalid token, please login again.');
        }
    } else {
        throw new Exception('Invalid Authorization format.');
    }
    
    $dataset = [];

    $getAllUsers = $conn->prepare("SELECT username, usertype FROM ".TABLE_PREFIX."users");
    $getAllUsers->execute();
    $result = $getAllUsers->get_result();

    if ($result && $result->num_rows > 0) {        
        while ($row = $result->fetch_assoc()) {
            $dataset[] = [
                'username' => $row['username'],
                'usertype' => $row['usertype'] === 'SA' ? 'Super Admin' : 'User',
            ];
        }
        echo generateResponseBody(200, $dataset, $result->num_rows.' Users found.');
    } else {
        echo generateResponseBody(400, $result, 'No users found.');
    }
} catch(Exception $e) {
    logWrite('users', $e->getMessage());

    echo generateResponseBody(400, [], $e->getMessage());
}