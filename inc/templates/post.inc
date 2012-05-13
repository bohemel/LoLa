<div class="row" style="margin-bottom: 3em;">
  <article class="post span8" id="article-<?= $vars['id']; ?>">
    <div class="page-header">
      <?php
        $timestamp = intval($vars['created']);
        $vars['created'] = date('D, j M Y H:i', $timestamp);
        if ($vars['type'] === 'link')
          echo sprintf('<h1><a href="%s"><i class="icon-globe"></i> %s</a> <small>posted on <span class="postdate">%s</span></small></h1>', $vars['link'], $vars['title'], $vars['created']);
        elseif ($vars['type'] === 'image')
          echo sprintf('<h1><i class="icon-picture"></i> %s <small>posted on <span class="postdate">%s</span></small></h1>', $vars['title'], $vars['created']);
        else
          echo sprintf('<h1>%s <small>posted on <span class="postdate">%s</span></small></h1>', $vars['title'], $vars['created']);
      ?>
    </div>
    <?= $vars['content']; ?>
    <footer>
      Permalink: <a href="http://joelsoderberg.se<?= $vars['pretty_url']; ?>"><?= $vars['pretty_url']; ?></a>
      <?php if(!empty($vars['tags'])): ?>
        <br />Tags: 
        <?php foreach ($vars['tags'] as $tag): ?>
          <a href="/tag/<?= $tag; ?>"><span class="label"><?= $tag; ?></span></a>
        <?php endforeach; ?>
      <?php endif; ?>
    </footer>
  </article>
</div>
