<?php
  
  error_reporting(E_ALL);
//  ini_set('display_errors',1);
  ini_set('display_errors',0);

  require_once('../include/functions.php');
  require_once("../include/metadata.php");

// Loading the required libraries

  require('./lib/composer/vendor/autoload.php');

// Input schemas 

// Output schemas

  $outputSchemas = array();
//  $outputSchemas['core'] = array(
  $outputSchemas['http://data.europa.eu/r5r/'] = array(
    'label' => 'DCAT-AP',
    'description' => 'TBD',
    'url' => 'TBD',
//    'xslt' => 'https://raw.githubusercontent.com/SEMICeu/iso-19139-to-dcat-ap/master/iso-19139-to-dcat-ap.xsl',
    'xslt' => './xslt/iso-19139-to-dcat-ap.xsl',
    'params' => array(
//      'profile' => 'core'
      'profile' => 'http://data.europa.eu/r5r/'
    )
  );
//  $outputSchemas['extended'] = array(
  $outputSchemas['http://data.europa.eu/930/'] = array(
    'label' => 'GeoDCAT-AP',
    'description' => 'TBD',
    'url' => 'TBD',
//    'xslt' => 'https://raw.githubusercontent.com/SEMICeu/iso-19139-to-dcat-ap/master/iso-19139-to-dcat-ap.xsl',
    'xslt' => './xslt/iso-19139-to-dcat-ap.xsl',
    'params' => array(
//      'profile' => 'extended'
      'profile' => 'http://data.europa.eu/930/'
    )
  );
//  $defaultOutputSchema = 'core';
  $defaultOutputSchema = 'http://data.europa.eu/930/';

// XSLT to generate the HTML+RDFa serialisation

//  $rdf2rdfa = "https://raw.githubusercontent.com/SEMICeu/dcat-ap-rdf2html/master/dcat-ap-rdf2rdfa.xsl";
  $rdf2rdfa = "./xslt/dcat-ap-rdf2rdfa.xsl";

// Input format

  $inputFormat = 'application/xml';

// Output formats

  $outputFormats = array();
//  $outputFormats['application/xml'] = array('XML','','xml');
  $outputFormats['text/html'] = array('HTML+RDFa','','html');
  $outputFormats['application/rdf+xml'] = array('RDF/XML','rdf','rdf');
  $outputFormats['text/turtle'] = array('Turtle','turtle','ttl');
  $outputFormats['text/n3'] = array('N3','n3','n3');
  $outputFormats['application/n-triples'] = array('N-Triples','ntriples','nt');
  $outputFormats['application/ld+json'] = array('JSON-LD','jsonld','jsonld');
//  $defaultOutputFormat = 'application/rdf+xml';
  $defaultOutputFormat = 'text/html';

// NOTE: The following function has been moved to file ../include/functions.php

// HTTP codes & corresponding pages
/*
  function returnHttpError($code) {
    global $head;
    
    $http_code["300"]["title"] = ' Multiple Choices';
    $http_code["300"]["content"] = '';

    $http_code["404"]["title"] = ' Not Found';
    $http_code["404"]["content"] = 'The requested URL <code>' . $_SERVER["REQUEST_URI"] . '</code> was not found on this server.';
    
    $http_code["415"]["title"] = ' Unsupported Media Type';
    $http_code["415"]["content"] = 'The server does not support the media type transmitted in the request.';

    $title =  $_SERVER["SERVER_PROTOCOL"] . ' ' . $code;
    $content = '';
    
    if (isset($http_code[$code])) {
      $title  .= $http_code[$code]["title"];
      $content = $http_code[$code]["content"];
      http_response_code($code);
      echo '<!DOCTYPE html><html><head><title>' . $title . '</title>' . str_replace("\n", "", $head) . '</head><body><header><h1>' . $title . '</h1></header><section><p>' . $content . '</p></section></body></html>';
      exit;
    }
  }
*/
// Setting the output schema

  if (isset($_GET['src'])) {

    $xmluri = $_GET['src'];

    $outputSchema = $defaultOutputSchema;
    $xsluri = $outputSchemas[$outputSchema]['xslt'];
    $outputSchemas[$outputSchema]['params']['src'] = $xmluri;
    if (isset($_GET['outputSchema'])) {
      if (isset($outputSchemas[$_GET['outputSchema']])) {
        $outputSchema = $_GET['outputSchema'];
        $xsluri = $outputSchemas[$outputSchema]['xslt'];
        $outputSchemas[$outputSchema]['params']['src'] = $xmluri;
        if (isset($_GET['baseuri'])) {
          $outputSchemas[$outputSchema]['params']['baseuri'] = $_GET['baseuri'];
        }
        if (isset($_GET['itemid'])) {
          $outputSchemas[$outputSchema]['params']['itemid'] = rawurlencode($_GET['itemid']);
        }
        if (isset($_GET['request'])) {
          $outputSchemas[$outputSchema]['params']['request'] = $_GET['request'];
        }
        if (isset($_GET['maxitems'])) {
          $outputSchemas[$outputSchema]['params']['maxitems'] = $_GET['maxitems'];
        }
        if (isset($_GET['page'])) {
          $outputSchemas[$outputSchema]['params']['page'] = $_GET['page'];
        }
        if (isset($_GET['q'])) {
          $outputSchemas[$outputSchema]['params']['q'] = urlencode($_GET['q']);
        }
        if (isset($_GET['summary'])) {
          $outputSchemas[$outputSchema]['params']['summary'] = $_GET['summary'];
        }
        if (isset($_GET['details'])) {
          $outputSchemas[$outputSchema]['params']['details'] = $_GET['details'];
        }
      }
      else {
        returnHttpError(404);
      }
    }

// Loading the source document 

    if (isset($_GET["method"]) && $_GET["method"] == 'post') {
      parse_str(parse_url($xmluri, PHP_URL_QUERY), $params);
      $data = getRequestAsXML($params);
      $opts["http"]["method"] = "POST";
      $opts["http"]["header"] = "Content-type: application/xml";
      $opts["http"]["content"] = $data;
      $context = stream_context_create($opts);
      $response = file_get_contents($xmluri, false, $context);
    }
    else {
      $response = file_get_contents($xmluri);
    }
    
//    echo $response;
//    exit;
    
    if ($response === false) {
      returnHttpError(404);
    }

    if (isset($_GET['outputFormat']) && $_GET['outputFormat'] == $inputFormat) {
      header("Content-Type: application/xml");
      echo $response;
      exit;
    }

    $xml = new DOMDocument;
//    if (!$xml->load($xmluri)) {
    if (!$xml->loadXML($response)) {
      returnHttpError(404);
    }
    else {
      $xpath = new DOMXPath($xml);
      $xpath->registerNamespace('xlink', 'http://www.opengis.net/ows');
    
      $query["exception"] = "/*[local-name() = 'ExceptionReport']";
    
      $entries = $xpath->query($query["exception"]);

      if ($entries->length > 0) {
        header("Content-Type: application/xml");
        echo $response;
        exit;
      }
    }
    
    $xpath = new DOMXPath($xml);
    
    $query["resource-nr"] = "/*//*[local-name() = 'SearchResults']/@numberOfRecordsMatched";
    
    $result = array();
    
    foreach ($query as $k => $v) {
      $entries = $xpath->query($query[$k]);
      if ($entries->length === 1) {
        $result[$k] = $entries->item(0)->nodeValue;
      }
    }

    if (isset($result['resource-nr'])) {
      $outputSchemas[$outputSchema]['params']['resource-nr'] = $result['resource-nr'];
    }
    
// Loading the XSLT to transform the source document into RDF/XML

    $xsl = new DOMDocument;
    if (!$xsl->load($xsluri)) {
      returnHttpError(404);
    }

// Transforming the source document into RDF/XML

    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl);

    foreach ($outputSchemas[$outputSchema]['params'] as $k => $v) {
      $proc->setParameter("", $k, $v);
    }
    
    $proc->setParameter("", "CoupledResourceLookUp", "disabled");

    if (!$rdf = $proc->transformToXML($xml)) {
      returnHttpError(404);
    }

    $outputFormat = $defaultOutputFormat;
    if (isset($_GET['outputFormat'])) {
      if (!isset($outputFormats[$_GET['outputFormat']])) {
        returnHttpError(415);
      }
      else {
        $outputFormat = $_GET['outputFormat'];
      }
    }
    else {
      preg_match_all("/[a-zA-Z0-9]+\/[a-zA-Z0-9\!\#\$\&\-\^_\.\+]+/", $_SERVER["HTTP_ACCEPT"], $matches);
      if (count($matches[0]) < 1) {
// NOTE: For unknown reasons, this is returned also when the condition is not met
//        returnHttpError(415);
      }
      else {
        $acceptedFormats = $matches[0];
        $supportedFormats = array_keys($outputFormats);
        $candidateFormats = array_values(array_intersect($supportedFormats, $acceptedFormats));
        switch (count($candidateFormats)) {
          case 0:
            returnHttpError(415);
            break;
          case 1:
            $outputFormat = $candidateFormats[0];
            break;
          default:
            returnHttpError(300);
            break;
        }
      }
    }

// Related resources

    $resource_uri = '';
    if (isset($outputSchemas[$outputSchema]["params"]["request"]) && isset($outputSchemas[$outputSchema]["params"]["request"])) {
      switch ($outputSchemas[$outputSchema]["params"]["request"]) {
        case 'service':
          $resource_uri = substr($outputSchemas[$outputSchema]["params"]["baseuri"], 0, -strlen("resource/"));
          break;
        case 'resource-list':
          $resource_uri  = $outputSchemas[$outputSchema]["params"]["baseuri"];
          break;
        case 'resource':
          $resource_uri = $outputSchemas[$outputSchema]["params"]["baseuri"] . rawurlencode($outputSchemas[$outputSchema]["params"]["itemid"]);
          break;
      }
    }

// The metadata profile of the output resource (output schema)
    $link[] = array(
      "href" => $outputSchema,
      "rel" => "profile",
      "type" => $outputFormat,
      "title" => $outputSchemas[$outputSchema]["label"]
    );
// The input resource
    if ($resource_uri == '') {
      $uri = $xmluri . ".xml";
    }
    else {
      $uri = $resource_uri . ".xml";
    }

    $link[] = array(
//      "href" => $xmluri,
      "href" => $uri,
      "rel" => "derivedfrom",
      "type" => "application/xml",
      "title" => "XML"
    );
// The available serialisations of the output resource (output format)
    foreach ($outputFormats as $k => $v) {
      $query_string = '';
      foreach ($outputSchemas[$outputSchema]["params"] as $pk => $pv) {
        if ($pk != 'profile') {
          $query_string .= '&' . $pk . '=' . urlencode($pv);
        }
      }
//      $uri = str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']) . 'outputSchema=' . rawurlencode($outputSchema) . '&src=' . rawurlencode($xmluri) . '&outputFormat=' . rawurlencode($k);
      $uri = str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']) . 'outputSchema=' . rawurlencode($outputSchema) . '&outputFormat=' . rawurlencode($k) . $query_string;
      if ($resource_uri != '') {
        $uri = $resource_uri . "." . $v[2];
     }
      $rel = 'alternate';
      if ($k == $outputFormat) {
        $rel = 'self';
      }
      $link[] = array(
        "href" => $uri,
        "rel" => $rel,
        "type" => $k,
        "title" => $v[0]
      );
      $outputFormats[$k][] = $uri;
    }

// Building HTTP "Link" headers and HTML "link" elements pointing to the related resources

    $linkHTTP = array();
    $linkHTML = array();
    $anchorHTML = array();
    foreach ($link as $v) {
      $linkHTTP[] = '<' . $v["href"] . '>; rel="' . $v["rel"] . '"; type="' . $v["type"] . '"; title="' . $v["title"] . '"';
      $linkHTML[] = '<link href="' . $v["href"] . '" rel="' . $v["rel"] . '" type="' . $v["type"] . '" title="' . $v["title"] . '"/>';
      if ($v["rel"] == "alternate" || $v["rel"] == "derivedfrom") {
        $anchorHTML[] = '<a href="' . $v["href"] . '" rel="' . $v["rel"] . '" type="' . $v["type"] . '" title="' . $v["title"] . '">' . $v["title"] . '</a>';
      }  
    }

// Setting namespace prefixes

    EasyRdf_Namespace::set('adms', 'http://www.w3.org/ns/adms#');
    EasyRdf_Namespace::set('cnt', 'http://www.w3.org/2011/content#');
    EasyRdf_Namespace::set('dc', 'http://purl.org/dc/elements/1.1/');
    EasyRdf_Namespace::set('dcat', 'http://www.w3.org/ns/dcat#');
    EasyRdf_Namespace::set('gsp', 'http://www.opengis.net/ont/geosparql#');
    EasyRdf_Namespace::set('locn', 'http://www.w3.org/ns/locn#');
    EasyRdf_Namespace::set('prov', 'http://www.w3.org/ns/prov#');

// Creating the RDF graph from the RDF/XML serialisation

    $graph = new EasyRdf_Graph;
//    $graph->parse($rdf, "rdfxml", $outputFormats['application/rdf+xml'][2]);
    $graph->parse($rdf);

// Sending HTTP headers

    header("Content-type: " . $outputFormat . ';charset=utf-8');
    header('Link: ' . join(', ', $linkHTTP));

    if ($outputFormat == 'text/html') {
      
      $xml = new DOMDocument;
// From the raw RDF/XML output of the stylesheet
      $xml->loadXML($rdf) or die();
// From the re-serialised RDF/XML output of the stylesheet
//      $xml->loadXML($graph->serialise("rdfxml")) or die();
      $xsl = new DOMDocument;
      $xsl->load($rdf2rdfa);
      $proc = new XSLTProcessor();
      $proc->importStyleSheet($xsl);
// The title of the HTML+RDFa
      $proc->setParameter('', 'title', $title);
      $proc->setParameter('', 'subtitle', $subtitle);
// The URL of the repository
      $proc->setParameter('', 'home', $apiSrcRep);
// All what needs to be added in the HEAD of the HTML+RDFa document
      $head .= join("\n", $linkHTML) . "\n";
      $proc->setParameter('', 'head', $head);
      $altformats = join(" | ", $anchorHTML) . "\n";
      $proc->setParameter('', 'alt-formats', $altformats);
      if (isset($logo)) {
	$proc->setParameter('', 'logo', $logo);
	$proc->setParameter('', 'logotitle', $logotitle);
	$proc->setParameter('', 'logourl', $logourl);
      }
      if (isset($includeHomeLink)) {
	$proc->setParameter('', 'include-home-link', $includeHomeLink);
      }
      $proc->setParameter('', 'header', $header);
      $proc->setParameter('', 'footer', $footer);
      
      foreach ($outputSchemas[$outputSchema]['params'] as $k => $v) {
        $proc->setParameter("", $k, $v);
      }
      
      echo $proc->transformToXML($xml);
      exit;
    }
    else {
// Block added to enable pretty-print output of the JSON-LD serialisation, not supported in the current version of EasyRdf
// Predefined constants are as per ML/JsonLD - see: https://github.com/lanthaler/JsonLD/blob/master/JsonLD.php
      if ($outputFormat == 'application/ld+json') {
        echo json_encode(json_decode($graph->serialise($outputFormats[$outputFormat][1])), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
      }
      else {
        echo $graph->serialise($outputFormats[$outputFormat][1]);
      }
// To be used when JSON-LD pretty-print will be supported in EasyRdf (see previous comment)      
//      echo $graph->serialise($outputFormats[$outputFormat][1]);
      exit;
    }

  }


// The API

  $apiSrcRep = "https://github.com/SEMICeu/csw-4-web/tree/master/api";

// Overwriting / appending values for some of the variables for API landing page.

  $title = "CSW-4-Web GeoDCAT-AP API";
  $subtitle = "ISO 19139 records in RDF";

  $exampleSrcURL = "http://sdi.eea.europa.eu/catalogue/srv/eng/csw?request=GetRecords&service=CSW&version=2.0.2&namespace=xmlns%28csw=http://www.opengis.net/cat/csw%29&resultType=results&outputSchema=http://www.isotc211.org/2005/gmd&outputFormat=application/xml&typeNames=csw:Record&elementSetName=full&constraintLanguage=CQL_TEXT&constraint_language_version=1.1.0&maxRecords=20";

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
      <form id="api" action="./index.php" method="get">
        <h2>
          <label for="outputSchema">Output Schema : </label>
          <select id="outputSchema" name="outputSchema">
<?php

  foreach ($outputSchemas as $k => $v) {
    $selected = '';
    if ($k == $defaultOutputSchema) {
      $selected = ' selected="selected"';
    }
    echo '            <option value="' . $k . '"' . $selected . '>' . $v['label'] . '</option>' . "\n";
  }

?>
          </select>
          <input style="float:right" type="submit" id="transform" value="Transform"/>
        </h2>
        <p><input type="url" required="required" id="src" name="src" title="Copy &amp; paste the URL of ISO 19139 records" placeholder="Copy &amp; paste the URL of ISO 19139 records" value="<?php echo $exampleSrcURL?>"/></p>
        <p style="float:right;">
          <label for="outputFormat">Output format : </label>
          <select id="outputFormat" name="outputFormat">
<?php

  foreach ($outputFormats as $k => $v) {
    $selected = '';
    if ($k == $defaultOutputFormat) {
      $selected = ' selected="selected"';
    }
    echo '            <option value="' . $k . '"' . $selected . '>' . $v[0] . '</option>' . "\n";
  }

?>
          </select>
        </p>
      </form>
    </section>
    <section>
      <h2>Usage notes</h2>
      <p>This is a customised version of the <a href="https://github.com/SEMICeu/iso-19139-to-dcat-ap/tree/master/api">GeoDCAT-AP API</a> is used by CSW-4-Web to convert the source metadata into HTML and RDF serialisations, following the <a href="https://joinup.ec.europa.eu/solution/geodcat-application-profile-data-portals-europe">GeoDCAT-AP specification</a>.</p>
      <p>Supported CSW request types: <code>GetCapabilities</code>, <code>GetRecords</code>, <code>GetRecordById</code>.</p>
      <p>Supported CSW output schema: <code>http://www.isotc211.org/2005/gmd</code>, <code>http://www.opengis.net/ows</code></p>
      <p><em>A description of the <?php echo $title; ?> is available on the <a href="<?php echo $apiSrcRep; ?>">dedicated GitHub repository</a>.</em></p>
    </section>
    </article>
    <aside>
    </aside>
    <footer><?php echo $footer; ?></footer>
  </body>
</html>
