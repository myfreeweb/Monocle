<div class="narrow">
  <?php if($this->entry): ?>
    <?= partial('partials/entry', [
      'entry' => $this->entry,
      'syndications' => ORM::for_table('entry_syndications')->where('entry_id', $this->entry->id)->find_many()
    ]) ?>
  <?php else: ?>
    <p>Currently the preview tool can only preview entries that have already been downloaded.</p>
  <?php endif; ?>
</div>