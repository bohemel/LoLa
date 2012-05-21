<div class="page-header">
  <h1>Share post <?= $vars['id']; ?></h1>
</div>
<div class="row">
  <div class="span12">
    <form class="well" method="post" action="/post/share/<?= $vars['id']; ?>">
      <label>Content</label>
      <textarea style="width: 99%;" rows="20" name="content"><?= $vars['content']; ?></textarea>
      <div>
        <button type="submit" class="btn">Tweet</button>
      </div>
    </form>
  </div>
</div>
