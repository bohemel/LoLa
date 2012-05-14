<?php

/**
 * @file
 * Main file for LoLa project.
 *
 * This file defines core functions and runs the starts the 'run' function that
 * serves as the applications entry point.
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
 *   The value for given configuration keÃ or FALSE if the key not is set.
 */
function conf($key, $value = FALSE) {
  static $conf = array();
  if ($value)
    $conf[$key] = $value;
  if (isset($conf[$key]))
    return $conf[$key];
  else
    return FALSE;
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
  return (isset($_COOKIE[get_session_name()]) && $_COOKIE[get_session_name()] === active_auth_token());
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
 *   Message displayed echoed to teh browser.
 */
function fatal_error($msg) {
  echo '<pre>';
  echo $msg;
  die();
}

function not_found($msg = '') {
  header('HTTP/1.0 404 Not Found');
  echo render('inc/templates/404.inc', array('message' => $msg));
  die();
}

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

function inc($name, $type = 'handler') {
  if ($filename = find($name, $type)) {
    require_once $filename;
    return TRUE;
  }
  return FALSE;
}

function arg($id = NULL) {
  if(!isset($_GET['q']))
    return NULL;
  $args = explode('/', trim($_GET['q'], '/'));
  if($id !== NULL && isset($args[$id]))
   return $args[$id];
  elseif($id !== NULL) return NULL;
  return $args;
}

function base_path() {
  // Stole this from Drupal!
  static $dir = '';
  if(empty($dir)) 
    $dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/') . '/';
  return $dir;
}

function relative_path($path = '') {
  return base_path() . $path;
}

function run() {
  
  base_path();

  require_once 'conf.inc';

  $handler = 'post';
  if(arg(0) && arg(0) === 'rss.xml') {
    inc('rss', 'lib');
    new RssFeed();
    return;
  }
  elseif(arg(0))
    $handler = arg(0);

  if (inc($handler)) {
    $handler_class = ucfirst($handler) . 'Handler';
    $handler = new $handler_class();
    $vars['content'] = $handler->handle();
  }
  elseif ($page_file = find(arg(0), 'page'))
    $vars['content'] = render($page_file);

  if(!empty($vars)) {
    $vars['page_title'] = page_title();
    render('inc/templates/page.inc', $vars, TRUE);
  }
  else
    not_found(':(');
}

run();

