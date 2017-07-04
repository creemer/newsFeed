<?php
require "inc/lib.inc.php";

$title = $news->clearStr($_POST['title']);
$category = $news->clearInt($_POST['category']);
$description = $news->clearStr($_POST['description']);
$source = $news->clearStr($_POST['source']);

if(!empty($title) && !empty($description) && !empty($source)) {
    if(!$news->saveNews($title, $category, $description, $source)){
        $errMsg = "Произошла ошибка при добавлении новости";
    }else{
        $errMsg = "Запись успешно добавлена";
        header("Location: news.php");
    } 
} else {
    $errMsg = "Заполните все поля формы!";
}
