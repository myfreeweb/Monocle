<div class="narrow">

  <?= partial('partials/header') ?>

  <h2>Subscriptions</h2>

  <div id="subscribe_box">
    <p>Subscribe to a URL:</p>

    <input type="button" class="btn btn-success" id="subscribe_btn" value="Add">
    <input type="text" class="form-control" id="subscribe_url" value="http://pk.dev">
    <div style="clear:both;"></div>

    <div id="feeds_discovered" style="display: none;">
      <h3>Feeds Discovered:</h3>
      <div class="loading"><img src="/images/spinner.gif" width="54" height="55"></div>
      <ul></ul>
    </div>
  </div>
  


</div>
<script type="text/javascript">
$(function(){
  $("#subscribe_btn").click(function(){
    $("#subscribe_btn").removeClass("btn-success");
    $("#feeds_discovered").show();

    $.post('/subscriptions/discover', {
      url: $("#subscribe_url").val()
    }, function(response) {
      $("#feeds_discovered .loading").hide();
      $("#subscribe_btn").addClass("btn-success");
      $(response.feeds).each(function(i,feed){
        $("#feeds_discovered ul").append('<li><input type="button" class="btn btn-success" data-url="'+feed.url+'" value="'+feed.display_url+'"> ('+feed.type+')</li>');
      });
      bind_subscribe_buttons();
    });
  });
});

function bind_subscribe_buttons() {
  $("#feeds_discovered li input").click(function(){
    $.post('/subscriptions/add', {
      url: $(this).data('url')
    }, function() {
      window.location = window.location;
    });
  });
}
</script>