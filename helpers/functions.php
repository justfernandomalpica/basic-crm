<?php

function debug(mixed $var, bool $kill = true) : void {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    if($kill) exit;
}

function s(string $html) : string{
    return htmlspecialchars($html);
}

function start_session() : void {
    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}