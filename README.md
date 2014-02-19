# Dindent

[![Build Status](https://travis-ci.org/gajus/dindent.png?branch=master)](https://travis-ci.org/gajus/dindent)
[![Coverage Status](https://coveralls.io/repos/gajus/dindent/badge.png)](https://coveralls.io/r/gajus/dindent)

* HTML indentation for development and testing.
* Do not use in production.

Dedicated to developers who suffer from OCD and cannot bare reading template engine produced output.

If you are looking for documentation validation or sanitisation, consider:

* [DOMDocument::$formatOutput](http://www.php.net/manual/en/class.domdocument.php)
* [HTML Purifier](http://htmlpurifier.org/)
* [Tidy](http://www.php.net/manual/en/book.tidy.php)

You can test Dindent in a [sandbox](http://gajus.com/dindent/sandbox/) pastebin.

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