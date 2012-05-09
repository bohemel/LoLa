<div class="row" style="margin-bottom: 3em;">
  <article class="span8" id="article-<?= $vars['id']; ?>">
    <div class="page-header">
      <?php if ($vars['title_as_link']): ?>
        <h1><a href="<?= $vars['pretty_url']; ?>"><?= $vars['title']; ?></a> <small>posted on <?= $vars['created']; ?></small></h1>
      <?php else: ?>
        <h1><?= $vars['title']; ?> <small>posted on <?= $vars['created']; ?></small></h1>
      <?php endif; ?>
    </div>
    <?= $vars['content']; ?>
    <?php if(!empty($vars['tags'])): ?>
      <div style="color: #999; ">Tags: 
        <?php foreach ($vars['tags'] as $tag): ?>
          <a href="/tag/<?= $tag; ?>"><span class="label"><?= $tag; ?></span></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </article>
</div>
