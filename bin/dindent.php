<?php
require __DIR__ . '/../vendor/autoload.php';

$options = getopt(null, ['input:', 'output:', 'indentation_character:']);

if (!isset($options['input'])) {
    throw new \InvalidArgumentException('Missing "input" parameter.');
} else if (!file_exists($options['input'])) {
    throw new \ErrorException('"input" file does not exist.');
}

if (isset($options['output'])) {
    $path = realpath(dirname($options['output']));

    if (!$path) {
        throw new \ErrorException('"output" file path does not exist.');
    }

    if (is_dir($options['output'])) {
        throw new \ErrorException('"output" must refer to a file.');
    }

    if (file_exists($options['output'])) {
        if (!is_writable($options['output'])) {
            throw new \ErrorException('"output" file is not writable.');
        }
    } else if (!touch($options['output'])) {
        throw new \ErrorException('"output" file path is not writable.');
    }
}

$indenter = new \Gajus\Dindent\Indenter(isset($options['indentation_character']) ? array('indentation_character' => $options['indentation_character']) : array());

$output = $indenter->indent(file_get_contents($options['input']));

if (isset($options['output'])) {
    file_put_contents($options['output'], $output);
}