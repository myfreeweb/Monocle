<div class="narrow">

  <div class="jumbotron h-x-app">
    <h1><img src="/images/icons/monocle-icon-84.png" height="84" style="margin-bottom: 13px;" class="u-logo p-name" alt="Monocle"> Monocle</h1>

    <p class="tagline p-summary">Monocle is an IndieWeb-enabled reader.</p>

    <p>To use Monocle, sign in with your domain. If your website supports <a href="http://micropub.net/">Micropub</a>, you will be able to post replies from the reader.</p>

    <form action="/auth/start" method="get" class="form-inline">
      <input type="text" name="me" placeholder="yourdomain.com" value="" class="form-control">
      <input type="submit" value="Sign In" class="btn btn-primary">
    </form>

    <a href="<?= Config::$base_url ?>/" class="u-url"></a>
  </div>

</div>
