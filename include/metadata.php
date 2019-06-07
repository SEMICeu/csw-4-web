<?php

// The API

  $apiSrcRep = "https://github.com/SEMICeu/csw-4-web";

// Variables for API landing page.

  $title = "CSW-4-Web";
  $subtitle = "A Web-friendly front-end for CSW endpoints";

  $head = '';

  $footer = '';

// Default Bootstrap style

  $logo = "";
  $logotitle = "";
  $logourl = "";
  
  $includeHomeLink = "yes";

  $head .= '<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/readable/bootstrap.min.css" media="screen"/>' . "\n";
  $head .= '<link type="text/css" rel="stylesheet" href="https://getbootstrap.com/docs/3.3/assets/css/docs.min.css"/>
<style rel="stylesheet" type="text/css">
.metadata dd {
  margin-left: 40px;
}
.metadata a, .metadata code, .metadata pre {
  overflow-wrap: break-word;
  word-wrap: break-word;
  word-break: break-all;
}
</style>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://getbootstrap.com/docs/3.3/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://getbootstrap.com/docs/3.3/assets/js/docs.min.js"></script>  
<script type="text/javascript">
$(document).ready(function() {
//$("header").addClass("navbar bg-primary container-fluid");
//$("header > h1").addClass("navbar-header container");
//$("header > p").addClass("lead container");
$("header").addClass("bs-docs-header");
$("header > h1").addClass("container");
$("header > p").addClass("container");
$("nav").addClass("container");
$("footer").addClass("page-footer container text-muted small text-center").css("padding","1em");
$("body > article").addClass("bs-docs-container container");
$("body > article section").addClass("bs-docs-section").css("padding","1em");
});
</script>
';

  $footer = '';
  $footer .= '<p>';
  $footer .= $title .' @ GitHub: <a href="' . $apiSrcRep . '">' . $apiSrcRep . '</a> ';
  $footer .= '</p>';

  $header = '';
  if (isset($logo) && $logo != '') {
    $header .= '      <div class="logo"><a href="' . $logourl . '" target="_blank"><img src="' . $logo . '" title="' . $logotitle . '" width="90" height="90"/></a></div>' . "\n";
  }
  $header .= '      <h1>' . $title . '</h1>' . "\n";
  $header .= '      <p class="subtitle">' . $subtitle . '</p>' . "\n";

?>
