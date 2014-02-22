<?php
namespace Gajus\Dindent;

/**
 * @link https://github.com/gajus/dintent for the canonical source repository
 * @license https://github.com/gajus/dintent/blob/master/LICENSE BSD 3-Clause
 */
class Parser {
    private
        $log = [],
        $indent = '    ',
        $temporary_replacements_script = [],
        $temporary_replacements_inline = [];

    const MATCH_INDENT_NO = 0;
    const MATCH_INDENT_DECREASE = 1;
    const MATCH_INDENT_INCREASE = 2;
    const MATCH_DISCARD = 3;

    public function indent ($input) {
        $this->log = [];

        // Dindent does not indent <script> body. Instead, it temporary removes it from the code, indents the input, and restores the script body.
        $input = preg_replace_callback('/<script\b[^>]*>([\s\S]*?)<\/script>/mi', function ($e) { $this->temporary_replacements_script[] = $e[0]; return '<script></script>'; }, $input);
        
        // Removing double whitespaces to make the source code easier to read.
        // With exception of <pre>/ CSS white-space changing the default behaviour, double whitespace is meaningless in HTML output.
        // This reason alone is sufficient not to use Dindent in production.
        $input = str_replace("\t", '', $input);
        $input = preg_replace('/\s{2,}/', ' ', $input);

        // Remove inline tags and replace them with text entities.
        $input = preg_replace_callback('/<(b|i|abbr|em|strong|a|span)[^>]*>(?:[^<]*)<\/\1>/', function ($e) { $this->temporary_replacements_inline[] = $e[0]; return 'ᐃ' . count($this->temporary_replacements_inline) . 'ᐃ'; }, $input);

        $subject = $input;

        $output = '';

        $next_line_indentation_level = 0;

        do {
            $indentation_level = $next_line_indentation_level;

            foreach ([
                // block tag
                '/^(<([a-z]+)(?:[^>]*)>(?:[^<]*)<\/(?:\2)>)/' => static::MATCH_INDENT_NO,
                // DOCTYPE
                '/^<!([^>]*)>/' => static::MATCH_INDENT_NO,
                // tag with implied closing
                '/^<(input|link|meta|base|br|img|hr)([^>]*)>/' => static::MATCH_INDENT_NO,
                // opening tag
                '/^<[^\/]([^>]*)>/' => static::MATCH_INDENT_INCREASE,
                // closing tag
                '/^<\/([^>]*)>/' => static::MATCH_INDENT_DECREASE,
                // self-closing tag
                '/^<(.+)\/>/' => static::MATCH_INDENT_DECREASE,
                // whitespace
                '/^(\s+)/' => static::MATCH_DISCARD,
                // text node
                '/([^<]+)/' => static::MATCH_INDENT_NO
                ] as $pattern => $rule) {
                if ($match = preg_match($pattern, $subject, $matches)) {
                    $this->log[] = [
                        'rule' => ['NO', 'DECREASE', 'INCREASE', 'DISCARD'][$rule],
                        'pattern' => $pattern,
                        'subject' => $subject,
                        'match' => $matches[0]
                    ];

                    $subject = mb_substr($subject, mb_strlen($matches[0]));

                    if ($rule === static::MATCH_DISCARD) {
                        break;
                    }

                    if ($rule === static::MATCH_INDENT_NO) {
                        
                    } else if ($rule === static::MATCH_INDENT_DECREASE) {
                        $next_line_indentation_level--;
                        $indentation_level--;
                    } else {
                        $next_line_indentation_level++;
                    }

                    if ($indentation_level < 0) {
                        $indentation_level = 0;
                    }

                    #$output .= str_repeat($this->indent, $indentation_level) . 'A:' . $indentation_level . "\n";
                    $output .= str_repeat($this->indent, $indentation_level) . $matches[0] . "\n";

                    break;
                }
            }
        } while ($match);

        $interpreted_input = implode('', array_map(function ($e) { return $e['match']; }, $this->log));

        if ($interpreted_input !== $input) {
            throw new \RuntimeException('Did not reproduce the exact input.');
        }

        $output = preg_replace_callback('/(<(\w+)[^>]*>)\s*(<\/\2>)/', function ($e) { return $e[1] . $e[3]; }, $output);

        $output = preg_replace_callback('/<script><\/script>/', function ($e) { return array_shift($this->temporary_replacements_script); }, $output);

        foreach ($this->temporary_replacements_inline as $i => $original) {
            $output = str_replace('ᐃ' . ($i + 1) . 'ᐃ', $original, $output);   
        }

        return trim($output);
    }

    /**
     * Debugging utility. Get log for the last indent operation.
     *
     * @return array
     */
    public function getLog () {
        return $this->log;
    }
}