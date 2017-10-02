<?php
header("Content-Type: application/json; charset=utf-8");


$mysqli = new mysqli("localhost", "root", "root", "BeastWords");
$mysqli->query("SET NAMES 'utf8'");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$params = $_GET;
$word = ($params['w']);
$return = array();

$query = 'SELECT id, word, next_words_id FROM `words` WHERE `word` LIKE \'%' . $word . '%\'';

if ($result = $mysqli->query($query)) {
    // リクエスト値に返答できる語録があるか
    if ($result->num_rows) {
        while ($row = $result->fetch_assoc()){
            $candidate_words_id[] = $row['next_words_id'];
        }
        $next_words_id = $candidate_words_id[array_rand($candidate_words_id)];

        $query = 'SELECT `word` FROM `words` WHERE `id` = ' . $next_words_id;

        $result = $mysqli->query($query);
        while ($row = $result->fetch_assoc()) {
            echo $row["word"];
        }

    } else {
        // 返答できる語録がない場合はランダムで返す
        $query = 'SELECT `word` FROM `words` ORDER BY RAND() LIMIT 1';
        $result = $mysqli->query($query);
        while ($row = $result->fetch_assoc()) {
            echo $row["word"];
        }
    }
    $result->close();
}
$mysqli->close();
