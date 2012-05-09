<?php

$vars['content'] = '<div class="alert alert-error"><h4 class="alert-heading">Document not found!</h4>
<p>Sorry man! There\'s nothing to see here. Send me an <a href="mailto:joel@joelsodererg.se">email</a> if you think that there should be!</p>';
if (!empty($vars['message'])) 
  $vars['content'] .= '<p>' . $vars['message'] . '</p>';
$vars['content'] .= '</div>';
require 'inc/templates/page.inc';


