<?php
$content = json_decode(file_get_contents('test.json'));
//echo str_replace(["```json\n", "\n```"], '', current($content->choices)->message->content);die;
$result = json_decode(str_replace(["```json\n", "\n```"], '', current($content->choices)->message->content));
var_dump($result);
