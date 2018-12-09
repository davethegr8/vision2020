<?php

namespace App;

class Slack
{
    protected $params = [];

    public function __construct($url, $options = [])
    {
        $this->url = $url;
        $this->params = $options;
    }

    public function send($message, array $payload = [])
    {
        $payload = array_merge($this->params, $payload);
        $payload['text'] = $message;

        $data = "payload=" . json_encode($payload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
