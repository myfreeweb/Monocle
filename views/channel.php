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
      'entry' => $entry,
      'syndications' => ORM::for_table('entry_syndications')->where('entry_id', $entry->id)->find_many()
    ]) ?>
  <? endforeach; ?>

</div>
<script>
$(function(){
  $(".entry-actions .action-like, .entry-actions .action-repost").click(function(){

    var btn = $(this);
    //btn.children('.fa').addClass('fa-spin');
    btn.children('.fa').addClass('loading');

    $.post('/micropub/'+$(this).data('action'), {
      url: $(this).data('url')
    }, function(response){

    $("#entry-actions-"+btn.data('id')+' .result').show();

      btn.children('.fa').removeClass('fa-spin');

      if(response.status != 201) {
        $("#entry-actions-"+btn.data('id')+' .raw pre').text(response.response);
        $("#entry-actions-"+btn.data('id')+' .raw').show();
      } else {
        btn.addClass('active');
        $("#entry-actions-"+btn.data('id')+' .raw').hide();
        $("#entry-actions-"+btn.data('id')+' .summary').html('<a href="'+response.location+'">Success!</a>');
      }

    });

  });
});
</script>