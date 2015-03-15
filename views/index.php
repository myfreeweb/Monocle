<div class="narrow">
  <?= partial('partials/header') ?>

  <div id="channels">
    <ul>
      <? foreach($this->channels as $channel): ?>
        <li><a href="/channel/<?= $channel['id'] ?>"><?= $channel['name'] ?></a></li>
      <? endforeach; ?>
    </ul>
  </div>

  <?= partial('partials/add-feed-to-channel', [
    'channel' => $this->channel
  ]) ?>

</div>
