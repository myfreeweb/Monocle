<div class="narrow">
  <?= partial('partials/header') ?>
  <h2>Channels</h2>

  <ul>
  <? foreach($this->channels as $channel): ?>
    <li>
      <a href="/channel/<?= $channel['id'] ?>/settings"><?= $channel['name'] ?></a> (<?= $channel['sources'] ?> sources)
    </li>
  <? endforeach; ?>
  </ul>

</div>