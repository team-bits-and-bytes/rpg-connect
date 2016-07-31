<?php

class RedisSerialization extends \Predis\Command\StringSet {
    protected function filterArguments($arguments) {
        $arguments[1] = json_encode($arguments[1]);
        return $arguments;
    }
}

class RedisDeserialization extends \Predis\Command\StringGet {
    public function parseResponse($data) {
        return json_decode($data);
    }
}