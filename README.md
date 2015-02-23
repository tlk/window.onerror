window.onerror
====
#### Log client side javascript exceptions on your server. Works in all major browsers.

When our client side javascript is deployed it has to work in a hostile browser environment with network errors, limited memory, malware and browser extensions of poor quality. With automated testing we have come a long way, but considering the unpredictable browser environment, there will be error cases that we cannot possibly predict.

Nevertheless, our users expect everything to work flawlessly and we cannot expect them to provide reliable error reports. As a consequence, we are looking for a method to detect new javascript errors as soon as possible.

##### So how do we keep track of client side errors?

A simple solution is to attach an javascript error handler to window.onerror, see https://developer.mozilla.org/en-US/docs/Web/API/GlobalEventHandlers.onerror

Note that this does not catch all errors! But it might catch enough. Let us continue this happy-go-lucky approach and allow the error handler to be optimistic, because then we can keep it short and simple. That makes it less intrusive to inline the entire javascript error handler in a html script tag, which is preferable over using script src because that extra http get request might fail.

On the server side we keep a log file with all the error messages we have 
received from the client side error handler.

We can now use this server side log file to discover when our client side javascript is failing in new and unexpected ways. Keep in mind that this is a happy-go-lucky solution, and that by design we are going to miss some errors.


##### Classes of errors
There are two classes of client side errors:
* Errors that we can fix, and
* Errors we cannot fix 

I have collected a list of the last class at https://github.com/tlk/window.onerror-blacklist


#### Get Started

1. Insert the following before any other script tags in your HTML:

    ```html
    <script>
    window.onerror = function(m,u,l,c) {
        if (window.XMLHttpRequest) {
            var xhr = new XMLHttpRequest();
            var msg = "msg="+encodeURIComponent(m)+"&url="+encodeURIComponent(u)+"&line="+l+"&col="+c+"&href="+encodeURIComponent(window.location.href);
            xhr.open("GET", "/logger.php?"+msg, true);
            xhr.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
            xhr.send();
        }
    };
    </script>
    ```

2. Make your server log the data that is posted to /logger.php e.g:

    ```php
    <?php

if (!$_POST['msg']) {
        exit;
}

if ($_POST['msg'] == 'Script error.' && $_POST['line'] == '0') {
        exit;
}

$log = array();
$log[] = $_POST['href'];
$log[] = $_POST['url'];
$log[] = $_POST['msg'];
$log[] = $_POST['line'];
$log[] = $_POST['col'];
$log[] = $_SERVER['HTTP_USER_AGENT'];
$log[] = time();

$line = json_encode($log) . "\n";

# NOTE: Adjust this path and file permissions
file_put_contents('/var/log/window.onerror/all.log', $line, FILE_APPEND | LOCK_EX);

?>
    ```

3. Verify by adding a script error:

    ```html
    <script>
    alrt("foo");
    </script>
    ```


#### See also

* https://github.com/tlk/window.onerror-blacklist for a list of odd exceptions
* https://github.com/errorception/ie-error-languages on how to interpret localized errors (MSIE)
* https://github.com/ryanseddon/sourcemap-onerror
* https://github.com/stacktracejs/stacktrace.js
* https://github.com/occ/TraceKit
* https://github.com/jefferyto/glitchjs
* https://github.com/protonet/simple-javascript-airbrake-notifier
* http://blog.protonet.info/post/9620971736/exception-notifier-javascript
* http://jserrlog.appspot.com


#### Commercial solutions

* http://www.muscula.com
* https://errorception.com
* https://bugsnag.com
* https://www.debuggify.net
* http://trackjs.com
