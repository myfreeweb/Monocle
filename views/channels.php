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

  <input type="text" class="form-control" id="new-channel" placeholder="Add new channel">

</div>
<script>
$(function(){
  $("#new-channel").bind('keydown', function(e){
    if(e.keyCode == 13) {
      $.post('/channels/new', {
        name: $("#new-channel").val()
      }, function(response) {
        window.location = window.location;
      });
    }
  });
});
</script>