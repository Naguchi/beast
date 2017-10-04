<?php
// header("Content-Type: application/json; charset=utf-8");

$mysqli = new mysqli("localhost", "root", "root", "BeastWords");
$mysqli->query("SET NAMES 'utf8'");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$params = $_GET;
$word = $params['w'] ?? "";

if (! $word) {
    echo '語録がないゾ～';
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
    }
    $result->close();
}
$mysqli->close();

?>

<html lang="en">

<table border="1" cellspacing="0" cellpadding="1" align="left">
  <p>語録「<?= $word; ?>」</p>
  <tr>
    <th>候補</th><th>返答</th>
  </tr>
<?php for($i=0; $i<count($request_words); $i++) { ?>
  <tr align="left">
    <td><?= $request_words[$i]; ?></td>
    <td><?= $responce_words[$i]; ?></td>
  </tr>
<?php } ?>
</table>

