<div class="narrow">
  <?= partial('partials/header') ?>
  <h2><?= $this->channel->name ?></h2>

  <?= partial('partials/add-feed-to-channel', [
    'channel' => $this->channel
  ]) ?>

</div>
