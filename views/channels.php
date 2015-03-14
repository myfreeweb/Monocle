<div class="narrow">
  <?= partial('partials/header') ?>
  <h2>Channels</h2>

  <ul>
  <? foreach($this->channels as $channel): ?>
    <li>
      <h4><?= $channel['name'] ?></h4>
      <ul>
        <? foreach($channel['sources'] as $source): ?>
          <li><?= $source['id'] ?></li>
        <? endforeach; ?>
      </ul>
    </li>
  <? endforeach; ?>
  </ul>

</div>