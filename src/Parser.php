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

    const INDENT_NO = 0;
    const INDENT_DECREASE = 1;
    const INDENT_INCREASE = 2;
    const DISCARD = 3;

    public function indent ($input) {
        $this->log = [];

        // Remove scrip content body, but keep the tag.
        $input = preg_replace_callback('/<script\b[^>]*>([\s\S]*?)<\/script>/mi', function ($e) { $this->temporary_replacements_script[] = $e[0]; return '<script></script>'; }, $input);
        
        // Remove whitespaces.
        $input = str_replace("\t", '', $input);
        $input = preg_replace('/\s{2,}/', ' ', $input);

        // Remove inline tags and replace them with text entities.
        $input = preg_replace_callback('/<(b|i|abbr|em|strong|a|span)[^>]*>([\s\S]*?)<\/\1>/', function ($e) { $this->temporary_replacements_inline[] = $e[0]; return 'ᐃ' . count($this->temporary_replacements_inline) . 'ᐃ'; }, $input);

        $subject = $input;

        $output = '';

        $next_line_indentation_level = 0;

        do {
            $indentation_level = $next_line_indentation_level;

            foreach ([
                // block tag
                '/^(<([a-z]+)(?:[^>]*)>(?:[^<]*)<\/(?:\2)>)/' => static::INDENT_NO,
                // self-closing tag
                '/^<(.+)\/>/' => static::INDENT_DECREASE,
                // closing tag
                '/^<\/([^>]*)>/' => static::INDENT_DECREASE,
                // DOCTYPE
                '/^<!(.*)>/' => static::INDENT_NO,
                // tag with implied closing
                '/^<(input|link|meta|base|br|img|hr)([^>]*)>/' => static::INDENT_NO,
                // opening tag
                '/^<[^\/]([^>]*)>/' => static::INDENT_INCREASE,
                // whitespace
                '/^(\s+)/' => static::DISCARD,
                // text node
                '/([^<]+)/' => static::INDENT_NO
                ] as $pattern => $rule) {
                if ($match = preg_match($pattern, $subject, $matches)) {
                    $this->log[] = [
                        'rule' => ['NO', 'DECREASE', 'INCREASE', 'DISCARD'][$rule],
                        'pattern' => $pattern,
                        'subject' => $subject,
                        'match' => $matches[0]
                    ];

                    $subject = mb_substr($subject, mb_strlen($matches[0]));

                    if ($rule === static::DISCARD) {
                        break;
                    }

                    if ($rule === static::INDENT_NO) {
                        
                    } else if ($rule === static::INDENT_DECREASE) {
                        $next_line_indentation_level--;
                        $indentation_level--;
                    } else {
                        $next_line_indentation_level++;
                    }

                    if ($indentation_level < 0) {
                        throw new \RuntimeException('Negative indentation.');
                    }

                    #$output .= str_repeat($this->indent, $indentation_level) . 'A:' . $indentation_level . "\n";
                    $output .= str_repeat($this->indent, $indentation_level) . $matches[0] . "\n";

                    break;
                }
            }
        } while ($match);

        $interpreted_input = implode('', array_map(function ($e) { return $e['match']; }, $this->log));

        #bump($interpreted_input, $input, $this->log);

        if ($interpreted_input !== $input) {
            throw new \RuntimeException('Did not reproduce the exact input.');
        }

        $output = preg_replace_callback('/(<(\w+)[^>]*>)\s*(<\/\2>)/', function ($e) { return $e[1] . $e[3]; }, $output);

        $output = preg_replace_callback('/<script><\/script>/', function ($e) { return array_shift($this->temporary_replacements_script); }, $output);

        foreach ($this->temporary_replacements_inline as $i => $original) {
            $output = str_replace('ᐃ' . ($i + 1) . 'ᐃ', $original, $output);   
        }

        return trim($output);

        /*
        <p>
            <input>
            <input />
            <input></input>
        </p>
        */

        /*foreach ($tags as $tag) {
            $indentation_level = $next_line_indentation_level;

            // Self-closing, doctype or opening and closing tag on the same line, or non-standard tag.
            if ($this->match(['/<(.+)\/>/', '/<!(.*)>/', '/^<([a-z]+)(?:[^>]*)>(?:[^<]*)<\/(?:\1)>/'], $tag)) { // , '/<(input|link|meta|base|br|img|hr)(.*)>/'
      
            // Closing HTML tag
            } else if ($this->match(['/<\/(.*)>/'], $tag)) {
                $next_line_indentation_level--;
                $indentation_level--;
            }
            // If opening tag
            else if ($this->match(['/<[^\/](.*)>/'], $tag)) {
                $next_line_indentation_level++;
            } else {
                throw new \RuntimeException('Unknown tag.');
            }

            if ($indentation_level < 0) {
                #throw new \RuntimeException('Negative indentation.');
                $indentation_level = 0;
            }

            $response .= str_repeat($this->indent, $indentation_level) . $tag . "\n";
        }

        $response = preg_replace_callback('/(<script[^>]*>)(\s+)(<\/script>)/', function ($e) { return array_shift($this->temporary_replacements_script); }, $response);

        foreach ($this->temporary_replacements_inline as $i => $original) {
            $response = str_replace('ᐃ' . ($i + 1) . 'ᐃ', $original, $response);   
        }

        $response = preg_replace_callback('/(<(\w+)[^>]*>)\s*(<\/\2>)/', function ($e) { return $e[1] . $e[3]; }, $response);

        return trim($response);*/
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