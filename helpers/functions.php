<?php

function debug(mixed $var, bool $kill = true) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    if($kill) exit;
}

function s(string $html) : string{
    return htmlspecialchars($html);
}