<div class="page-header">
  <h1>Edit post <?= $vars['id']; ?></h1>
</div>
<div class="row">
  <div class="span12">
    <form class="well" method="post" action="/post/edit/<?= $vars['id']; ?>">
      <label>Title</label>
      <input value="<?= $vars['title']; ?>" name="title" />
      <label>Content</label>
      <textarea style="width: 99%;" rows="20" name="content"><?= $vars['content']; ?></textarea>
      <label>Tags</label>
      <input name="tags" value="<?= implode(', ', $vars['tags']); ?>" />
      <div>
        <button type="submit" class="btn">Submit</button>
      </div>
    </form>
  </div>
</div>
