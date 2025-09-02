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

    $isActive = 'Y';

    $postData   = file_get_contents("php://input");
    $request    = json_decode($postData, true);

    // // Check if session token exists
    // if (empty($_SESSION['_token'])) {
    //     throw new Exception('Session token not found. Please log in again.');
    // }

    $getAllQuestions = $conn->prepare("
        SELECT qstn.id, qstn.question, answr.id AS quiz_answr_id, answr.quiz_questions_id, answr.options AS option_title, answr.is_correct AS correct_option FROM ".TABLE_PREFIX."quiz_questions AS qstn
        INNER JOIN ".TABLE_PREFIX."quiz_questions_answers AS answr
        ON qstn.id = answr.quiz_questions_id 
        WHERE qstn.is_active = ?
    ");
    $getAllQuestions->bind_param('s', $isActive);
    $getAllQuestions->execute();
    $result = $getAllQuestions->get_result();

    if ($result && $result->num_rows > 0) {
        $dataset = $questions = [];

        while ($row = $result->fetch_assoc()) {
            $qid = $row['id'];

            $dataset[$qid]['question'] = $row['question'];
            $dataset[$qid]['id'] = $qid;
            $dataset[$qid]['options'][] = [
                'quiz_answr_id'  => $row['quiz_answr_id'],
                'option_title'   => $row['option_title'],
                'correct_option' => $row['correct_option'],
            ];
        }
        $questions = array_values($dataset);

        echo generateResponseBody(200, $questions, count($dataset) . ' questions found.');
    } else {
        echo generateResponseBody(400, [], 'No questions found.');
    }
} catch(Exception $e) {
    echo generateResponseBody(400, [], $e->getMessage());
}
