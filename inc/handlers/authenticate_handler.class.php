<?php

class AuthenticateHandler {

  function handle() {

    // disable all cache for this handler
    conf('cache', FALSE);

    if (is_auth()) {
      if(isset($_GET['logout'])) {
        destroy_auth_session();
        redirect('/');
      }
      return render('inc/templates/auth/auth.inc', array('session' => get_session()));
    }
    require_once 'inc/lib/openid.php';
    try {
      $openid = new LightOpenID('joelsoderberg.se');
      if (!$openid->mode) {
        if(isset($_GET['login'])) {
          $openid->identity = 'https://www.google.com/accounts/o8/id';
          $openid->required = array(
            'namePerson',
            'namePerson/first',
            'namePerson/last',
            'contact/email',
          );
          header('Location: ' . $openid->authUrl());
        }
        return render('inc/templates/auth/login.inc');
      } 
      elseif ($openid->mode == 'cancel') {
        fatal_error('User has canceled authentication!');
      } 
      elseif ($openid->validate()) {
        if ($openid->identity === $openid->identity) {
          $attr = $openid->getAttributes();
          set_auth_session($attr);
          redirect('/authenticate');
        }
        else
          return '<p>No access for this identity! <a href="/authenticate">Try again</a>.</p>';
      }
      else 
        return '<p>Invalid! <a href="/authenticate">Try again</a>.</p>';
    } 
    catch(ErrorException $e) {
      return $e->getMessage();
    }
  }
}

