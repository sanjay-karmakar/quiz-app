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
            throw new Exception('Invalid token, please login again');
        }
    } else {
        throw new Exception('Invalid Authorization format');
    }

    $postData   = file_get_contents("php://input");
    $request    = json_decode($postData, true);

    $userType   = 'U';

    $getAllSubmissions = $conn->prepare("
        SELECT qstnsub.user_id, qstnsub.submission_status, qstnsub.total_question_answered, qstnsub.total_correct_answer, qstnsub.created_at, usr.username as username FROM ".TABLE_PREFIX."quiz_submissions as qstnsub
        INNER JOIN ".TABLE_PREFIX."users as usr
        ON qstnsub.user_id = usr.id
        WHERE
        usr.usertype = ?
    ");
    $getAllSubmissions->bind_param('s', $userType);
    $getAllSubmissions->execute();
    $result = $getAllSubmissions->get_result();

    if ($result && $result->num_rows > 0) {
        $dataset = [];
        while ($row = $result->fetch_assoc()) {
            $dataset[] = [
                'username'                  => $row['username'],
                'submission_status'         => $row['submission_status'] == 'S' ? 'Submitted' : 'Not Submitted',
                'total_question_answered'   => $row['total_question_answered'],
                'total_correct_answer'      => $row['total_correct_answer'],
                'created_at'                => $row['created_at'],
            ];
        }
        echo generateResponseBody(200, $dataset, count($dataset).' submissions found.');
    } else {
        echo generateResponseBody(400, $result, 'No submissions found.');
    }
} catch(Exception $e) {
    logWrite('submissions', $e->getMessage());

    echo generateResponseBody(400, [], $e->getMessage());
}