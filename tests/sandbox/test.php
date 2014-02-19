<?php
require __DIR__ . '/../../vendor/autoload.php';

$parser = new \Gajus\Pindent\Parser();

echo $parser->indent( file_get_contents(__DIR__ . '/test.html') );