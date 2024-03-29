<?php

/**
 * @file
 * Main file for LoLa project.
 *
 * This file defines core functions and runs the starts the 'run' function that
 * serves as the applications entry point.
 *
 * All LoLa code is released under the New BSD License. See COPYRIGHT.txt.
 */

/**
 * Sets and gets configuration values.
 * 
 * @param $key
 *   Configuration key to set/retrieve.
 * 
 * @param $value
 *   Optional. If given, sets the configuration item given by $key.
 * 
 * @return
 *   The value for given configuration key or FALSE if the key not is set.
 */
function conf($key, $value = NULL) {
  static $conf = array();
  if (!is_null($value))
    $conf[$key] = $value;
  if (isset($conf[$key]))
    return $conf[$key];
  else
    return NULL;
}

/**
 * Get the session cookie name.
 */
function get_session_name() {
  return conf('session_name');
}

/**
 * Gets the session array.
 * 
 * @return
 *   A populated session array if session is active, otherwise an empty array.
 */
function get_session() {
  if (!file_exists(conf('data_dir') . '/session.dat'))
    return FALSE;
  $session = @unserialize(file_get_contents(conf('data_dir') . '/session.dat'));
  if (!$session)
    return array();
  return $session;
}

/**
 * Returns the active auth token from the session array.
 */
function active_auth_token() {
  $session = get_session();
  if (!empty($session) && !empty($session['token']))
    return $session['token'];
}

/**
 * Generates and returns an authentication token based on current time.
 */
function generate_auth_token() {
  return md5(microtime());
}

/**
 * Writes a given session array to disk and sets session cookie.
 */
function set_auth_session($data = array()) {
  $data['token'] = generate_auth_token();
  file_put_contents(conf('data_dir') . '/session.dat', serialize($data));
  setcookie(get_session_name(), $data['token']);
}

/**
 * Completly destroys the current session.
 */
function destroy_auth_session() {
  $session_name = get_session_name();
  if (file_exists(conf('data_dir') . '/session.dat'))
    unlink(conf('data_dir') . '/session.dat');
  setcookie($session_name, '', time() -3600);
}

/**
 * Returns TRUE if the current user is authenticated
 */
function is_auth() {
  return 
    (isset($_COOKIE[get_session_name()]) && 
    $_COOKIE[get_session_name()] === active_auth_token());
}

/**
 * Sets/gets page title.
 *
 * @param $new_title
 *   If given, sets the title of current page.
 * 
 * @return
 *   The current page title. 
 */
function page_title($new_title = '') {
  static $page_title = '';
  if (!empty($new_title))
    $page_title = $new_title;
  return $page_title;
}

/**
 * Sends http redirect header to browser and stops execution of script.
 *
 * @param string $url
 *   Url to redirect to.
 */
function redirect($url) {
  header('Location: '. $url);
  die();
}

/**
 * Renders a template.
 * 
 * Renders a given template (php) file with the given variable array. Includes
 * the php file and buffers the output. The content in the output buffer can
 * then be returned and echoed to browser.
 * 
 * @param string $file
 *   The file to be rendered
 *
 * @param array $vars
 *  Variables to be used in the template
 * 
 * @param bool $echo
 *   (optional) If TRUE the result of the render is echoed to browser.
 * 
 * @return string
 *   The result of the render.
 */
function render($file, $vars = array(), $echo = FALSE) {
  ob_start();
  require $file;
  $o = ob_get_clean();
  if($echo)
    print $o;
  return $o;
}
/**
 * Echoes error message and ends script execution
 *
 * This function can be called anywhere in the code when an error that 
 * prevents further execution of the system occurs.
 *
 * @param string $msg
 *   Message displayed echoed to the browser.
 */
function fatal_error($msg) {
  echo '<pre>';
  echo $msg;
  die();
}

/**
 * Renders a 404 file not found page and sets http headers accordingly
 */
function not_found($msg = '') {
  header('HTTP/1.0 404 Not Found');
  echo render('inc/templates/404.inc', array('message' => $msg));
  die();
}

/**
 * Finds a file of specified type.
 * 
 * @param string $name
 *   Base name of file
 *
 * @param string $type
 *   (optional) Type of file. If given must be one of: (handler, page, lib)
 *
 * @return
 *   Returns filename as a string if file is found. If not returns FALSE
 */
function find($name, $type = 'handler') {
  $filename = 'inc/';
  if ($type === 'handler')
    $filename .= 'handlers/' . $name . '_handler.class.php';
  elseif ($type === 'page')
    $filename .= 'pages/' . $name . '.inc';
  elseif ($type === 'lib')
    $filename .= 'lib/' . $name . '.php';
  else
    return FALSE;
  if (!file_exists($filename))
    return FALSE;
  return $filename;
}

/**
 * Include file of specified type.
 * 
 * @param string $name
 *   Base name of file
 *
 * @param string $type
 *   Not used
 *
 * @return bool
 *   Returns TRUE if included seccessfully.
 */
function inc($name, $type = 'handler') {
  if ($filename = find($name, $type)) {
    require_once $filename;
    return TRUE;
  }
  return FALSE;
}


/**
 * Return numblered arg from path.
 * 
 * Example: If path (relative to base path) is post/34. arg(0) returns 'post'
 * and arg(1) returns '34'
 * 
 * @param $id
 *   (optional) The index of the argsument requested.
 *
 * @return
 *   If $id is given argument att that index. If $id nog given, returns the 
 *   entire argument array.
 */
function arg($id = NULL) {
  if(!isset($_GET['q']))
    return NULL;
  $args = explode('/', trim($_GET['q'], '/'));
  if($id !== NULL && isset($args[$id]))
   return $args[$id];
  elseif($id !== NULL) return NULL;
  return $args;
}

/**
 * Returns the base path of the app.
 *
 * The base path is the path on the webserver where lola's index.php resides.
 *
 * Examples
 *  - example.com/lola/index.php -> returns /lola/
 *  - example.com/index.php -> returns /
 */
function base_path() {
  // Stole this from Drupal!
  static $dir = '';
  if(empty($dir)) 
    $dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/') . '/';
  return $dir;
}

/**
 * Returns a path relative to the app.
 */
function relative_path($path = '') {
  return base_path() . $path;
}

function get_cache_filename_from_path($path) {
  return conf('cache_dir') . '/static/' . conf('hostname') . '/' . $path . '_.html';
}

function write_cache($content, $path = '') {
  if (!$path)
    $path = $_GET['q'];
  $filename = get_cache_filename_from_path($path);
  $content = str_replace('<small class="created-by">Page generated.</small>', '<small class="created-by">Page from cache: ' . $filename . '</small>', $content);
  if (!is_dir(dirname($filename)))
    mkdir(dirname($filename), 0755, TRUE);
  file_put_contents($filename, $content);
}

function invalidate_cache($path = '') {
  if (!$path)
    $path = $_GET['q'];
  $filename = get_cache_filename_from_path($path);
  unlink($filename);
}

/**
 * Main function and app entry point
 *
 * This function holds the routing functionality of LoLa. Not exactly rocket
 * science. Just checks the first argument and either loads the rss class, a
 * handler class or a static page. Calls the not_found() function as if all 
 * else fails.
 *
 * If handler or page is found it sends the result into the render function
 * with the main page template.
 */
function run() {

  mb_internal_encoding('UTF-8');
  require_once 'conf.inc';

  $handler = 'post';
  if (arg(0) && arg(0) === 'rss.xml') {
    inc('rss', 'lib');
    new RssFeed();
    return;
  }
  elseif (arg(0))
    $handler = arg(0);

  if (inc($handler)) {
    $handler_class = ucfirst($handler) . 'Handler';
    $handler = new $handler_class();
    $vars['content'] = $handler->handle();
  }
  elseif ($page_file = find(arg(0), 'page'))
    $vars['content'] = render($page_file);

  if (!empty($vars)) {
    $vars['page_title'] = page_title();
    $content = render('inc/templates/page.inc', $vars, TRUE);
    if (conf('cache'))
      write_cache($content);
  }
  else
    not_found(':(');
}

// RUN RUN RUN
run();

