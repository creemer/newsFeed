<?php
$allNews = $news->getNews();
if(!$allNews) $errMsg = "Произошла ошибка при выводе новостной ленты";

foreach ($allNews as $value) {
    echo "<div class='news'>
            <h2>".$value['title']."</h2>
            <h3>".$value['category']."</h3>
            <p>".$value['description']."</p>
            <p>Источник : ".$value['source']."</p>
            <p>Время добавления :".date("d-m-Y H:i", $value['datetime'])."</p>
            <a href='news.php?id=".$value['id']."' class='deleteButton'> Удалить новость</a>
            </div>";
}