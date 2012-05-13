<?php

define('DATA_DIR', 'data');
define('CACHE_DIR', 'cache');
define('VALID_OPENID', 'openid_here');
define('C_SESSION_NAME', 'SIDKBD989BKJV08KUSFDL087KJSFKSKJC987IG');

function get_session_name() {
  return C_SESSION_NAME;
}

function get_session() {
  if (!file_exists(DATA_DIR . '/session.dat'))
    return FALSE;
  $session = @unserialize(file_get_contents(DATA_DIR . '/session.dat'));
  if (!$session)
    return array();
  return $session;
}

function active_auth_token() {
  $session = get_session();
  if (!empty($session) && !empty($session['token']))
    return $session['token'];
}

function generate_auth_token() {
  return md5(microtime());
}

function set_auth_session($data = array()) {
  $data['token'] = generate_auth_token();
  file_put_contents(DATA_DIR . '/session.dat', serialize($data));
  setcookie(get_session_name(), $data['token']);
}

function destroy_auth_session() {
  $session_name = get_session_name();
  if (file_exists(DATA_DIR . '/session.dat'))
    unlink(DATA_DIR . '/session.dat');
  setcookie($session_name, '', time() -3600);
}

function is_auth() {
  return (isset($_COOKIE[get_session_name()]) && $_COOKIE[get_session_name()] === active_auth_token());
}

function page_title($new_title = '') {
  static $page_title = '';
  if (!empty($new_title))
    $page_title = $new_title;
  return $page_title;
}

function redirect($url) {
  header('Location: '. $url);
  die();
}

function render($file, $vars = array(), $echo = FALSE) {
  ob_start();
  require $file;
  $o = ob_get_clean();
  if($echo)
    print $o;
  return $o;
}

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

function run() {


  $handler = 'post';
  if(arg(0))
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

