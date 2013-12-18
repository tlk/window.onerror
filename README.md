window.onerror
====
#### Log client side exceptions on your server when they occur in production. Works in all browsers. Get a glimpse into what's going on when something fails in the browser.

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
    <script>window.onerror = function(m,u,l,c) { if (window.XMLHttpRequest) { var xhr = new XMLHttpRequest(); var msg = "msg="+encodeURIComponent(m)+"&url="+encodeURIComponent(u)+"&line="+l+"&col="+c+"&href="+encodeURIComponent(window.location.href); xhr.open("GET", "/window.onerror?"+msg, true); xhr.setRequestHeader("Content-Type", "text/plain;charset=UTF-8"); xhr.send(); } }; </script>
    ```

2. Make your server log the data that is posted to /window.onerror
3. Verify by adding a script error:
    ```javascript
    <script>
    alrt("foo");
    /script>
    ```


#### Examples

TBD


#### Inspired by

Allan Ebdrup, http://www.muscula.com


### License

Copyright 2014 Thomas L. Kjeldsen. Released under the [MIT License (MIT)](LICENSE).
