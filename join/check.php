<?php
session_start();
require('../dbconnect.php');

if (!isset($_SESSION['join'])) {
  header('Location: index.php');
  exit();
}

if (!empty($_POST)) {
  $sql = sprintf('INSERT INTO members SET name="%s", email="%s", password="%s", picture="%s", created="%s"',
    mysql_real_escape_string($_SESSION['join']['name']),
    mysql_real_escape_string($_SESSION['join']['email']),
    mysql_real_escape_string(sha1($_SESSION['join']['password'])),
    mysql_real_escape_string($_SESSION['join']['image']),
    date('Y-m-d H:i:s')
  );
  mysql_query($sql) or die(mysql_error());
  unset($_SESSION['join']);

  header('Location: thanks.php');
  exit();
}
?>
<form action="" method="post">
  <input type="hidden" name="action" value="submit">
  <dl>
    <dt>ニックネーム</dt>
    <dd>
      <?php echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES, 'UTF-8'); ?>
    </dd>
    <dt>メールアドレス</dt>
    <dd>
      <?php echo htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES, 'UTF-8'); ?>
    </dd>
    <dt>パスワード</dt>
    <dd>
      ********
    </dd>
    <dt>プロフィール画像</dt>
    <dd>
      <img src="../member_picture/<?php echo $_SESSION['join']['image']; ?>" width="100" height="100">
    </dd>
  </dl>
  <div>
    <a href="index.php?action=rewrite">&laquo;&nbsp;修正</a>
    <input type="submit" value="登録する">
  </div>
</form>
