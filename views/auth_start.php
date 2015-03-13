<div class="narrow">
  <?= partial('partials/header') ?>

  <?php if($this->authorizationEndpoint): ?>
    <div class="bs-callout bs-callout-warning">Monocle found your authorization endpoint, but you didn't specify a micropub endpoint.</div>
    <p>You'll still be able to read with Monocle, but won't be able to use it to reply or to "like" things from your site.</p>
  <?php else: ?>
    <div class="bs-callout bs-callout-warning">It looks like your site isn't pointing to an authorization server.</div>
    <p>You'll still be able to read with Monocle, but won't be able to use it to reply or to "like" things from your site.</p>
  <?php endif; ?>

  <p><a href="<?= $this->authorizationURL ?>" class="btn btn-primary">Sign In</a></p>

  <?php if($this->authorizationEndpoint): ?>
    <p>Clicking the link above will take you to the authorization server you specfied, <?= $this->authorizationEndpoint ?>.
  <?php endif; ?>

</div>