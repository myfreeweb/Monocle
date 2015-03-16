<div class="narrow">
  <?= partial('partials/header') ?>

  <?= partial('partials/channel-tabs', [
    'channels' => $this->channels,
    'active_channel' => $this->channel
  ]) ?>

  <?= partial('partials/add-feed-to-channel', [
    'channel' => $this->channel
  ]) ?>

  <? foreach($this->entries as $entry): ?>
    <?= partial('partials/entry', [
      'entry' => $entry
    ]) ?>
  <? endforeach; ?>

</div>
