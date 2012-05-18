<?php

require_once 'inc/lib/libtwitter/twitter.class.php';

class Tweeter {
    
  private $connection;
  
  function __construct() {
    $this->connection = new LibTwitter(
      conf('twitter_consumer_key'), 
      conf('twitter_consumer_secret'),
      conf('twitter_access_token'),
      conf('twitter_access_token_secret'));
  }
  
  function tweet($status) {
    $res = $this->connection->send($status);
    var_dump($res); die();
  }
}

?>