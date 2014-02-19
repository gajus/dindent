<?php
namespace Gajus\Dindent;

/**
 * @link https://github.com/gajus/dintent for the canonical source repository
 * @license https://github.com/gajus/dintent/blob/master/LICENSE BSD 3-Clause
 */
class Parser {
    private
        $indent = '    ',
        $temporary_replacements_script = [],
        $temporary_replacements_inline = [];

    public function indent ($input) {
        // Remove scrip content body, but keep the tag.
        $input = preg_replace_callback('/<script\b[^>]*>([\s\S]*?)<\/script>/mi', function ($e) { $this->temporary_replacements_script[] = $e[0]; return '<script></script>'; }, $input);

        // Remove whitespaces.
        $input = str_replace("\t", '', $input);
        $input = preg_replace('/\s{2,}/', ' ', $input);

        // Remove inline tags and replace them with text entities.
        $input = preg_replace_callback('/<span\b[^>]*>([\s\S]*?)<\/span>/', function ($e) { $this->temporary_replacements_inline[] = $e[0]; return 'ᐃ' . count($this->temporary_replacements_inline) . 'ᐃ'; }, $input);

        // Prepare for preg_split.
        // @todo There must be a better way to break the markup between space tags.
        $input = trim($input);
        $input = trim($input, '<');
        $input = trim($input, '>');

        $input = preg_split('/>\s*</', $input);

        $tags = array_map(function ($e) { return '<' . $e . '>'; }, $input);

        $next_line_indentation_level = 0;

        $response = '';

        foreach ($tags as $tag) {
            $indentation_level = $next_line_indentation_level;

            // Self-closing, doctype or opening and closing tag on the same line, or non-standard tag.
            if (preg_match('/<(.+)\/>|<!(.*)>|<[^\/](.*)>([^<]*)<\/(.*)>|<(input|link|meta|base|br|img|hr)(.*)>/', $tag)) {
                      
            // Closing HTML tag
            } else if (preg_match("/<\/(.*)>/", $tag)) {
                $next_line_indentation_level--;
                $indentation_level--;
            }
            // If opening tag
            else if (preg_match("/<[^\/](.*)>/", $tag)) {
                $next_line_indentation_level++;
            } else {
                throw new \RuntimeException('Unknown tag.');
            }

            if ($indentation_level < 0) {
                throw new \RuntimeException('Negative indentation.');
            }

            $response .= str_repeat($this->indent, $indentation_level) . $tag . "\n";
        }

        $response = preg_replace_callback('/(<script[^>]*>)(\s+)(<\/script>)/', function ($e) { return array_shift($this->temporary_replacements_script); }, $response);

        foreach ($this->temporary_replacements_inline as $i => $original) {
            $response = str_replace('ᐃ' . ($i + 1) . 'ᐃ', $original, $response);   
        }

        $response = preg_replace_callback('/(<(\w+)[^>]*>)\s*(<\/\2>)/', function ($e) { return $e[1] . $e[3]; }, $response);

        return trim($response);
    }
}