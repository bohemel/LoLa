<?php

require_once 'inc/lib/post.php';

class PostHandler {

  function handle() {
    // index
    if (!arg(1))
      return $this->index();
    // create
    elseif (arg(1) === 'create' && !arg(2))
      return $this->create();
    // post page
    elseif (arg(1) === 'edit' && arg(2))
      return $this->edit();
    // share page
    elseif (arg(1) === 'share' && arg(2))
      return $this->share();
    elseif (is_numeric(arg(1)))
      return $this->post(arg(1));
    not_found();
  }

  function edit() {
    // never cache
    conf('cache', FALSE);
    if (!is_auth()) {
      redirect('/authenticate');
    }
    else {
      $post = new Post(arg(2));
      if (!$post->isLoaded())
        not_found(':(');
      if (!empty($_POST)) {
        $post->data['title'] = $_POST['title'];
        $post->data['content'] = $_POST['content'];
        $post->data['tags'] = array_map('trim', explode(',', trim($_POST['tags'], ', ')));
        $post->update();
        invalidate_cache('/');
        invalidate_cache($post->prettyUrl());
        redirect($post->prettyUrl());
      }
      else {
        return render('inc/templates/edit.inc', $post->data);
      }
    }
  }

  function share() {
    // never cache
    conf('cache', FALSE);
    if (!is_auth()) {
      redirect('/authenticate');
    }
    else {
      $post = new Post(arg(2));
      if (!$post->isLoaded())
        not_found(':(');
      return render('inc/templates/share.inc', array('id' => $post->id, 'content' => $post->generateTweet()));
    }
  }

  function create() {
    // never cache
    conf('cache', FALSE);
    if (!is_auth()) {
      redirect('/authenticate');
    }
    else {
      if (!empty($_POST)) {
        $data = array(
          'title' => $_POST['title'],
          'content' => $_POST['content'],
          'tags' => array_map('trim', explode(',', trim($_POST['tags'], ', '))),
        );
        $post = new Post($data);
        invalidate_cache('/');
        redirect($post->prettyUrl());
      }
      else
        return render('inc/templates/create_post.inc');
    }
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

  function render($data, $in_list = TRUE) {
    $data['in_list'] = $in_list;
    return render('inc/templates/post.inc', $data);
  }

  function post($id) {
    $post = new Post($id);
    if(!$post->isLoaded())
      not_found(':(');
    else {
      page_title($post->data['title']);
      $post->preprocess();
      return $this->render($post->data, FALSE);
    }
  }

}
