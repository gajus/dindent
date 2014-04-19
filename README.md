# Dindent

[![Build Status](https://travis-ci.org/gajus/dindent.png?branch=master)](https://travis-ci.org/gajus/dindent)
[![Coverage Status](https://coveralls.io/repos/gajus/dindent/badge.png?branch=master)](https://coveralls.io/r/gajus/dindent?branch=master)

Dindent (aka., "HTML beautifier") will indent HTML for development and testing. Dedicated for those who suffer from reading a template engine produced markup. Try it in the [sandbox](http://gajus.com/dindent/sandbox/).

## A word of caution

Do not be bothered about the markup indentation in the production environment. Do not use HTML beautifiers-filters to hide the underlying issues with the code.

If you are looking to remove malicious code or make sure that your document is standards compliant, consider the following alternatives:

* [HTML Purifier](https://github.com/Exercise/HTMLPurifierBundle)
* [DOMDocument::$formatOutput](http://www.php.net/manual/en/class.domdocument.php)
* [Tidy](http://www.php.net/manual/en/book.tidy.php)

If you need to indent your code in the development environment, beware that earlier mentioned libraries will attempt to fix your markup (that's their primary purpose; indentation is a by-product).

Dindent will not attempt to sanitise or otherwise manipulate your output beyond indentation. This library is designed to make debugging easier. Do not use it in production.

## Use

Parser implements a single method, `indent`:

```php
$parser = new \Gajus\Dindent\Parser();
$output = $parser->indent('<html>[..]</html>');
```

In the above example, `[..]` is a placeholder for:

```html
<!DOCTYPE html>
<html>
<head></head>
<body>
    <script>
    console.log('te> <st');
    function () {
        test; <!-- <a> -->
    }
    </script>
    <div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <div><table border="1" style="background-color: red;"><tr><td>A cell    test!</td>
<td colspan="2" rowspan="2"><table border="1" style="background-color: green;"><tr> <td>Cell</td><td colspan="2" rowspan="2"></td></tr><tr>
        <td><input><input><input></td></tr><tr><td>Cell</td><td>Cell</td><td>Ce
            ll</td></tr></table></td></tr><tr><td>Test <span>Ce       ll</span></td></tr><tr><td>Cell</td><td>Cell</td><td>Cell</td></tr></table></div></div>
</body>
</html>
```

Dindent will convert it to:

```HTML
<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <script>
    console.log('te> <st');
    function () {
        test; <!-- <a> -->
    }
    </script>
        <div>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
            <div>
                <table border="1" style="background-color: red;">
                    <tr>
                        <td>A cell test!</td>
                        <td colspan="2" rowspan="2">
                            <table border="1" style="background-color: green;">
                                <tr>
                                    <td>Cell</td>
                                    <td colspan="2" rowspan="2"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <input>
                                        <input>
                                        <input>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                    <td>Ce ll</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>Test <span>Ce ll</span></td>
                    </tr>
                    <tr>
                        <td>Cell</td>
                        <td>Cell</td>
                        <td>Cell</td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
```

## Options

`Parser` constructor accepts the following options that control indentation:

|Name|Description|
|---|---|
|`indentation_character`|Character(s) used for indentation. Defaults to 4 whitespace characters.|

## Known issues

* Does not treat comments nicely and IE conditional blocks.

## Installation

The recommended way to use Dindent is through [Composer](https://getcomposer.org/).

```json
{
    "require": {
        "gajus/dindent": "1.0.*"
    }
}
```
