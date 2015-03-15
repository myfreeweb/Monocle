<div class="narrow">
  <?= partial('partials/header') ?>
  <h2><?= $this->channel->name ?></h2>

  <?= partial('partials/channel', [
    'channel' => $this->channel
  ]) ?>

</div>
