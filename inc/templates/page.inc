<!DOCTYPE html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <title><?php if (!empty($vars['page_title'])) echo $vars['page_title'] . ' | '; ?>joelsoderberg.se, own your own computing!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A blog about tech and random geekieness">
    <meta name="author" content="Joel Söderberg">

    <link href="/assets/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/assets/styles.css" rel="stylesheet">
    <link href="/assets/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">

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
          <a class="brand" href="/">joelsoderberg.se</a>
<!--
          <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="icon-user"></i> Administrator
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="/post/create">Create Post</a></li>
              <li><a href="/settings">Other Settings</a></li>
              <li class="divider"></li>
              <li><a href="/user/logout">Sign Out</a></li>
            </ul>
          </div> -->
          <div class="nav-collapse">
            <ul class="nav">
              <li class="<?php if (!arg(0) || arg(0) === 'post') echo 'active'; ?>"><a href="/">Blog</a></li>
              <li class="<?php if (arg(0) === 'about') echo 'active'; ?>"><a href="/about">About</a></li>
              <li><a href="/contact">Contact</a></li>
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
        <p>&copy; Joel Söderberg 2012<p>
         <p>Uses <a href="http://twitter.github.com/bootstrap/">Twitter Bootstrap</a>, <a href="http://daringfireball.net/projects/markdown/">markdown</a> and <a href="http://michelf.com/projects/php-markdown/">php-markdown</a>

       <br /><a href="/post/create">Create Post</a> <a href="/authenticate">Account</a> 
        </p>
      </footer>

    </div> <!-- /container -->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.js"></script>
    <script src="/assets/scripts.js"></script>
  </body>
</html>
