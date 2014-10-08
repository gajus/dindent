<?php
require __DIR__ . '/../vendor/autoload.php';

function error ($error) {
    echo $error;
    exit(1);
}

$options = getopt(null, array('input:', 'indentation_character:', 'inline:', 'block:'));

if (!isset($_SERVER['argv'][1])) {
    echo '
Indent HTML.

Options:
    --input=./input_file.html
        Input file
    --indentation_character="    "
        Character(s) used for indentation. Defaults to 4 whitespace characters.
    --inline=""
        A list of comma separated "inline" element names.
    --block=""
        A list of comma separated "block" element names.

Examples:
    ./dindent.php --input="./input.html"
        Indent "input.html" file and print the output to STDOUT.

    ./dindent.php --input="./input.html" | tee ./output.html
        Indent "input.html" file and dump the output to "output.html".

    ./dindent.php --input="./input.html" --indentation_character="\t"
        Indent "input.html" file using tab to indent the markup.

    ./dindent.php --input="./input.html" --inline="div,p"
        Indent "input.html" file treating <div> and <p> elements as inline.

    ./dindent.php --input="./input.html" --block="span,em"
        Indent "input.html" file treating <span> and <em> elements as block.
';

exit;
}

if (!isset($options['input'])) {
    error('Missing "input" parameter.');
} else if (!file_exists($options['input'])) {
    error('"input" file does not exist.');
}

$indenter = new \Gajus\Dindent\Indenter(isset($options['indentation_character']) ? array('indentation_character' => $options['indentation_character']) : array());

if (isset($options['inline'])) {
    foreach (explode(',', $options['inline']) as $element_name) {
        $indenter->setElementType($element_name, \Gajus\Dindent\Indenter::ELEMENT_TYPE_INLINE);
    }
}

if (isset($options['block'])) {
    foreach (explode(',', $options['block']) as $element_name) {
        $indenter->setElementType($element_name, \Gajus\Dindent\Indenter::ELEMENT_TYPE_BLOCK);
    }
}

$output = $indenter->indent(file_get_contents($options['input']));

echo $output;