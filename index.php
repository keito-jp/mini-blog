<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  // ログインしている
  $_SESSION['time'] = time();

  $sql = sprintf('SELECT * FROM members WHERE id=%d',
    mysql_real_escape_string($_SESSION['id'])
  );
  $record = mysql_query($sql) or die(mysql_error());
  $member = mysql_fetch_assoc($record);
} else {
  // ログインしていない
  header('Location: login.php');
  exit();
}

// 投稿を記録する
if (!empty($_POST)) {
  if ($_POST['message'] != '') {
    $sql = sprintf('INSERT INTO posts SET member_id=%d, message="%s", reply_post_id=%d, created=NOW()',
      mysql_real_escape_string($member['id']),
      mysql_real_escape_string($_POST['message']),
      mysql_real_escape_string($_POST['reply_post_id'])
    );
    mysql_query($sql) or die(mysql_error());

    header('Location: index.php');
    exit();
  }
}

// 投稿を取得する
if (isset($_REQUEST['page'])) {
  $page = $_REQUEST['page'];
} else {
  $_REQUEST['page'] = 1;
  $page = $_REQUEST['page'];
}

if ($page == '') {
  $page = 1;
}
$page = max($page, 1);

// 最終ページを取得する
$sql = 'SELECT COUNT(*) AS cnt FROM posts';
$recordSet = mysql_query($sql);
$table = mysql_fetch_assoc($recordSet);
$maxPage = ceil($table['cnt'] / 10);
$page = min($page, $maxPage);

$start = ($page - 1) * 10;
$start = max(0, $start);

$sql = sprintf('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT %d, 10', $start);
$posts = mysql_query($sql) or die(mysql_error());

// 返信の場合
if (isset($_REQUEST['res'])) {
  $sql = sprintf('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=%d ORDER BY p.created DESC',
    mysql_real_escape_string($_REQUEST['res'])
  );
  $record = mysql_query($sql) or die(mysql_error());
  $table = mysql_fetch_assoc($record);
  $message = '@' . $table['name'] . ' ' . $table['message'];
}

// htmlspecialcharsかんすう
function h($value) {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// 投稿内のURLにリンクを設定かんすう
function makeLink($value) {
  return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2" target="_blank">\1\2</a>' , $value);
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
    <div style="text-align: left;"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <dt><?php echo htmlspecialchars($member['name']); ?>さん、メッセージをどうぞ</dt>
        <dl>
          <textarea name="message" cols="50" rows="5"><?php if (isset($message)) { echo h($message); } ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>">
        </dl>
        <div>
          <input type="submit" value="投稿する">
        </div>
      </dl>
    </form>
<?php 
while($post = mysql_fetch_assoc($posts)):
?>
    <article>
      <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>">
      <p><?php echo makeLink(h($post['message'])); ?><span class="name"> (<?php echo h($post['name']); ?>) </span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re:</a>]</p>
      <p class="day"><a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>
<?php 
if ($post['reply_post_id'] > 0):
?>
      <a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">返信元のメッセージ</a>
<?php
endif;
?>
<?php
if ($_SESSION['id'] == $post['member_id']):
?>
      [<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color: #f33;">削除</a>]
<?php
endif;
?>
      <hr>
      </p>
    </article>
<?php
endwhile;
?>

    <ul>
<?php
if($page > 1) {
?>
      <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
<?php
} else {
?>
      <li>前のページへ</li>
<?php
}
?>
<?php
if($page < $maxPage) {
?>
      <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
<?php
} else {
?>
      <li>次のページへ</li>
<?php
}
?>
    </ul>
  </main>
  <footer>
    <p>Copyright &copy;2015 Kate Inc.</p>
  </footer>
</div>
</body>
</html>