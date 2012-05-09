<?php

require_once 'inc/lib/crud.class.php';

class Post extends Crud {

  protected static $entity = 'post';
  protected static $class_name = 'Post';

  function preprocess() {

    if(!isset($this->data['tags']))
      $tags = array();
    else
      $tags = $this->data['tags'];

    $this->data['tags'] = array();

    foreach($tags as $tag)
      if(!empty($tag)) $this->data['tags'][] = trim($tag);

    require_once 'inc/lib/markdown.php';
    $this->data['content'] = Markdown($this->data['content']);
    $this->data['pretty_url'] = $this->prettyUrl();
  }

  function prettyUrl() {
    return '/post/' . $this->id . '/'
      . preg_replace('/[^a-z2-9\-]/', '', strtolower(str_replace(' ', '-', $this->data['title'])));
  }

}
