<?php

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

  if (file_exists('inc/handlers/' . $handler . '_handler.class.php')) {
    require_once 'inc/handlers/' . $handler . '_handler.class.php';
    $handler_class = ucfirst($handler) . 'Handler';
    $handler = new $handler_class();
    $vars['content'] = $handler->handle();
    return render('inc/templates/page.inc', $vars, TRUE);
  }
  else
    not_found(':(');
}

run();

