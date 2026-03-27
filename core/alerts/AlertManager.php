<?php

namespace Core\Alerts;

class AlertManager {

    // Persistence
    public static function push(Alert $alert) : Alert {
        start_session();
        $_SESSION["alerts"] ??= [];
        $alert->id = (array_key_last($_SESSION["alerts"]) ?? - 1) + 1;
        $_SESSION["alerts"][$alert->id] = $alert;

        return $alert;
    }

    public static function getAll() : array {
        start_session();
        $alerts = $_SESSION["alerts"] ?? [];
        $_SESSION["alerts"] = [];

        return $alerts;
    }

    public static function getById(int $id) : ?Alert {
        start_session();
        $alerts = $_SESSION["alerts"] ?? [];
        if(!array_key_exists($id, $alerts)) return null;
        $alert = $_SESSION["alerts"][(int) $id];
        unset($_SESSION["alerts"][(int) $id]);
        return $alert;
    }

    public static function clear() : void {
        start_session();
        $_SESSION["alerts"] = [];
    }

    public static function delete(int $id) : void {
        start_session();
        $alerts = $_SESSION["alerts"] ?? [];
        if(!array_key_exists($id, $alerts)) return;
        unset($_SESSION["alerts"][(int) $id]);
    }

    // Contextual making

    public static function error(string $head, string $body) : ?Alert {
        $alert = Alert::make('error', $head, $body);
        if (!$alert) return null;
        return self::push($alert);
    }
    public static function success(string $head, string $body) : ?Alert {
        $alert = Alert::make('success', $head, $body);
        if (!$alert) return null;
        return self::push($alert);
    }
    public static function warning(string $head, string $body) : ?Alert {
        $alert = Alert::make('warning', $head, $body);
        if (!$alert) return null;
        return self::push($alert);
    }
    public static function info(string $head, string $body) : ?Alert {
        $alert = Alert::make('info', $head, $body);
        if (!$alert) return null;
        return self::push($alert);
    }
}