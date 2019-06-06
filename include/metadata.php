<?php

// The API

  $apiSrcRep = "https://github.com/SEMICeu/csw-4-web";

// Variables for API landing page.

  $title = "CSW-4-Web";
  $subtitle = "A Web-friendly front-end for CSW endpoints";

  $head = '';

  $footer = '';

// Default Bootstrap style
/*
  $logo = "";
  $logotitle = "";
  $logourl = "";
  
  $includeHomeLink = "no";

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
*/
// Style on geodcat-ap.semic.eu

  $logo = "http://geodcat-ap.semic.eu/common/isa-dcat-ap-geo-logo.png";
  $logotitle = "GeoDCAT-AP";
  $logourl = "https://joinup.ec.europa.eu/solution/geodcat-application-profile-data-portals-europe";

  $includeHomeLink = "yes";

  $head .= '<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/readable/bootstrap.min.css" media="screen"/>' . "\n";
  $head .= '<link rel="stylesheet" type="text/css" href="http://geodcat-ap.semic.eu/common/normalize.css"/>' . "\n";
  $head .= '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400italic,600,600italic,300"/>' . "\n";
  $head .= '<link rel="stylesheet" type="text/css" href="http://geodcat-ap.semic.eu/common/screen.css"/>' . "\n";
  $head .= '<link rel="stylesheet" type="text/css" href="' . trim($_SERVER["SCRIPT_NAME"],basename($_SERVER["SCRIPT_NAME"])) . 'css/style.css"/>' . "\n";

  $footer = '';
  $footer .= '<p>';
  $footer .= ' <a href="https://joinup.ec.europa.eu/community/are3na/" target="_blank"><img alt="ARe3NA" src="http://geodcat-ap.semic.eu/common/isa-are3na-logo.jpg" height="70"></a> ';
  $footer .= ' <a href="https://joinup.ec.europa.eu/community/semic/" target="_blank"><img alt="SEMIC" src="http://geodcat-ap.semic.eu/common/isa-semic-logo.png" height="70"></a> ';
  $footer .= ' <a href="http://ec.europa.eu/isa/" target="_blank"><img alt="ISA" src="http://geodcat-ap.semic.eu/common/isa-logo.png" height="70"></a> ';
  $footer .= '</p>';

// Style on inspire-sandbox.jrc.ec.europa.eu
/*
  $title = "CSW-4-Web";
  $subtitle = "A Web-friendly front-end for CSW endpoints";

//  $logo = "http://geodcat-ap.semic.eu/common/isa-dcat-ap-geo-logo.png";
//  $logotitle = "GeoDCAT-AP";
//  $logourl = "https://joinup.ec.europa.eu/solution/geodcat-application-profile-data-portals-europe";

  $includeHomeLink = "no";

  $head .= '  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="' . $subtitle . '.">
  <meta name="author" content="European Commission - Joint Research Centre">

  <meta property="og:title" content="INSPIRE GeoDCAT-AP Sandbox: ' . $title . '"/>
  <meta property="og:description" content="'. $subtitle . '."/>
  <meta property="og:url" content="http://inspire-sandbox.jrc.ec.europa.eu/geodcat-ap/csw-4-web/"/>
  <meta property="og:image" content="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/favicon-194x194.png"/>
  <meta property="og:type" content="website"/>

  <title>INSPIRE GeoDCAT-AP Sandbox</title>

  <link rel="stylesheet" href="http://inspire.ec.europa.eu/cdn/latest/css/ec.eu.css">

  <link rel="apple-touch-icon" sizes="57x57" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/apple-touch-icon-180x180.png">
  <link rel="icon" type="image/png" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/favicon-194x194.png" sizes="194x194">
  <link rel="icon" type="image/png" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/png" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/android-chrome-192x192.png" sizes="192x192">
  <link rel="icon" type="image/png" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/favicon-16x16.png" sizes="16x16">
  <link rel="shortcut icon" href="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/favicon.ico">
  <meta name="msapplication-TileImage" content="http://inspire.ec.europa.eu/cdn/latest/img/ec.ico/mstile-144x144.png">
  <meta name="msapplication-TileColor" content="#2b5797">
  <meta name="theme-color" content="#003399">

  <link rel="stylesheet" href="http://inspire-sandbox.jrc.ec.europa.eu/css/inspire-sandbox.css">
  <style type="text/css">
.inspire-tabs{
  font-size:90%;
  padding:20px 100px 0px 100px;
}
  </style>
  <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
  <script type="text/javascript">$(document).ready(function(){$("nav").addClass("inspire-tabs");$("article").addClass("inspire-docs container");});</script>

  <!--[if lt IE 9]>
  <script src="http://inspire.ec.europa.eu/cdn/latest/js/html5shiv.min.js"></script>
  <script src="http://inspire.ec.europa.eu/cdn/latest/js/respond.min.js"></script>
  <![endif]-->
' . "\n";
  $head .= '<link rel="stylesheet" type="text/css" href="' . trim($_SERVER["SCRIPT_NAME"],basename($_SERVER["SCRIPT_NAME"])) . 'css/style.css"/>' . "\n";
  
  $header = '';
  $header .= '    <div class="hb1">
      <div class="container relative">
        <a class="ec-logo" href="#"><img src="http://inspire.ec.europa.eu/cdn/latest/img/ec.logo/logo_en.gif" /></a>
        <span class="mt">INSPIRE</span>

        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#topm" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>

        <div id="topm" class="topm navbar-collapse collapse">
          <ul class="lnk">
            <li><a href="/">About</a></li><li><a href="mailto:dcat_application_profile-geo@joinup.ec.europa.eu">Contact</a></li><li><a href="http://ec.europa.eu/geninfo/legal_notices_en.htm" target="_blank">Legal Notice</a></li>
          </ul>
          <select data-live-search="true" class="selectpicker">
<!--
            <option value="bg">български (bg)</option>
            <option value="cs">ceština (cs)</option>
            <option value="da">dansk (da)</option>
            <option value="de">Deutsch (de)</option>
            <option value="et">eesti keel (et)</option>
            <option value="el">ελληνικά (el)</option>
-->
            <option value="en">English (en)</option>
<!--
            <option value="es">español (es)</option>
            <option value="fr">français (fr)</option>
            <option value="hr">hrvatski (hr)</option>
            <option value="it">italiano (it)</option>
            <option value="lv">latviešu valoda (lv)</option>
            <option value="lt">lietuviu kalba (lt)</option>
            <option value="hu">magyar (hu)</option>
            <option value="mt">Malti (mt)</option>
            <option value="nl">Nederlands (nl)</option>
            <option value="pl">polski (pl)</option>
            <option value="pt">português (pt)</option>
            <option value="ro">româna (ro)</option>
            <option value="sk">slovencina (sk)</option>
            <option value="sl">slovenšcina (sl)</option>
            <option value="fi">suomi (fi)</option>
            <option value="sv">svenska (sv)</option>
-->
          </select>
        </div>

      </div>
    </div>
    <div class="hb2">
      <div class="container">
        <span>' . $title . '</span>
      </div>
    </div>
    <div class="hb3 hidden-xs">
      <div class="container">
        <ol class="breadcrumb" vocab="http://schema.org/" typeof="BreadcrumbList">
          <li property="itemListElement" typeof="ListItem">
            <a property="item" typeof="WebPage" href="http://ec.europa.eu/index_en.htm"><span property="name">European Commission</span></a>
          </li>
          <li property="itemListElement" typeof="ListItem">
            <a property="item" typeof="WebPage" href="http://inspire.ec.europa.eu"><span property="name">INSPIRE</span></a>
          </li>
          <li property="itemListElement" typeof="ListItem">
            <a property="item" typeof="WebPage" href="/"><span property="name">Sandbox</span></a>
          </li>
          <li property="itemListElement" typeof="ListItem">
            <a property="item" typeof="WebPage" href="/geodcat-ap/"><span property="name">GeoDCAT-AP Sandbox</span></a>
          </li>
          <li class="active" property="itemListElement" typeof="ListItem">
            <a property="item" typeof="WebPage" href="' . preg_replace("/\/api\/$/","/",trim($_SERVER["SCRIPT_NAME"],basename($_SERVER["SCRIPT_NAME"]))) . '"><span property="name">' . $title . '</span></a>
          </li>
        </ol>
      </div>
    </div>
';

  $footer = '';
  $footer .= '    <div class="ft1">
      <div class="container">
        <div class="row">
          <div class="col-xs-6">INSPIRE GeoDCAT-AP Sandbox</div>
<!--
          <div class="col-xs-6 text-right">Powered by: <a href="http://virtuoso.openlinksw.com/">Virtuoso</a></div>
-->
        </div>
      </div>
    </div>
    <div class="ft2">
      <div class="container">
        <div class="row">
          <div class="col-sm-15 hidden-xs">
            <img class="ec-inspire-logo" src="http://inspire.ec.europa.eu/cdn/latest/img/ec.inspire.logo/logo_en.png" />
          </div>
          <div class="col-sm-15">
            <h4>INSPIRE<a data-toggle="collapse" href="#c1" aria-expanded="false" aria-controls="c1" class="navbar-toggle collapsed">&nbsp;</a></h4>
            <ol class="navbar-collapse collapse" id="c1">
              <li><a href="http://inspire.ec.europa.eu/">INSPIRE Web Site</a></li>
              <li><a href="http://inspire.ec.europa.eu/legislation">INSPIRE Legislation</a></li>
              <li><a href="http://inspire.ec.europa.eu/library">INSPIRE Library</a></li>
              <li><a href="http://inspire.ec.europa.eu/forum">INSPIRE Forum</a></li>
              <li><a href="http://inspire.ec.europa.eu/thematic-clusters">INSPIRE Thematic clusters</a></li>
            </ol>
          </div>
          <div class="col-sm-15">
            <h4>NEWS &amp; EVENTS<a data-toggle="collapse" href="#c2" aria-expanded="false" aria-controls="c2" class="navbar-toggle collapsed">&nbsp;</a></h4>
            <ol class="navbar-collapse collapse" id="c2">
              <li><a href="http://inspire.ec.europa.eu/news">News</a></li>
              <li><a href="http://inspire.ec.europa.eu/events">Events</a></li>
              <li><a href="http://inspire.ec.europa.eu/subscribe_news">Subscribe to INSPIRE news</a></li>
              <li><a href="http://inspire.ec.europa.eu/rssnew.cfm">RSS News</a></li>
            </ol>
          </div>
          <div class="col-sm-15">
            <h4>INSPIRE Tools<a data-toggle="collapse" href="#c3" aria-expanded="false" aria-controls="c3" class="navbar-toggle collapsed">&nbsp;</a></h4>
            <ol class="navbar-collapse collapse" id="c3">
              <li><a href="http://inspire-geoportal.ec.europa.eu/">Geoportal</a></li>
              <li><a href="http://inspire-geoportal.ec.europa.eu/validator2/">Validator</a></li>
              <li><a href="http://inspire-geoportal.ec.europa.eu/editor/">Metadata Editor</a></li>
              <li><a href="http://inspire.ec.europa.eu/registry/">Registry</a></li>
              <li><a href="http://inspire-regadmin.jrc.ec.europa.eu/dataspecification/">Data Specification toolkit</a></li>
            </ol>
          </div>
          <div class="col-sm-15">
            <h4>GeoDCAT-AP Sandbox<a data-toggle="collapse" href="#c4" aria-expanded="false" aria-controls="c4" class="navbar-toggle collapsed">&nbsp;</a></h4>
            <ol id="c4" class="navbar-collapse collapse">
<!--
              <li><a href="./fct">GeoDCAT-AP Faceted Browser</a></li>
              <li><a href="./sparql">GeoDCAT-AP SPARQL Endpoint</a></li>
-->
              <li><a href="/geodcat-ap/api">GeoDCAT-AP API</a></li>
              <li><a href="/geodcat-ap/csw-4-web/">' . $title . '</a></li>
              <li><a href="https://joinup.ec.europa.eu/asset/dcat_application_profile/asset_release/geodcat-ap-v10">GeoDCAT-AP 1.0 Specification</a></li>
            </ol>
          </div>

        </div>
      </div>
    </div>
    <div class="ft3">
      <div class="container">
        <div class="pull-right">
        <ol>
          <li><a href="/">About</a></li>
          <li><a href="mailto:dcat_application_profile-geo@joinup.ec.europa.eu">Contact</a></li>
          <li><a target="_blank" href="http://ec.europa.eu/geninfo/legal_notices_en.htm">Legal notice</a></li>
        </ol>
        <div class="social">
          <div>
            <a href="https://twitter.com/INSPIRE_EU"><i class="fa fa-twitter-square "></i></a>
            <a href="https://www.facebook.com/groups/inspiredirective/"><i class="fa fa-facebook-square"></i></a>
          </div>
        </div>
        </div>
      </div>
    </div>
';
*/
  $header = '';
  if (isset($logo) && $logo != '') {
    $header .= '      <div class="logo"><a href="' . $logourl . '" target="_blank"><img src="' . $logo . '" title="' . $logotitle . '" width="90" height="90"/></a></div>' . "\n";
  }
  $header .= '      <h1>' . $title . '</h1>' . "\n";
  $header .= '      <p class="subtitle">' . $subtitle . '</p>' . "\n";

?>
