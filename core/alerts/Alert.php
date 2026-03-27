<?php

namespace Core\Alerts;


class Alert {
    public int $id;
    public string $type;
    public string $head;
    public string $body;

    public function __construct(array $args = []) {
        $this->id = $args["id"] ?? 0; 
        $this->type = $args["type"] ?? ''; 
        $this->head = $args["head"] ?? ''; 
        $this->body = $args["body"] ?? ''; 
    }

    public static function make(string $type, string $head, string $body) : ?self {
        $type = trim($type);
        $head = trim($head);
        $body = trim($body);

        if($type === '' || $head === '' || $body === '') return null;

        return new self([
            'type' => $type,
            'head' => $head,
            'body' => $body
        ]);
    }
}