<div style="margin:10px 0;" id="subscribe_box">
  <input type="text" class="form-control" id="subscribe_url" placeholder="Subscribe to a URL" value="http://tantek.com">

  <div id="feeds_discovered" style="display: none;">
    <h3>Feeds Found:</h3>
    <div class="loading"><img src="/images/spinner.gif" width="54" height="55"></div>
    <ul></ul>
  </div>
</div>

<script type="text/javascript">
$(function(){
  $("#subscribe_url").keydown(function(e){
    if(e.keyCode == 13) {
      $("#feeds_discovered").show();
      $("#feeds_discovered .loading").show();

      $("#feeds_discovered ul").html('');

      $.post('/channels/discover', {
        url: $("#subscribe_url").val()
      }, function(response) {
        $("#feeds_discovered .loading").hide();
        $("#subscribe_btn").addClass("btn-success");
        $(response.feeds).each(function(i,feed){
          var filter = '<span><button class="btn btn-default filter-btn" data-id="'+i+'"><i class="fa fa-search"></i></button></span>';
          if(feed.enabled == false) { 
            filter = '';
          }
          $("#feeds_discovered ul").append('<li><div class="form-inline"><button data-id="'+i+'" class="btn btn-success subscribe'+(feed.enabled ? '' : ' disabled')+'" data-url="'+feed.url+'">'+feed.icon+' '+feed.display_url+'</button> '+filter+'</div></li>');
        });
        bind_subscribe_buttons();
      });
    }
  });
});

function bind_subscribe_buttons() {
  $("#feeds_discovered li .subscribe").click(function(){
    var index = $(this).data('id');
    $.post('/channels/add_feed', {
      url: $(this).data('url'),
      filter: $(".filter[data-id="+index+"]").val(),
      channel_id: <?= $this->channel->id ?>
    }, function() {
      window.location = window.location;
    });
  });
  $("#feeds_discovered li .filter-btn").click(function(){
    $(this).parents("span").html('<input type="text" data-id="'+$(this).data('id')+'" class="form-control filter" placeholder="comma-separated terms" />');
  });
}
</script>
