<?php
header("Content-Type: application/json; charset=utf-8");

$mysqli = new mysqli("localhost", "root", "root", "BeastWords");
$mysqli->query("SET NAMES 'utf8'");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$word = $_POST['w'] ?? $_GET['w'] ?? "";

$responce = [];
$responce['result'] = false;
$responce['value'] = '';

if (! $word) {
    $result['value'] = 'no request';
    echo json_encode($result);
    exit();
}

$query = 'SELECT id, word, next_words_id FROM `words` WHERE `word` LIKE \'%' . $word . '%\'';
if ($result = $mysqli->query($query)) {
    // リクエスト値に返答できる語録があるか
    if ($result->num_rows) {
        // リクエスト内容を解析
        while ($row = $result->fetch_assoc()){
            $request_words[] = $row['word'];
            $next_words_ids[] = $row['next_words_id'];
        }

        // レスポンス内容を算出
        $query = 'SELECT `word` FROM `words` WHERE `id` IN (' . implode(',', $next_words_ids) . ')';
        $result = $mysqli->query($query);
        while ($row = $result->fetch_assoc()) {
            $responce_words[] = $row['word'];
        }

        $responce['result'] = true;
        $responce['value'] = $responce_words[0];
        echo json_encode($responce);
    } else {
        $responce['value'] = 'no match word';
        echo json_encode($responce);
    }
    $result->close();
}
$mysqli->close();
