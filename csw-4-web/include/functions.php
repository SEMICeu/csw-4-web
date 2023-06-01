<?php

// HTTP codes & corresponding pages

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
      echo '<!DOCTYPE html><html><head><title>' . $title . '</title>' . str_replace("\n", "", $head) . '</head><body><header><h1>' . $title . '</h1></header><article><section><p>' . $content . '</p></section></article></body></html>';
      exit;
    }
  }

  function getRequestAsXML($params) {
  
    $p = array();
    
    foreach ($params as $k => $v) {
      $p[strtolower($k)] = $v;
    }

    switch (strtolower($p['request'])) {
      case 'getcapabilities':
        $RequestType = 'GetCapabilities';
        break;
      case 'getrecords':
        $RequestType = 'GetRecords';
        break;
      case 'getrecordbyid':
        $RequestType = 'GetRecordById';
        break;
    }
  
    $xml  = '<csw:' . $RequestType;
    $xml .= ' service="' . $p['service'] . '"';
    $xml .= ' version="' . $p['version'] . '"';
    if ($RequestType == 'GetRecords') {
      $xml .= ' maxRecords="' . $p['maxrecords'] . '"';
      $xml .= ' startPosition="' . $p['startposition'] . '"';
      $xml .= ' resultType="' . $p['resulttype'] . '"';
    }
    $xml .= ' outputFormat="' . $p['outputformat'] . '"';
    $xml .= ' outputSchema="' . $p['outputschema'] . '"';
    $xml .= ' xsi:schemaLocation="http://www.opengis.net/cat/csw/' . $p['version'] . '/CSW-discovery.xsd"';
    $xml .= ' xmlns="http://www.opengis.net/cat/csw/' . $p['version'] . '"';
    $xml .= ' xmlns:csw="http://www.opengis.net/cat/csw/' . $p['version'] . '"';
    $xml .= ' xmlns:ogc="http://www.opengis.net/ogc"';
    $xml .= ' xmlns:dc="http://purl.org/dc/elements/1.1/"';
    $xml .= ' xmlns:dct="http://purl.org/dc/terms/"';
    $xml .= ' xmlns:gmd="http://www.isotc211.org/2005/gmd"';
    $xml .= ' xmlns:gml="http://www.opengis.net/gml"';
    $xml .= ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
    if ($RequestType == 'GetRecords') {
      $xml .= '<ElementSetName typeNames="' . $p['typenames'] . '">' . $p['elementsetname'] . '</ElementSetName>';
      $xml .= '<Query typeNames="' . $p['typenames'] . '">';
      if (isset($p['constraint'])) {
        $xml .= '<Constraint version="' . $p['constraint_language_version'] . '">';
        switch (strtolower($p['constraintlanguage'])) {
          case 'filter':
            $xml .= '<ogc:Filter>';
            $xml .= '<ogc:PropertyIsLike>';
            $xml .= '<ogc:PropertyName>AnyText</ogc:PropertyName>';
            $xml .= '<ogc:Literal>' . explode("'", $p['constraint'])[1] . '</ogc:Literal>';
            $xml .= '</ogc:PropertyIsLike>';
            $xml .= '</ogc:Filter>';
            break;
          case 'cql_text':
            $xml .= '<CqlText>';
            $xml .= $p['constraint'];

            $xml .= '</CqlText>';
            break;
        }
        $xml .= '</Constraint>';
      }
      $xml .= '</Query>';
    }
    if ($RequestType == 'GetRecordById') {
      $xml .= '<ElementSetName>' . $p['elementsetname'] . '</ElementSetName>';
      $xml .= '<Id>' . $p['id'] . '</Id>';
    }
    $xml .= '</csw:' . $RequestType . '>';
    
    return $xml;
  
  }

?>
