<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <title><?php if (!empty($vars['page_title'])) echo $vars['page_title'] . ' | '; ?>joelsoderberg.se, control your own computing!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A blog about tech and random geekieness">
    <meta name="author" content="Joel Söderberg">
    
    <link href="<?= relative_path('assets/css/all.css'); ?>" rel="stylesheet">
    
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="shortcut icon" href="<?= relative_path('assets/img/favicon.png'); ?>" type="image/png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?= relative_path('assets/img/ios-144.png'); ?>">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= relative_path('assets/img/ios-72.png'); ?>">
    <link rel="apple-touch-icon-precomposed" href="<?= relative_path('assets/img/ios-57.png'); ?>">

    <link rel="alternate" type="application/rss+xml" title="joelsoderberg.se Feed" href="http://joelsoderberg.se/rss.xml">

    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-31881781-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>

  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?= base_path(); ?>">joelsoderberg.se</a>
          <ul style="display: none;" id="user-menu" class="nav pull-right">
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                Admin
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="/post/create">Create Post</a></li>
                <?php if(arg(0) === 'post' && is_numeric(arg(1))): ?>
                <li><a href="/post/edit/<?= arg(1); ?>">Edit Post</a></li>
                <li><a href="/post/share/<?= arg(1); ?>">Share Post</a></li>
                <?php endif; ?>
                <li class="divider"></li>
                <li><a href="/authenticate">Account</a></li>
                <li><a href="/authenticate?logout">Sign Out</a></li>
              </ul>
            </li>
          </ul> 
          <div class="nav-collapse">
            <ul class="nav">
              <li class="<?php if (!arg(0) || arg(0) === 'post') echo 'active'; ?>"><a href="<?= base_path(); ?>">Blog</a></li>
              <li class="<?php if (arg(0) === 'about') echo 'active'; ?>"><a href="<?= relative_path('about'); ?>">About</a></li>
              <li><a href="<?= relative_path('contact'); ?>">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
      
      <?php if(!arg(0) || (arg(0) === 'posts' && !arg(1))): ?>
      <div class="row">
        <div class="span12">
          <div class="hero-unit">
            <h1>Hello, world!</h1>
            <p>A kind of sort of bloggy pagey thing! I made it myself! (Too many exclamation marks already!) </p>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if(!empty($vars['debug'])): ?>
      <div class="alert alert-block">
        <a class="close" data-dismiss="alert" href="#">×</a>
        <h4 class="alert-heading">Debug!</h4>
        <ul>
          <li><?= implode('<li>', $vars['debug']); ?>
        </ul>
      </div>
      <?php endif; ?>

      <?= $vars['content']; ?>

      <footer class="footer">
        <p class="pull-right"><a href="#">Back to top</a></p>
        <p>&copy; Joel S&ouml;derberg 2012</p>
        <p><small class="created-by">Page generated.</small></p>
      </footer>

    </div> <!-- /container -->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="<?= relative_path('assets/js/all.js'); ?>"></script>
    
  </body>
</html>
