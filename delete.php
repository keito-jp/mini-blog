<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
  $id = $_REQUEST['id'];

  $sql = sprintf('SELECT * FROM posts WHERE id=%d',
    mysql_real_escape_string($id)
  );
  $record = mysql_query($sql) or die(mysql_error());
  $table = mysql_fetch_assoc($record);
  if ($table['member_id'] == $_SESSION['id']) {
    // さくじょ
    mysql_query('DELETE FROM posts WHERE id=' . mysql_real_escape_string($id)) or die(mysql_error());
  }
}

header('Location: index.php');
exit();
?>