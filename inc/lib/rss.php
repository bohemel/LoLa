<?php

require_once 'inc/lib/post.class.php';

class RssFeed  {

  function __construct() {
    $this->header();
    $posts = Crud::getPage('Post');
    foreach ($posts as $post) {
      $this->item($post);
    }
    $this->footer();
  }

  private function item($post) {
    $post->preprocess();
    echo '<item>';
    echo sprintf('<title>%s</title>', htmlspecialchars($post->data['title']));
    echo sprintf('<link>http://joelsoderberg.se%s</link>', $post->prettyUrl());
    echo sprintf('<description><![CDATA[%s]]></description>', $post->data['content']);
    echo sprintf('<pubDate>%s</pubDate>', date('r', $post->data['created']));
    echo '<dc:creator>Joel Soderberg</dc:creator>';
    echo '<guid isPermaLink="false">' . $post->id . ' at http://joelsoderberg.se</guid>';
    echo '</item>';
  }

  private function header() {
    header('Content-Type: application/rss+xml');
    echo '<?xml version="1.0" encoding="utf-8"?>';
    echo '<rss version="2.0" xml:base="http://joelsoderberg.se" xmlns:atom="http://www.w3.org/2005/Atom"  xmlns:dc="http://purl.org/dc/elements/1.1/">';
    echo '<channel>';
    echo '<atom:link href="http://joelsoderberg.se/rss.xml" rel="self" type="application/rss+xml" />';
    echo '<title>joelsoderberg.se, control your own computing!</title>';
    echo '<link>http://joelsoderberg.se</link>';
    echo '<description></description>';
    echo '<language>en</language>';
  }

  private function footer() {
    echo '</channel>';
    echo '</rss>';
  }
}
