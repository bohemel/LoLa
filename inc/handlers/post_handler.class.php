<?php

require_once 'inc/lib/post.class.php';

class PostHandler {

  function handle() {
    // index
    if (!arg(1))
      return $this->index();
    // create
    elseif (arg(1) === 'create' && !arg(2))
      return $this->showCreate();
    // post page
    elseif (is_numeric(arg(1)))
      return $this->post(arg(1));
  }

  function showCreate() {
    if (!empty($_POST)) {
      $data = array(
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'tags' => array_map('trim', explode(',', trim($_POST['tags'], ', '))),
      );
      $post = new Post($data);
      redirect($post->prettyUrl());
    }
    else
      return render('inc/templates/create_post.inc');
  }

  function index() {
    $posts = Crud::getPage('Post', 0);
    $output = '';
    foreach($posts as $post) {
      $post->preprocess();
      $output .= $this->render($post->data);
    }
    return $output;
  }

  function render($data, $title_as_link = TRUE) {
    $data['title_as_link'] = $title_as_link;
    return render('inc/templates/post.inc', $data);
  }

  function post($id) {
    $post = new Post($id);
    if(!$post->isLoaded())
      not_found(':(');
    else {
      $post->preprocess();
      return $this->render($post->data, FALSE);
    }
  }

}
