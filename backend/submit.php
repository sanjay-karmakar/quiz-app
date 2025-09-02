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
    
    $postData   = file_get_contents("php://input");
    $request    = json_decode($postData, true);

    $dataset = [];    
    $totalQuestionAnswered = $totalCorrectAnswer = 0;
    $answerStatus = 'Wrong Answer';
    $isCorrect = 'Y';

    $answers = $request['answers'];    

    foreach ($answers as $key => $answerId) {
        $explodedValue = explode('_', $key);
        $questionId = $explodedValue[1];

        if ($answerId) {
            $getAllQuizQuestionsAnswers = $conn->prepare("
                SELECT * FROM ".TABLE_PREFIX."quiz_questions_answers
                WHERE id = ? AND quiz_questions_id = ? AND is_correct = ?
            ");
            $getAllQuizQuestionsAnswers->bind_param('iis', $answerId, $questionId, $isCorrect);
            $getAllQuizQuestionsAnswers->execute();
            $result = $getAllQuizQuestionsAnswers->get_result();

            if ($result->num_rows) {
                $answerStatus = 'Correct Answer';
                $totalCorrectAnswer++;
            } else {
                $answerStatus = 'Wrong Answer';
            }

            $dataset[$questionId] = $answerStatus;

            $totalQuestionAnswered++;
        } else {
            $dataset[$questionId] = 'Not Answered';
        }
    }
    
    $dataset['total_question_answered'] = $totalQuestionAnswered;
    $dataset['total_correct_answer']    = $totalCorrectAnswer;

    if ($totalQuestionAnswered == 0) {
        echo generateResponseBody(400, $dataset, 'No questions answered. Please select at least one answer.');
        return;
    }

    $userId             = $_SESSION['_user_id'];
    $answers            = json_encode($answers);
    $submissionStatus   = 'S';
    
    $insertIntoQuizSubmission = $conn->prepare("
        INSERT INTO ".TABLE_PREFIX."quiz_submissions (user_id, answers, submission_status, total_question_answered, total_correct_answer) values(?, ?, ?, ?, ?)
    ");
    $insertIntoQuizSubmission->bind_param("issii", $userId, $answers, $submissionStatus, $totalQuestionAnswered, $totalCorrectAnswer);
    $insertIntoQuizSubmission->execute();

    echo generateResponseBody(200, $dataset, $answers.' questions answered.');
} catch(Exception $e) {
    logWrite('submit', $e->getMessage());

    echo generateResponseBody(400, [], $e->getMessage());
}