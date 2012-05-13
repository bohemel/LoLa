<?php

require_once 'inc/lib/crud.class.php';

class Post extends Crud {

  protected static $entity = 'post';
  protected static $class_name = 'Post';

  function categorize($link) {
    $ext = array_pop(explode('.', $link));
    $parts = parse_url($link);
    if (in_array(strtolower($ext), array('jpeg', 'jpg', 'gif', 'png')))
      return 'image';
    if ($parts['host'] === 'www.youtube.com')
      return 'youtube';
    if ($parts['host'] === 'instagr.am')
      return 'instagram';
    return 'link';
  }

  function preprocessImage($lines, $link) {
    $this->data['type'] = 'image';
    array_unshift($lines, '<img alt="' . $this->data['title'] . '" src="' . $link . '" />' . "\n\nOriginal on: <a href=\"" . $link . '">'.$link.'</a>');
    $this->data['tags'][] = 'pictures';
    return $lines;
  }

  function preprocessInstagram($lines, $link) {
    $this->data['type'] = 'image';
    array_unshift($lines, '<img alt="' . $this->data['title'] . '" src="' . rtrim($link, '/') . '/media?size=l" />' . "\n\nVia Instagram: <a href=\"" . $link . '">'.$link.'</a>');
    $this->data['tags'][] = 'pictures';
    return $lines;
  }

  function preprocessLink($lines, $link) {
    $this->data['type'] = 'link';
    $this->data['link'] = $link;
    array_push($lines, sprintf("\n\n" . 'Original url: <a href="%s">%s</a>' . "\n\n", $link, $link)); 
    $this->data['tags'][] = 'links';
    return $lines;
  }

  function preprocessYoutube($lines, $link) {
    $parts = parse_url($link);
    parse_str($parts['query'], $args);
    $youtube_markup = sprintf('<iframe width="420" height="315" src="http://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>', $args['v']);
    array_unshift($lines, 'Via youtube: <a href="' . $link . '">Direct link</a>');
    array_unshift($lines, $youtube_markup);
    $this->data['type'] = 'video';
    $this->data['tags'][] = 'video';
    return $lines;
  }

  function preprocess() {
    if(!isset($this->data['tags']))
      $tags = array();
    else
      $tags = $this->data['tags'];
    $this->data['tags'] = array();
    foreach($tags as $tag)
      if(!empty($tag)) $this->data['tags'][] = trim($tag);
    $this->data['type'] = 'text';
    $lines = explode("\n", trim($this->data['content']));
    $first_line = array_shift($lines);
    if($first_line && $link = filter_var(trim($first_line), FILTER_VALIDATE_URL)) {
      $cat = $this->categorize($link);
      $processor_function = 'preprocess' . ucfirst($cat);
      $lines = $this->$processor_function($lines, $link);
      $this->data['content'] = implode("\n", $lines);
    }
    require_once 'inc/lib/markdown.php';
    $this->data['content'] = Markdown($this->data['content']);
    $this->data['pretty_url'] = $this->prettyUrl();
  }

  function prettyUrl() {
    return '/post/' . $this->id . '/'
      . preg_replace('/[^a-z2-9\-]/', '', strtolower(str_replace(' ', '-', $this->data['title'])));
  }
}

