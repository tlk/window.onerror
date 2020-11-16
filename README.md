window.onerror
====
#### Log client side javascript exceptions on your server. Works in all major browsers.

When our client side javascript is deployed it has to work in a hostile browser environment with network errors, limited memory, malware and browser extensions of poor quality. With automated testing we have come a long way, but considering the unpredictable browser environment, there will be error cases that we cannot possibly predict.

Nevertheless, our users expect everything to work flawlessly and we cannot expect them to provide reliable error reports. As a consequence, we are looking for a method to detect new javascript errors as soon as possible.

##### So how do we keep track of client side errors?

A simple solution is to attach a javascript error handler to window.onerror.

Note that this does not catch all errors! But it might catch enough. Let us continue this happy-go-lucky approach and allow the error handler to be optimistic, because then we can keep it short and simple. That makes it less intrusive to inline the entire javascript error handler in a html script tag, which is preferable over using script src because that extra http get request might fail.

On the server side we keep a log file with all the error messages we have 
received from the client side error handler.

We can now use this server side log file to discover when our client side javascript is failing in new and unexpected ways. Keep in mind that this is a happy-go-lucky solution, and that by design we are going to miss some errors.


##### Classes of errors
There are two classes of client side errors:
* Errors that we can fix, and
* Errors we cannot fix 

I have collected a list of the last class at https://github.com/tlk/window.onerror-ignore


#### Get Started

1. Insert the following before any other script tags in your HTML:

    ```html
    <script>
    window.onerror = function(m,u,l,c) {
        if (window.XMLHttpRequest) {
            var xhr = new XMLHttpRequest();
            var data = "msg="+encodeURIComponent(m)
                    +"&url="+encodeURIComponent(u)
                    +"&line="+l
                    +"&col="+c
                    +"&href="+encodeURIComponent(window.location.href);
            xhr.open("GET", "/logger.php?"+data, true);
            xhr.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
            xhr.send();
        }
    };
    </script>
    ```

2. Make your server store the data that is being posted to it. This example is written in PHP to illustrate the concept:

    ```php
    <?php

    function getVal($key) {
        return array_key_exists($key, $_GET)
            ? $_GET[$key]
            : '';
    }

    function isValidRequest() {
        return getVal('msg')
            && !(getVal('msg') == 'Script error.' && getVal('line') == '0')
            && array_key_exists('HTTP_USER_AGENT', $_SERVER);
    }

    if (!isValidRequest()) {
        exit;
    }

    $data = array();
    array_push($data,
        time(),
        getVal('msg'),
        getVal('url'),
        getVal('line'),
        getVal('col'),
        getVal('href'),
        $_SERVER['HTTP_USER_AGENT']
    );

    $line = json_encode($data) . "\n";

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

* https://developer.mozilla.org/en-US/docs/Web/API/GlobalEventHandlers.onerror
* https://github.com/tlk/window.onerror-ignore for a list of odd exceptions
* https://github.com/errorception/ie-error-languages on how to interpret localized errors (MSIE)
* https://github.com/ryanseddon/sourcemap-onerror
* https://github.com/stacktracejs/stacktrace.js
* https://github.com/occ/TraceKit
* https://github.com/jefferyto/glitchjs
* https://github.com/protonet/simple-javascript-airbrake-notifier
* http://blog.protonet.info/post/9620971736/exception-notifier-javascript
* http://jserrlog.appspot.com
* http://blog.gospodarets.com/track_javascript_angularjs_and_jquery_errors_with_google_analytics/
* https://github.com/sap1ens/javascript-error-logging
* https://github.com/lucho-yankov/window.onerror
* https://github.com/lillesand/js-onerror
* https://github.com/steaks/exceptions.js


#### Commercial solutions

* https://opbeat.com
* https://papertrailapp.com
* http://www.muscula.com
* https://errorception.com
* https://bugsnag.com
* https://www.debuggify.net
* http://trackjs.com
* See https://github.com/cheeaun/javascript-error-logging for a collection of JavaScript error logging services

