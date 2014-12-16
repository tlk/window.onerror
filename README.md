window.onerror
====
#### Log client side exceptions on your server when they occur in production. Works in all major browsers. Get a glimpse into what's going on when something fails in the browser.

#### The idea
Network errors, limited memory and badly written browser extensions can be a
hostile environment for your client side code. Nevertheless, you want to
improve your code and make it even more robust. How do you keep track of these
"odd errors"? Well, a simple solution is to attach an error handler to
window.onerror. If we allow the error handler to be optimistic it can be short
and simple. Basically, it's just a matter of telling your server that something
went wrong, using a simple AJAX call.

On your server, log everything that is posted to /window.onerror. After a while
you will be able to see a new side of your web application. Now you can make it
better.

There are commercial solutions that already do this and more, and this simple
script is not meant to compete with those services.


#### Get Started

1. Insert the following before any other script tags in your HTML:

    ```html
    <script>
    window.onerror = function(m,u,l,c) {
        if (window.XMLHttpRequest) {
            var xhr = new XMLHttpRequest();
            var msg = "msg="+encodeURIComponent(m)+"&url="+encodeURIComponent(u)+"&line="+l+"&col="+c+"&href="+encodeURIComponent(window.location.href);
            xhr.open("GET", "/window.onerror?"+msg, true);
            xhr.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
            xhr.send();
        }
    };
    </script>
    ```

2. Make your server log the data that is posted to /window.onerror e.g:

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

* https://github.com/tlk/window.onerror-blacklist for a list of "odd" exceptions
* https://github.com/errorception/ie-error-languages on how to interpret localized errors (MSIE)
* https://github.com/ryanseddon/sourcemap-onerror
* https://github.com/stacktracejs/stacktrace.js
* https://github.com/occ/TraceKit
* https://github.com/jefferyto/glitchjs
* https://github.com/protonet/simple-javascript-airbrake-notifier
* http://blog.protonet.info/post/9620971736/exception-notifier-javascript
* http://jserrlog.appspot.com/


#### Commercial solutions

* http://www.muscula.com
* https://errorception.com
* https://bugsnag.com
* https://www.debuggify.net
* http://trackjs.com/
