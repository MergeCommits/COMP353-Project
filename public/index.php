<?php
include(__DIR__ . '/../src/Router.php');

function navi() {
    echo <<<EOD
  Navigation:
  <ul>
      <li><a href="/">home</a></li>
      <li><a href="/index.php">index.php</a></li>
      <li><a href="/user/3/edit">edit user 3</a></li>
      <li><a href="/foo/5/bar">foo 5 bar</a></li>
      <li><a href="/foo/bar/foo/bar">long route example</a></li>
      <li><a href="/contact-form">contact form</a></li>
      <li><a href="/get-post-sample">get+post example</a></li>
      <li><a href="/test.html">test.html</a></li>
      <li><a href="/phpinfo">PHP Info</a></li>
      <li><a href="/aTrailingSlashDoesNotMatter">aTrailingSlashDoesNotMatter</a></li>
      <li><a href="/aTrailingSlashDoesNotMatter/">aTrailingSlashDoesNotMatter/</a></li>
      <li><a href="/theCaseDoesNotMatter">theCaseDoesNotMatter</a></li>
      <li><a href="/thecasedoesnotmatter">thecasedoesnotmatter</a></li>
      <li><a href="/this-route-is-not-defined">404 Test</a></li>
      <li><a href="/this-route-is-defined">405 Test</a></li>
  </ul>
EOD;
}

// Add base route (startpage)
Router::add('/', function() {
    navi();
    echo 'Welcome :-)';
});

// Another base route example
Router::add('/index.php', function() {
    navi();
    echo 'You are not really on index.php ;-)';
});

// Simple test route that simulates static html file
// TODO: Fix this for some web servers
Router::add('/test.html', function() {
    navi();
    echo 'Hello from test.html';
});

// This route is for debugging only
// It simply prints out some php infos
// Do not use this route on production systems!
Router::add('/phpinfo', function() {
    navi();
    phpinfo();
});

// Post route example
Router::add('/contact-form', function() {
    navi();
    echo '<form method="post"><input type="text" name="test"><input type="submit" value="send"></form>';
}, 'get');

// Post route example
Router::add('/contact-form', function() {
    navi();
    echo 'Hey! The form has been sent:<br>';
    print_r($_POST);
}, 'post');

// Get and Post route example
Router::add('/get-post-sample', function() {
    navi();
    echo 'You can GET this page and also POST this form back to it';
    echo '<form method="post"><input type="text" name="input"><input type="submit" value="send"></form>';
    if (isset($_POST['input'])) {
        echo 'I also received a POST with this data:<br>';
        print_r($_POST);
    }
}, ['get','post']);

// Route with regexp parameter
// Be aware that (.*) will match / (slash) too. For example: /user/foo/bar/edit
// Also users could inject SQL statements or other untrusted data if you use (.*)
// You should better use a saver expression like /user/([0-9]*)/edit or /user/([A-Za-z]*)/edit
Router::add('/user/(.*)/edit', function($id) {
    navi();
    echo 'Edit user with id '.$id.'<br>';
});

// Accept only numbers as parameter. Other characters will result in a 404 error
Router::add('/foo/([0-9]*)/bar', function($var1) {
    navi();
    echo $var1.' is a great number!';
});

// Crazy route with parameters
Router::add('/(.*)/(.*)/(.*)/(.*)', function($var1,$var2,$var3,$var4) {
    navi();
    echo 'This is the first match: '.$var1.' / '.$var2.' / '.$var3.' / '.$var4.'<br>';
});

// Long route example
// By default this route gets never triggered because the route before matches too
Router::add('/foo/bar/foo/bar', function() {
    echo 'This is the second match (This route should only work in multi match mode) <br>';
});

// Trailing slash example
Router::add('/aTrailingSlashDoesNotMatter', function() {
    navi();
    echo 'a trailing slash does not matter<br>';
});

// Case example
Router::add('/theCaseDoesNotMatter',function() {
    navi();
    echo 'the case does not matter<br>';
});

// 405 test
Router::add('/this-route-is-defined', function() {
    navi();
    echo 'You need to patch this route to see this content';
}, 'patch');

// Add a 404 not found route
Router::pathNotFound(function($path) {
    // Do not forget to send a status header back to the client
    // The router will not send any headers by default
    // So you will have the full flexibility to handle this case
    header('HTTP/1.0 404 Not Found');
    navi();
    echo 'Error 404 :-(<br>';
    echo 'The requested path "'.$path.'" was not found!';
});

// Add a 405 method not allowed route
Router::methodNotAllowed(function($path, $method) {
    // Do not forget to send a status header back to the client
    // The router will not send any headers by default
    // So you will have the full flexibility to handle this case
    header('HTTP/1.0 405 Method Not Allowed');
    navi();
    echo 'Error 405 :-(<br>';
    echo 'The requested path "'.$path.'" exists. But the request method "'.$method.'" is not allowed on this path!';
});

// Run the Router with the given Basepath
// If your script lives in the web root folder use a / or leave it empty
Router::run('/');

// If your script lives in a subfolder you can use the following example
// Do not forget to edit the basepath in .htaccess if you are on apache
// Router::run('/api/v1');

// Enable case sensitive mode, trailing slashes and multi match mode by setting the params to true
// Router::run('/', true, true, true);

?>
<link rel="stylesheet" type="text/css" href="style.css">
