<div class="narrow">
  <?= partial('partials/header') ?>

  <div id="channels">
    <ul>
      <? foreach($this->channels as $channel): ?>
        <li><a href="/channel/<?= $channel['id'] ?>"><?= $channel['name'] ?></a></li>
      <? endforeach; ?>
    </ul>
  </div>


  <div style="margin:10px 0;" id="subscribe_box">
    <input type="text" class="form-control" id="subscribe_url" placeholder="Subscribe to a URL" value="http://transportini.com">

    <div id="feeds_discovered" style="display: none;">
      <h3>Feeds Discovered:</h3>
      <div class="loading"><img src="/images/spinner.gif" width="54" height="55"></div>
      <ul></ul>
    </div>
  </div>


<?php
foreach($this->entries as $entry) {
  echo partial('partials/entry-in-list', [
    'post_id' => $entry->id,
    'author_name' => $entry->author_name,
    'author_url' => $entry->author_url,
    'author_photo' => $entry->author_photo,
    'url' => $entry->url,
    'published' => $entry->published,
    'content' => $entry->content,
  ]);
}

?>
</div>
<script type="text/javascript">
$(function(){
  $("#subscribe_url").keydown(function(e){
    if(e.keyCode == 13) {
      // $("#subscribe_btn").removeClass("btn-success");
      $("#feeds_discovered").show();

      $("#feeds_discovered ul").html('');

      $.post('/channels/discover', {
        url: $("#subscribe_url").val()
      }, function(response) {
        console.log(response);
        $("#feeds_discovered .loading").hide();
        $("#subscribe_btn").addClass("btn-success");
        $(response.feeds).each(function(i,feed){
          $("#feeds_discovered ul").append('<li><input type="button" class="btn btn-success" data-url="'+feed.url+'" value="'+feed.display_url+'"> ('+feed.type+')</li>');
        });
        bind_subscribe_buttons();
      });
    }
  });
});

function bind_subscribe_buttons() {
  $("#feeds_discovered li input").click(function(){
    $.post('/channels/add', {
      url: $(this).data('url')
    }, function() {
      window.location = window.location;
    });
  });
}
</script>