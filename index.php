<?php

  ini_set('display_errors',1);
  error_reporting(E_ALL);

  $customCSW = "no";
  $customCSW = "yes";

  require_once("./include/metadata.php");
  require_once("./include/functions.php");

  if ($customCSW == "yes" && isset($_GET["capabilities"]) && trim($_GET["capabilities"]) != "") {
    $cookie_value = trim($_GET["capabilities"]);
    $cookie_name = hash("crc32",$cookie_value);
//    setcookie($cookie_name, $cookie_value, 0, '/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_NAME'])), $_SERVER['SERVER_NAME'], false, true);
    setcookie($cookie_name, $cookie_value, 0);
    header("Location: ./" . $cookie_name . "/");
    exit;
  }
  
// Default profile

//  $profile = "extended";
  $profile = "http://data.europa.eu/930/";
//  $baseuri = 'http://' . $_SERVER['SERVER_NAME'] . '/' . trim(dirname($_SERVER['SCRIPT_NAME']),$_SERVER['DOCUMENT_ROOT']) . '/';
  $baseuri = 'http://' . $_SERVER['SERVER_NAME'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_NAME'])) . '/';
  $summary = "yes";
  $details = "yes";
  $exception = "no";

  $transform_api = $baseuri . "api/index.php";

  $serviceid = '';
  $itemid = '';
  $request = '';
  $method['service'] = 'get';
  $method["resource-list"] = 'get';
  $method["resource"] = 'get';
  $page = 1;
  $q = '';
  
  $formats["xml"]["media-type"] = "application/xml";
  $formats["rdf"]["media-type"] = "application/rdf+xml";
  $formats["ttl"]["media-type"] = "text/turtle";
  $formats["n3"]["media-type"] = "text/n3";
  $formats["nt"]["media-type"] = "application/n-triples";
  $formats["jsonld"]["media-type"] = "application/ld+json";
  $formats["html"]["media-type"] = "text/html";

// Default format  
  
//  $format = "html";
  $format = "";

  $accept = "";

// Max number of returned items
  
  $maxitems = 20;

// Test services

  $srv["eea-csw-copernicus"]["capabilities"] = "https://sdi.eea.europa.eu/catalogue/srv/eng/csw-copernicus?SERVICE=CSW&VERSION=2.0.2&REQUEST=GetCapabilities";
  $srv["eea-csw-copernicus"]["id"] = hash("crc32",$srv["eea-csw-copernicus"]["capabilities"]);
  $srv["eea-csw-copernicus"]["name"] = "EEA SDI Catalogue (Copernicus)";
  $srv["eea-csw-copernicus"]["description"] = "~70 records";

  $srv["eea-csw"]["capabilities"] = "https://sdi.eea.europa.eu/catalogue/srv/eng/csw?SERVICE=CSW&VERSION=2.0.2&REQUEST=GetCapabilities";
  $srv["eea-csw"]["id"] = hash("crc32",$srv["eea-csw"]["capabilities"]);
  $srv["eea-csw"]["name"] = "EEA SDI Catalogue";
  $srv["eea-csw"]["description"] = "1,000+ records";

  $srv["inspire-geoportal-csw"]["capabilities"] = "http://inspire-geoportal.ec.europa.eu/GeoportalProxyWebServices/resources/OGCCSW202?request=GetCapabilities&service=CSW";
  $srv["inspire-geoportal-csw"]["id"] = hash("crc32",$srv["inspire-geoportal-csw"]["capabilities"]);
  $srv["inspire-geoportal-csw"]["name"] = "INSPIRE Geoportal Discovery Service";
  $srv["inspire-geoportal-csw"]["description"] = "200,000+ records";
/*
  $srv["creodias-csw"]["capabilities"] = "https://catalogue.creodias.eu/srv/en/csw?request=GetCapabilities&service=CSW&version=2.0.2";
  $srv["creodias-csw"]["id"] = hash("crc32",$srv["creodias-csw"]["capabilities"]);
  $srv["creodias-csw"]["name"] = "CREODIAS";
  $srv["creodias-csw"]["description"] = "?? records";

  $srv["mundi-csw"]["capabilities"] = "https://catalog-browse.default.mundiwebservices.com/acdc/catalog/proxy/search/Sentinel1/csw?service=CSW&request=GetCapabilities";
  $srv["mundi-csw"]["id"] = hash("crc32",$srv["mundi-csw"]["capabilities"]);
  $srv["mundi-csw"]["name"] = "MUNDI";
  $srv["mundi-csw"]["description"] = "?? records";
*/  
/*
  $srv["onda-csw"]["capabilities"] = "https://www.onda-dias.eu/";
  $srv["onda-csw"]["id"] = hash("crc32",$srv["onda-csw"]["capabilities"]);
  $srv["onda-csw"]["name"] = "ONDA";
  $srv["onda-csw"]["description"] = "200,000+ records";
*/
/*
  $srv["sobloo-csw"]["capabilities"] = "https://sobloo.eu/api/v1/services/csw";
  $srv["sobloo-csw"]["id"] = hash("crc32",$srv["sobloo-csw"]["capabilities"]);
  $srv["sobloo-csw"]["name"] = "SOBLOO";
  $srv["sobloo-csw"]["description"] = "?? records";

  $srv["wekeo-csw"]["capabilities"] = "https://www.wekeo.eu/elastic-csw/service?service=CSW&request=GetCapabilities";
  $srv["wekeo-csw"]["id"] = hash("crc32",$srv["wekeo-csw"]["capabilities"]);
  $srv["wekeo-csw"]["name"] = "WEKEO";
  $srv["wekeo-csw"]["description"] = "?? records";
*/
  $service["id"] = "";//hash("crc32",$capabilities);
  $service["type"] = "CSW";//$qp["service"];
  $service["version"] = "";//$qp["version"];
  $service["url"] = "";//trim($capabilities,parse_url($capabilities, PHP_URL_QUERY));
  
  if (isset($_GET["serviceid"]) && (isset($srv[$_GET["serviceid"]]) || ($customCSW == "yes" && isset($_COOKIE[$_GET["serviceid"]])))) {
    $serviceid = $_GET["serviceid"];
    $service["id"] = $serviceid;
    $request = 'service';
    if (!isset($srv[$serviceid]) && $customCSW == "yes") {
      $srv[$serviceid]["capabilities"] = $_COOKIE[$serviceid];
    }
    parse_str(parse_url($srv[$serviceid]["capabilities"], PHP_URL_QUERY), $output);
    $qp = array();
    foreach ($output as $k => $v) {
      $qp[strtolower($k)] = $v;
    }
    if (isset($qp["type"])) {
      $service["type"] = $qp["service"];
    }
    if (isset($qp["version"])) {
      $service["version"] = $qp["version"];
    }
//    $service["url"] = trim($srv[$serviceid]["capabilities"],parse_url($srv[$serviceid]["capabilities"], PHP_URL_QUERY));
    $service["url"] = substr($srv[$serviceid]["capabilities"], 0, -strlen(parse_url($srv[$serviceid]["capabilities"], PHP_URL_QUERY)));
    $baseuri = $baseuri . $serviceid . "/resource/";
    if (isset($_GET["req"]) && $_GET["req"] != '') {
      if ($_GET["req"] == 'resource/') {
        $request = 'resource-list';
      }
      else {
        $request = 'resource';
      }
    }
    if (isset($_GET["itemid"]) && $_GET["itemid"] != '') {
      $itemid = $_GET["itemid"];
    }
    if (isset($_GET["format"]) && $_GET["format"] != '') {
      $format = $_GET["format"];
    }
    if (isset($_GET["page"]) && $_GET["page"] != '') {
      $page = $_GET["page"];
    }
  }
  
  if ($serviceid != '') {
    $url = $srv[$serviceid]["capabilities"];
    
    $xml = new DOMDocument;
    if (!$xml->load($url)) {
      returnHttpError(404);
    }
    
    $xpath = new DOMXPath($xml);
    $xpath->registerNamespace('xlink', 'https://www.w3.org/1999/xlink');
    
    $query["service_version"] = "/*//*[local-name() = 'ServiceTypeVersion']";
    $query["constraint_language"] = "/*//*[local-name() = 'Parameter' and translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz') = 'constraintlanguage']/*[local-name() = 'Value']";
    $query["getrecords_get_url"] = "/*//*[local-name() = 'Operation' and @name = 'GetRecords']/*[local-name() = 'DCP']/*[local-name() = 'HTTP']/*[local-name() = 'Get']/@*[local-name() = 'href']";
    $query["getrecords_post_url"] = "/*//*[local-name() = 'Operation' and @name = 'GetRecords']/*[local-name() = 'DCP']/*[local-name() = 'HTTP']/*[local-name() = 'Post']/@*[local-name() = 'href']";
    $query["getrecordbyid_get_url"] = "/*//*[local-name() = 'Operation' and @name = 'GetRecordById']/*[local-name() = 'DCP']/*[local-name() = 'HTTP']/*[local-name() = 'Get']/@*[local-name() = 'href']";
    $query["getrecordbyid_post_url"] = "/*//*[local-name() = 'Operation' and @name = 'GetRecordById']/*[local-name() = 'DCP']/*[local-name() = 'HTTP']/*[local-name() = 'Post']/@*[local-name() = 'href']";
    
    foreach ($query as $k => $v) {
      $entries = $xpath->query($query[$k]);
      for ($i = 0; $i < $entries->length; $i++) {
        $srv[$serviceid][$k][$i] = $entries->item($i)->nodeValue;
      }
    }
    
    $service_version = '2.0.2';
    if (isset($srv[$serviceid]["service_version"]) && $srv[$serviceid]["service_version"][0] != '') {
      $service_version = $srv[$serviceid]["service_version"][0];
    }    

    $constraint_language = 'CQL_TEXT';
    if (isset($srv[$serviceid]["constraint_language"]) && $srv[$serviceid]["constraint_language"] != '' && !in_array($constraint_language, $srv[$serviceid]["constraint_language"]) && $srv[$serviceid]["constraint_language"][0] != '') {
      $constraint_language = $srv[$serviceid]["constraint_language"][0];
    }

    $summary = "no";
    $details = "yes";
    if ($request == 'resource-list' || $request == 'resource') {
//      $method["resource"] = 'get';
      if (!isset($srv[$serviceid]['getrecordbyid_get_url']) && isset($srv[$serviceid]['getrecordbyid_post_url'])) {
        $method["resource"] = 'post';
      }
//      $url = $service["url"] . "request=GetRecordById&service=CSW&version=" . rawurlencode($service_version) . "&namespace=xmlns%28csw=" . rawurlencode('http://www.opengis.net/cat/csw') . "%29&resultType=results&outputSchema=" . rawurlencode('http://www.isotc211.org/2005/gmd') . "&outputFormat=" . rawurlencode('application/xml') . "&typeNames=csw:Record&elementSetName=full&id=" . $itemid;//rawurlencode($itemid);
      $url = $service["url"] . "request=GetRecordById&service=CSW&version=" . rawurlencode($service_version) . "&outputSchema=" . rawurlencode('http://www.isotc211.org/2005/gmd') . "&outputFormat=" . rawurlencode('application/xml') . "&ElementSetName=full&Id=" . $itemid;//rawurlencode($itemid);
      $summary = "no";
      $details = "yes";
      if ($itemid == '') {
//        $method["resource-list"] = 'get';
        if (!isset($srv[$serviceid]['getrecords_get_url']) && isset($srv[$serviceid]['getrecords_post_url'])) {
          $method["resource-list"] = 'post';
        }
        $url = $service["url"] . "request=GetRecords&service=CSW&version=" . rawurlencode($service_version) . "&namespace=xmlns%28csw=http://www.opengis.net/cat/csw%29&resultType=results&outputSchema=http://www.isotc211.org/2005/gmd&outputFormat=application/xml&typeNames=csw:Record&elementSetName=full&constraintLanguage=" . rawurlencode($constraint_language) . "&constraint_language_version=1.1.0&maxRecords=" . $maxitems . "&startPosition=". ($maxitems*($page-1)+1);
    
        if (isset($_GET["q"]) && $_GET["q"] != '') {
          $q = $_GET["q"];
          $constraint = "AnyText like '" . $q . "'";
          $url .= "&constraint=" . rawurlencode($constraint);
        }
        $summary = "yes";
        $details = "no";
      }
    }

    $request_url = '';
/*
    $xml = new DOMDocument;
    if (!$xml->load($url)) {
      returnHttpError(404);
    }
    
    $xpath = new DOMXPath($xml);
    $xpath->registerNamespace('xlink', 'http://www.opengis.net/ows');
    
    $query["exception"] = "/*[local-name() = 'ExceptionReport']";
    
    $entries = $xpath->query($query["exception"]);

//var_dump($entries);
//exit;
    if ($entries->length > 0) {
      $exception = "yes";
    }
*/    
    if ($format == "xml" || $exception == "yes") {
      $format = "xml";
      $request_url = $transform_api . "?src=" . rawurlencode($url) . "&outputFormat=" . rawurlencode($formats[$format]["media-type"]) . "&method=" . rawurlencode($method[$request]);
    }
    else {
      $request_url = $transform_api . "?src=" . rawurlencode($url) . "&outputSchema=" . rawurlencode($profile) . "&baseuri=" . rawurlencode($baseuri) . "&itemid=" . rawurlencode($itemid) . "&summary=" . rawurlencode($summary) . "&details=" . rawurlencode($details) . "&request=" . rawurlencode($request) . "&maxitems=" . rawurlencode($maxitems) . "&page=" . rawurlencode($page) . "&q=" . rawurlencode($q);
      if ($format != '' && isset($formats[$format]) && isset($formats[$format]["media-type"])) {
        $request_url .=  "&outputFormat=" . rawurlencode($formats[$format]["media-type"]);
      }
      if (isset($method[$request]) && $method[$request] != '') {
        $request_url .=  "&method=" . rawurlencode($method[$request]);
      }
    }
    $opts["http"]["method"] = "GET";
    if ($format == '') {
      $accept = $_SERVER['HTTP_ACCEPT'];
      $opts["http"]["header"] = "Accept: ". $accept . "\r\n";
    }
    $result_headers = get_headers($request_url,1);
    $result_http_code = explode(' ',$result_headers[0])[1];
    if ($format != '' && !(isset($formats[$format]) && isset($formats[$format]["media-type"]))) {
      $result_http_code = 404;
    }
    if (in_array($result_http_code, array('300','404','415'))) {
      returnHttpError($result_http_code);
    }
    else {
      $context = stream_context_create($opts);
      $result = file_get_contents($request_url, false, $context);
      header($result_headers[0]);
      header("Content-Type: " . $result_headers["Content-Type"]);
      echo $result;
      exit;
    }
  }

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<?php echo $head; ?>
  </head>
  <body>
    <header>
<?php echo $header; ?>
    </header>
<!--    
    <nav>
    </nav>
-->    
    <article>
      <section id="input-box">
<?php if ($customCSW != "yes") { ?>
        <h2>Try one of the following services for a demo</h2>
<?php } ?>
<?php if ($customCSW == "yes") { ?>
        <p>
        <form name="api" id="api" role="form" method="get" action=".">
          <div class="input-group input-group-lg">
            <input class="form-control" name="capabilities" id="capabilities" type="url" required="required" placeholder="Copy &amp; paste the capabilities URL of a CSW service" title="Copy &amp; paste the capabilities URL of a CSW service"/>
            <span class="input-group-btn">
              <button type="submit" class="btn btn-default" aria-label="Play <?php echo $title; ?>" title="Play <?php echo $title; ?>">
                <span class="glyphicon glyphicon-play" aria-hidden="true"></span>
              </button>
            </span>
          </div>
        </form>
        </p>
<?php } ?>
<?php if ($customCSW == "yes") { ?>
        <div class="panel panel-info">
          <div class="panel-heading"><span class="glyphicon  glyphicon-info-sign"></span> Please read below before submitting the form</div>
          <div class="panel-body">
            <p>The form above, when submitted, will send you a cookie including the URL you specified, which will be used to generate the <?php echo $title; ?> pages you will be visiting.</p>
            <p>The cookie will be used only for that purpose, and it will be deleted when you close your browser. However, if you prefer to have no cookie set, you can try one of the following services for a demo.</p>
<?php } ?>
            <div class="list-group">
<?php

  foreach ($srv as $k => $v) {
    echo '            <a class="list-group-item" href="./' . $k . '/">' . "\n";
    echo '            <h5 class="list-group-item-heading">' . $srv[$k]["name"] . '</h5>' . "\n";
    echo '            <p class="list-group-item-text">' . $srv[$k]["description"] . '</p>' . "\n";
    echo '            </a>' . "\n";
  }

?>     
            </div>
<?php if ($customCSW == "yes") { ?>
          </div>
        </div>
<?php } ?>
      </section>
      <section>
        <h2>What is <?php echo $title; ?>?</h2>
        <p><?php echo $title; ?> is a proof-of-concept API designed to expose a <a href="http://www.opengeospatial.org/standards/cat">CSW</a> endpoint in a Web-friendly way.</p>
        <p>Basically, it converts the output of CSW calls into an HTML+RDFa representation - i.e., and HTML page, with metadata embedded as per the <a href="https://www.w3.org/TR/html-rdfa/">W3C HTML+RDFa Recommendation</a>. Moreover, it gives access to multiple machine-readable representations of records available from a CSW. More precisely, besides <a href="https://www.iso.org/standard/32557.html">ISO 19139</a>, natively supported by the relevant CWS endpoint, it makes available metadata as per the <a href="https://joinup.ec.europa.eu/solution/geodcat-application-profile-data-portals-europe">GeoDCAT-AP specification</a>. All the supported representations and serialisations are also accessible via <a href="https://tools.ietf.org/html/rfc7231#section-3.4">HTTP content negotiation</a>.</p>
        <p>In order to achieve this, the <?php echo $title; ?> API needs only the URL corresponding to the <code>GetCapabilities</code> request of a given CSW endpoint. Based on the available metadata, it returns a description of the relevant endpoint, along with the list of available resources, as well as a detailed description of each of them.</p>
        <p>Finally, all these pages are given HTTP URIs based on the following pattern:</p>
        <pre>/&lt;<var>service_ID</var>&gt;/(resource/(&lt;<var>resource_ID</var>&gt;)?)?</pre>
        <p>where:</p>
        <ul>
          <li><var>service_ID</var> is a system ID for the relevant CSW endpoint;</li>
          <li><var>resource_ID</var> corresponds to the metadata file identifier of the relevant record.</li>
        </ul>
        <h3>Technical details</h3>
        <p><?php echo $title; ?> uses a customised version of the <a href="https://github.com/SEMICeu/iso-19139-to-dcat-ap">GeoDCAT-AP XSLT &amp; API</a> to generate the different pages.</p>
        <p>Used CSW request types: <code>GetCapabilities</code>, <code>GetRecords</code>, <code>GetRecordById</code>.</p>
        <p><em>A description of the <?php echo $title; ?> API is available on the <a href="<?php echo $apiSrcRep; ?>">dedicated GitHub repository</a>.</em></p>
      </section>
    </article>
    <aside>
    </aside>
    <footer><?php echo $footer; ?></footer>
  </body>
</html>
