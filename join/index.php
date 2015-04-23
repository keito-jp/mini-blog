<?php
require('../dbconnect.php');

session_start();

if (!empty($_POST)) {
  // エラー項目の確認
  if ($_POST['name'] == '') {
    $error['name'] = 'blank';
  }
  if ($_POST['email'] == '') {
    $error['email'] = 'blank';
  }
  if (strlen($_POST['password']) < 4) {
    $error['password'] = 'length';
  }
  elseif ($_POST['password'] == '') {
    $error['password'] = 'blank';
  }
  $fileName = $_FILES['image']['name'];
  if (!empty($fileName)) {
    $ext = substr($fileName, -3);
    if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
      $error['image'] = 'type';
    }
  }

  if (empty($error)) {
    // 複数垢チェック
    $sql = sprintf('SELECT COUNT(*) AS cnt FROM members WHERE email="%s"',
      mysql_real_escape_string($_POST['email'])
    );
    $record = mysql_query($sql) or die(mysql_error());
    $table = mysql_fetch_assoc($record);
    if ($table['cnt'] > 0) {
      $error['email'] = 'duplicate';
    }
  }  

  // if (empty($error['name']) || empty($error['email']) || empty($error['password']) || empty($error['image'])) {
  if (empty($error)) {
    // 画像をアップロードする
    $image = date('YmdHis') . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);

    $_SESSION['join'] = $_POST;
    $_SESSION['join']['image'] = $image;
    header('Location: check.php');
    exit();
  }

}

if (isset($_REQUEST['action'])) {
  if ($_REQUEST['action'] == 'rewrite') {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
  }
}
?>
<p>次のフォームに必要事項を入力してください。</p>
<form action="" method="post" enctype="multipart/form-data">
  <dl>
    <dt>ニックネーム<span class="required">必須</span></dt>
    <dd>
      <input type="text" name="name" size="35" maxlength="255" value="<?php if (!empty($_POST)) { echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); } ?>">
      <?php if(isset($error['name'])){ if($error['name'] == 'blank') { ?>
      <p class="error">* ニックネームを入力してくだい＞＜</p>
      <?php }}; ?>
    </dd>
    <dt>メールアドレス<span class="required">必須</span></dt>
    <dd>
      <input type="text" name="email" size="35" maxlength="255" value="<?php if (!empty($_POST)) { echo htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); } ?>">
      <?php if(isset($error['email'])){ if($error['email'] == 'blank') { ?>
      <p class="error">* メールアドレスを入力してください＞＜</p>
      <?php }}; ?>
      <?php if(isset($error['email'])){ if($error['email'] == 'duplicate') { ?>
      <p class="error">* 入力したメールアドレスはすでに登録されています＞＜</p>
      <?php }}; ?>
    </dd>
    <dt>パスワード<span class="required">必須</span></dt>
    <dd>
      <input type="password" name="password" size="10" maxlength="20" value="<?php if (!empty($_POST)) { echo htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');  } ?>">
      <?php if(isset($error['password'])){ if($error['password'] == 'blank') { ?>
      <p class="error">* パスワードを入力してください＞＜</p>
      <?php }}; ?>
      <?php if(isset($error['password'])){ if($error['password'] == 'length') { ?>
      <p class="error">* パスワードは4文字以上で入力してください＞＜</p>
      <?php }}; ?>
    </dd>
    <dt>プロフィール画像<span class="required">必須</span></dt>
    <dd>
      <input type="file" name="image" size="35">
      <?php if(isset($error['image'])){ if($error['image'] == 'type') { ?>
      <p class="error">* 画像はgifまたはjpgまたはpngの画像を指定してください＞＜</p>
      <?php }}; ?>
      <?php if(!empty($_POST)){ if(!empty($error)) { ?>
      <p class="error">* 画像を改めて指定してください＞＜</p>
      <?php }}; ?>
    </dd>
  </dl>
  <div><input type="submit" value="入力内容を確認する"></div>
</form>
