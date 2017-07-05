<?php
require_once "inc/lib.inc.php";

$news = new NewsDB();
$errMsg = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require "save_news.inc.php";
}

if(isset($_GET['id'])){
    require "delete_news.inc.php";
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Новостная лента</title>
	<meta charset="utf-8" />
  <link rel="stylesheet" href="style/style.css">
</head>
<body>
  <div class="wrapper">
    <h1 class="header">Последние новости</h1>
    <?php
      if($errMsg) 
        echo "<h2>ERROR : $errMsg</h2>";
    ?>
    <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
        <label class="label">Заголовок новости:
            <input type="text" name="title" class="text"/><br />
        </label><br />
        <label class="label">Выберите категорию:
            <select class="label" name="category">
              <option value="1">Политика</option>
              <option value="2">Культура</option>
              <option value="3">Спорт</option>
            </select>
        </label><br />
      <br />
      <label class="label">Текст новости:
        <textarea name="description" cols="50" rows="5" class="textarea text"></textarea><br />
      </label><br />
      <label class="label">Источник:
          <input type="text" name="source" class="text"/><br />
      </label><br />
      <br />
      <input type="submit" value="Добавить!" class="sbmButton"/>
    </form>
    <div class="newsWrapper">
      <?php
        require "get_news.inc.php";
      ?>
    </div>
  </div> 
</body>
</html>