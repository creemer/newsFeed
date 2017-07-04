<?php

spl_autoload_register(function ($name) {
    require "$name.class.php";
});