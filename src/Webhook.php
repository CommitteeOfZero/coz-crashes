<?php namespace CoZCrashes;

class Webhook extends Base {
    public function send($body) {
        $payload = json_encode($body);
        $curl = curl_init($this->c->config['app']['discordWebhookUrl']);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload) // strlen returns byte length
        ]);
        curl_exec($curl);
        curl_close($curl);
    }
}