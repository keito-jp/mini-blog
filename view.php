<?php
session_start();
require('dbconnect.php');

if (empty($_REQUEST['id'])) {
  header('Location: index.php');
  exit();
}

// 投稿を取得する
$sql = sprintf('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=%d ORDER BY p.created DESC',
  mysql_real_escape_string($_REQUEST['id'])
);
$posts = mysql_query($sql) or die(mysql_error());

// htmlspecialcharsかんすう
function h($value) {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Twitter風掲示板</title>
</head>
<body>
<div class="wrapper">
  <header>
    <h1>Twitter風掲示板</h1>
  </header>
  <main>
    <p>&laquo;<a href="index.php">一覧にもどる</a></p>
<?php
if ($post = mysql_fetch_assoc($posts)):
?>
    <div class="msg">
      <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>">
      <p><?php echo h($post['message']); ?><span class="name"> (<?php echo h($post['name']); ?>) </span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re:</a>]</p>
      <p class="day"><?php echo h($post['created']); ?></p>
    </div>
<?php
else:
?>
    <p>その投稿は削除されたか、URLがまちがってるよっ</p>
<?php
endif;
?>
  </main>
  <footer>
    <p>Copyright &copy;2015 Kate Inc.</p>
  </footer>
</div>
</body>
</html>