<div class="entry-context"<?= $this->entry->in_reply_to_url ? '' : ' style="display:none;"' ?>>
  In reply to <a href="<?= $this->entry->in_reply_to_url ?>"><?= friendly_url($this->entry->in_reply_to_url) ?></a>
</div>
<div class="entry<?= $this->entry->in_reply_to_url ? ' has-context' : '' ?>">
  <div class="author">
    <div class="photo">
      <img src="<?= $this->entry->author_photo ?>" width="48">
    </div>
    <a class="url" href="<?= $this->entry->author_url ?>"><?= friendly_url($this->entry->author_url) ?></a>
    <div class="name"><?= $this->entry->author_name ?></div>
  </div>
  <div class="clear"></div>

  <?php if($this->entry->name): ?>
    <h2 class="name"><?= $this->entry->name ?></h2>
  <?php endif; ?>

  <?php /* 
  TODO: better checking of whether the image exists in the post 
    * check for src="..." src='...' and src=...
    * check for images inside object tags
  */ ?>
  <?php if($this->entry->photo_url && strpos($this->entry->content, $this->entry->photo_url) === false): ?>
    <img class="photo" src="<?= $this->entry->photo_url ?>">
  <?php endif; ?>

  <?php if($this->entry->summary): ?>
    <div class="summary"><?= $this->entry->summary ?></div>
  <?php endif; ?>

  <?php if($this->entry->content): ?>
    <div class="content"><?= $this->entry->content ?></div>
  <?php endif; ?>

  <?php /* TODO: better checking of whether the audio exists in the post */ ?>
  <?php if($this->entry->audio_url && strpos($this->entry->content, $this->entry->audio_url) === false): ?>
    <div class="audio">
      <audio src="<?= $this->entry->audio_url ?>" controls="controls" style="width: 100%"></audio>
    </div>
  <?php endif; ?>

  <?php /* TODO: better checking of whether the video exists in the post */ ?>
  <?php if($this->entry->video_url && strpos($this->entry->content, $this->entry->video_url) === false): ?>
    <div class="video">
      <video controls="controls" style="width: 100%"><source src="<?= $this->entry->video_url ?>" type="video/mp4"></video>
    </div>
  <?php endif; ?>

  <div class="meta">
    <ul>
      <li><a href="<?= $this->entry->url ?>"><?= friendly_date($this->entry->date_published, $this->entry->timezone_offset) ?></a></li>
      <?php 
        foreach(['comment','like','repost','rsvp'] as $type) {
          if($this->entry->{"num_".$type."s"}) {
            echo '<li>' . response_icon($type) . ' ' . $this->entry->{"num_".$type."s"} . ' ' . pluralize($type, $this->entry->{"num_".$type."s"}) . '</li>';
          }
        }
      ?>
    </ul>
  </div>
</div>
<div class="entry-actions" id="entry-actions-<?= $this->entry->id ?>">

  <div class="syndications" style="float: right;">
    <?php foreach($this->syndications as $s): ?>
      <?= partial('partials/syndication_url', ['syndication' => $s]) ?>
    <?php endforeach; ?>
  </div>

  <div class="button-row">
    <button class="action action-like" title="Like this post" data-action="like" data-url="<?= $this->entry->url ?>" data-id="<?= $this->entry->id ?>"><i class="fa fa-star"></i></button>
    <button class="action action-repost" title="Repost this" data-action="repost" data-url="<?= $this->entry->url ?>" data-id="<?= $this->entry->id ?>"><i class="fa fa-retweet"></i></button>
    <button class="action action-bookmark" title="Create a bookmark" data-action="bookmark" data-url="<?= $this->entry->url ?>" data-id="<?= $this->entry->id ?>"><i class="fa fa-bookmark"></i></button>
    <button class="action action-reply" title="Write a reply" data-action="reply" data-url="<?= $this->entry->url ?>" data-id="<?= $this->entry->id ?>"><i class="fa fa-reply"></i></button>
  </div>
  <div class="result" style="display: none;">
    <div class="summary"></div>
    <div class="raw" style="display: none;">
      <b>Error!</b>
      <p>Raw response from your site:</p>
      <pre></pre>
    </div>
  </div>

</div>
