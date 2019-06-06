<?xml version="1.0" encoding="utf-8" ?>

<!--  

  Copyright 2015-2018 EUROPEAN UNION
  Licensed under the EUPL, Version 1.1 or - as soon they will be approved by
  the European Commission - subsequent versions of the EUPL (the "Licence");
  You may not use this work except in compliance with the Licence.
  You may obtain a copy of the Licence at:
 
  http://ec.europa.eu/idabc/eupl
 
  Unless required by applicable law or agreed to in writing, software
  distributed under the Licence is distributed on an "AS IS" basis,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the Licence for the specific language governing permissions and
  limitations under the Licence.
 
  Authors: European Commission, Joint Research Centre (JRC)
           Andrea Perego <andrea.perego@ec.europa.eu>
 
-->

<!--

  PURPOSE AND USAGE

  This XSLT is a customised version of the DCAT-AP in HTML+RDFa XSLT, revised 
  for its use with the CSW-4-Web API (https://github.com/SEMICeu/csw-4-web).

  As such, this XSLT must be considered as unstable, and can be updated any
  time based on the revisions to the GeoDCAT-AP XSLT and the CSW-4-Web API.

-->

<xsl:transform
    xmlns:dcat    = "http://www.w3.org/ns/dcat#"
    xmlns:dcterms = "http://purl.org/dc/terms/"
    xmlns:dctype  = "http://purl.org/dc/dcmitype/"
    xmlns:foaf    = "http://xmlns.com/foaf/0.1/"
    xmlns:rdf     = "http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rdfs    = "http://www.w3.org/2000/01/rdf-schema#"
    xmlns:vcard   = "http://www.w3.org/2006/vcard/ns#"
    xmlns:xsl     = "http://www.w3.org/1999/XSL/Transform"
    version="1.0">

  <xsl:output method="html"
              doctype-system="about:legacy-compact"
              media-type="text/html"
              omit-xml-declaration="yes"
              encoding="UTF-8"
              exclude-result-prefixes="#all"
              indent="yes" />

<!-- Global parameters -->  

<!-- The URL of the repository hosting the XSLT source code -->

  <xsl:param name="home">https://github.com/SEMICeu/dcat-ap-rdf2html</xsl:param>
  
  <xsl:param name="request"/>
  <xsl:param name="q"/>
  <xsl:param name="resource-nr"/>
  <xsl:param name="maxitems"/>
  <xsl:param name="summary"/>
  <xsl:param name="details"/>
  <xsl:param name="page" select="1"/>
  <xsl:param name="max-pages" select="2"/>

  <xsl:param name="include-home-link"/>

<!-- The title of the resulting HTML page. 
     This information can  be passed as a parameter by the XSLT 
     processor used. -->

  <xsl:param name="title">
    <xsl:choose>
      <xsl:when test="/rdf:RDF/rdf:Description[@rdf:about='']">
        <xsl:choose>
          <xsl:when test="/rdf:RDF/rdf:Description/dcterms:title">
            <xsl:value-of select="/rdf:RDF/rdf:Description/dcterms:title"/>
          </xsl:when>
          <xsl:when test="/rdf:RDF/rdf:Description/rdfs:label">
            <xsl:value-of select="/rdf:RDF/rdf:Description/rdfs:label"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text>DCAT-AP in HTML+RDFa</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:text>DCAT-AP in HTML+RDFa</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  <xsl:param name="subtitle"/>
  <xsl:param name="logo"/>
  <xsl:param name="logotitle"/>
  <xsl:param name="logourl"/>

<!-- Use this parameter to specify the set of LINK, STYLE, SCRIPT
     elements to be added to the HEAD of the resulting HTML document.
     This information can  be passed as a parameter by the XSLT 
     processor used. -->

  <xsl:param name="head"/>
  <xsl:param name="alt-formats"/>

<!-- The header of the resulting HTML page.  -->

  <xsl:param name="header">
    <xsl:if test="$logo != ''">
      <div class="logo"><a href="{$logourl}" target="_blank"><img src="{$logo}" title="{$logotitle}" width="90" height="90"/></a></div>
    </xsl:if>
    <h1><xsl:value-of select="$title"/></h1>
    <p class="subtitle"><xsl:value-of select="$subtitle"/></p>
  </xsl:param>

<!-- The footer of the resulting HTML page. 
     This information can  be passed as a parameter by the XSLT 
     processor used. -->

  <xsl:param name="footer">
    <p><xsl:value-of select="$title"/><xsl:text> @ GitHub: </xsl:text><a href="{$home}"><xsl:value-of select="$home"/></a></p>
  </xsl:param>

<!-- Namespace URIs -->

  <xsl:param name="rdf">http://www.w3.org/1999/02/22-rdf-syntax-ns#</xsl:param>

<!-- Class to be used to type untyped blank nodes -->  
  
  <xsl:param name="bnodeClass">
    <rdfs:Resource/>
  </xsl:param>
  <xsl:param name="bnodeClassName" select="name(document('')/*/xsl:param[@name='bnodeClass']/*)"/>
  <xsl:param name="bnodeClassURI" select="concat(namespace-uri(document('')/*/xsl:param[@name='bnodeClass']/*),local-name(document('')/*/xsl:param[@name='bnodeClass']/*))"/>

<!-- Main template -->  
  
  <xsl:template match="/">
  
    <xsl:param name="DatasetNr" select="count(rdf:RDF/dcat:Dataset|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Dataset'])"/>
    <xsl:param name="ServiceNr" select="count(rdf:RDF/dcat:Catalog|rdf:RDF/dctype:Service|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Catalog' or rdf:type/@rdf:resource='http://purl.org/dc/dcmitype/Service'])"/>
    <xsl:param name="ResourceNr"><xsl:value-of select="number($DatasetNr)+number($ServiceNr)"/></xsl:param>

<html>
  <head>
    <title><xsl:value-of select="$title"/></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <xsl:value-of select="$head" disable-output-escaping="yes"/>
  </head>
  <body>
    <header>
      <xsl:value-of select="$header" disable-output-escaping="yes"/>
    </header>
    <nav role="navigation">
<!--    
      <ol class="breadcrumb">
        <li><a href="..">Service</a></li>
        <li><a href=".">Resources</a></li> 
      </ol>
-->      
      <ul class="nav nav-tabs">
        <xsl:choose>
          <xsl:when test="$request = 'service'">
            <xsl:if test="$include-home-link = 'yes'">
              <li role="presentation"><a href=".."><span class="glyphicon glyphicon-home"></span></a></li>
            </xsl:if>
            <li role="presentation" class="active"><a href=".">Service</a></li>
            <li role="presentation"><a href="./resource/">Resources</a></li> 
          </xsl:when>
          <xsl:when test="$request = 'resource-list'">
            <xsl:if test="$include-home-link = 'yes'">
              <li role="presentation"><a href="../.."><span class="glyphicon glyphicon-home"></span></a></li>
            </xsl:if>
            <li role="presentation"><a href="..">Service</a></li>
            <li role="presentation" class="active"><a href=".">Resources</a></li> 
          </xsl:when>
          <xsl:when test="$request = 'resource'">
            <xsl:if test="$include-home-link = 'yes'">
              <li role="presentation"><a href="../.."><span class="glyphicon glyphicon-home"></span></a></li>
            </xsl:if>
            <li role="presentation"><a href="..">Service</a></li>
            <li role="presentation"><a href=".">Resources</a></li> 
          </xsl:when>
        </xsl:choose>
      </ul>
    </nav>
    <article>
    <section>
    <xsl:if test="$request = 'resource-list'">
    <section>
      <form name="search" id="search" role="search" method="get" action=".">
        <div class="input-group input-group-lg">
          <input class="form-control" name="q" id="q" type="text" placeholder="Search for..." value="{$q}"/>
          <span class="input-group-btn">
            <button type="submit" class="btn btn-default" aria-label="Search">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
            </button>        
<!--          
            <input class="form-control" type="submit" value="Search"/>
-->            
          </span>
        </div>
      </form>
      <h2>
        <xsl:choose>
          <xsl:when test="$resource-nr = ''">
            <xsl:text>No resources found</xsl:text>
          </xsl:when>
          <xsl:when test="$resource-nr = 1">
            <xsl:value-of select="format-number($resource-nr, '#,###')"/><xsl:text> resource found</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="format-number($resource-nr, '#,###')"/><xsl:text> resources found</xsl:text>
          </xsl:otherwise>
        </xsl:choose>      
      </h2>
<!--    
      <h2>Summary</h2>
      <dl>
        <dt>Datasets</dt>
        <dd><xsl:value-of select="$DatasetNr"/></dd>
        <dt>Services</dt>
        <dd><xsl:value-of select="$ServiceNr"/></dd>
      </dl>
-->    
    </section>
    </xsl:if>
    <xsl:apply-templates select="rdf:RDF/dcat:Dataset|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Dataset']"/>
    <xsl:apply-templates select="rdf:RDF/dcat:Catalog|rdf:RDF/dctype:Service|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Catalog' or rdf:type/@rdf:resource='http://purl.org/dc/dcmitype/Service']"/>
<!--    
    <section>
      <h2>Datasets (<xsl:value-of select="count(rdf:RDF/dcat:Dataset|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Dataset'])"/>)</h2>
      <xsl:apply-templates select="rdf:RDF/dcat:Dataset|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Dataset']"/>
    </section>
    <section>
      <h2>Services (<xsl:value-of select="count(rdf:RDF/dcat:Catalog|rdf:RDF/dctype:Service|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Catalog' or rdf:type/@rdf:resource='http://purl.org/dc/dcmitype/Service'])"/>)</h2>
      <xsl:apply-templates select="rdf:RDF/dcat:Catalog|rdf:RDF/dctype:Service|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Catalog' or rdf:type/@rdf:resource='http://purl.org/dc/dcmitype/Service']"/>
    </section>
-->    
    </section>
    </article>
    <aside>
    </aside>
    <xsl:if test="$request = 'resource-list'">
    <nav aria-label="Page navigation" class="text-center">
      <xsl:variable name="page-nr" select="(number($resource-nr) - (number($resource-nr) mod number($maxitems))) div number($maxitems) + 1"/>
      <xsl:variable name="active-page">
        <xsl:choose>
          <xsl:when test="number($page) &lt; 0">
            <xsl:value-of select="1"/>
          </xsl:when>
          <xsl:when test="number($page) &gt; number($page-nr)">
            <xsl:value-of select="number($page-nr)"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="number($page)"/>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:variable>
      
      <ul class="pagination">

        <xsl:variable name="query">
          <xsl:if test="normalize-space($q) != ''">
            <xsl:value-of select="concat('q=',$q,'&amp;')"/>
          </xsl:if>
        </xsl:variable>

        <xsl:call-template name="pages">
          <xsl:with-param name="page-nr" select="$page-nr"/>
          <xsl:with-param name="active-page" select="$active-page"/>
          <xsl:with-param name="query" select="$query"/>
<!--          
          <xsl:with-param name="this-page" select="1"/>
-->          
        </xsl:call-template>
        
      </ul>
    </nav>
    </xsl:if>
    <footer>
      <xsl:value-of select="$footer" disable-output-escaping="yes"/>
    </footer>
  </body>
</html>
    
  </xsl:template>
  
  
<xsl:template name="pages">
  <xsl:param name="page-nr"/>
  <xsl:param name="active-page"/>
  <xsl:param name="query"/>
<!--  
  <xsl:param name="this-page"/>
-->  
  <xsl:param name="start-page">
    <xsl:variable name="sp" select="number($active-page) - number($max-pages)"/>
    <xsl:choose>
      <xsl:when test="number($sp) &lt; 1">
        <xsl:value-of select="1"/>
      </xsl:when>
      <xsl:otherwise>
<!--      
        <xsl:choose>
          <xsl:when test="number($sp) - number($page-nr) &lt; 2 * number($max-pages) + 1 + 1">
            <xsl:value-of select="number($active-page) - (2 * number($max-pages) + 1 + 1) - (number($sp) - number($page-nr))"/>
          </xsl:when>
          <xsl:otherwise>
-->          
            <xsl:value-of select="number($sp)"/>
<!--            
          </xsl:otherwise>
        </xsl:choose>
-->        
      </xsl:otherwise>
    </xsl:choose>
<!--
    <xsl:value-of select="number($active-page) - number($max-pages)"/>
-->
<!--
    <xsl:choose>
      <xsl:when test="number($page-nr) = 0 or number($page-nr) &lt; 0">
        <xsl:value-of select="0"/>
      </xsl:when>
-->
<!--      
      <xsl:when test="(number($active-page)- number($page-nr)) &lt; number($max-pages)">
        <xsl:value-of select="(number($page-nr) - number($max-pages))"/>
      </xsl:when>
-->
<!--      
      <xsl:otherwise>
        <xsl:value-of select="(number($active-page) - (number($active-page) mod number($max-pages)) + 1)"/>
      </xsl:otherwise>
-->
<!--
      <xsl:when test="(number($page-nr) div number($max-pages) &gt; 1) and (number($active-page) div number($max-pages) &gt; 1)">
        <xsl:value-of select="(number($active-page) - (number($active-page) mod number($max-pages))) + 1"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="1"/>
      </xsl:otherwise>
-->      
<!--
    </xsl:choose>
-->
  </xsl:param>
  <xsl:param name="this-page" select="number($start-page)"/>
  <xsl:param name="end-page">
    <xsl:variable name="ep" select="number($active-page) + number($max-pages)"/>
    <xsl:choose>
      <xsl:when test="number($ep) &gt; $page-nr">
        <xsl:value-of select="$page-nr"/>
      </xsl:when>
      <xsl:otherwise>
<!--      
        <xsl:choose>
          <xsl:when test="number($ep) &lt; 2 * number($max-pages) + 1 + 3">
            <xsl:value-of select="2 * number($max-pages) + 1 + 3"/>
          </xsl:when>
          <xsl:otherwise>
-->          
            <xsl:value-of select="number($ep)"/>
<!--            
          </xsl:otherwise>
        </xsl:choose>
-->        
      </xsl:otherwise>
    </xsl:choose>
<!--
    <xsl:choose>
      <xsl:when test="number($page-nr) = 0 or number($page-nr) &lt; 0">
        <xsl:value-of select="0"/>
      </xsl:when>
      <xsl:when test="number($page-nr) &gt; number($this-page)">
        <xsl:choose>
          <xsl:when test="number($page-nr) &gt; (number($start-page) + number($max-pages))">
            <xsl:value-of select="(number($start-page) + number($max-pages) - 1)"/>
          </xsl:when>
-->          
    <!--      
          <xsl:when test="number($page-nr) &gt; (number($start-page) + (number($this-page) mod number($max-pages)) - 1)">
            <xsl:value-of select="(number($start-page) + (number($this-page) mod number($max-pages)) - 1)"/>
          </xsl:when>
    -->      
<!--    
          <xsl:otherwise>
            <xsl:value-of select="$page-nr"/>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$page-nr"/>
      </xsl:otherwise>
    </xsl:choose>
-->    
  </xsl:param>

<!--
  <xsl:value-of select="concat('Mod page: ',number($this-page) mod number($max-pages))"/>
  <xsl:value-of select="concat('Page nr: ',$page-nr)"/>
  <xsl:value-of select="concat('Start page: ',$start-page)"/>
  <xsl:value-of select="concat('This page: ',$this-page)"/>
  <xsl:value-of select="concat('End page: ',$end-page)"/>
-->
  <xsl:if test="number($page-nr) &gt; 0">

  <xsl:if test="number($this-page) = number($start-page)">
    <xsl:choose>
      <xsl:when test="number($start-page) = 1">
        <li class="disabled"><a href="./?{$query}page={number($start-page)}" aria-label="Previous"><span aria-hidden="true">&#xab;</span></a></li>
      </xsl:when>
      <xsl:otherwise>
        <li><a href="./?{$query}page={number($active-page) - 1}" aria-label="Previous"><span aria-hidden="true">&#xab;</span></a></li>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="number($active-page) = 1">
        <li class="active"><a href="./?{$query}page=1">1</a></li>
      </xsl:when>
      <xsl:otherwise>
        <li><a href="./?{$query}page=1">1</a></li>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="number($start-page) = 3">
          <li><a href="./?{$query}page=2">2</a></li>
      </xsl:when>
      <xsl:when test="number($start-page) &gt; number($max-pages)">
        <li class="disabled"><a href="./?{$query}page={number($this-page) - 1}">...</a></li>
      </xsl:when>
    </xsl:choose>
  </xsl:if>
<!--        
  <xsl:value-of select="concat('Mod page: ',number($this-page) mod number($max-pages))"/>
  <xsl:value-of select="concat('Page nr: ',$page-nr)"/>
  <xsl:value-of select="concat('Start page: ',$start-page)"/>
  <xsl:value-of select="concat('This page: ',$this-page)"/>
  <xsl:value-of select="concat('End page: ',$end-page)"/>
-->
<!--  
    <xsl:if test="number($start-page) div number($max-pages) &gt; 1 and number($start-page) = number($this-page)">
      <li><a href="./?{$query}page={number($start-page) - 1}">...</a></li>
    </xsl:if>
-->
    <xsl:if test="number($end-page) &gt;= number($this-page)">
      <xsl:if test="number($this-page) &gt; 1 and number($this-page) &lt; number($page-nr)">
      <xsl:choose>
        <xsl:when test="number($this-page) = number($active-page)">
          <li class="active"><a href="./?{$query}page={$this-page}"><xsl:value-of select="format-number($this-page, '#,###')"/></a></li>
        </xsl:when>
        <xsl:otherwise>
          <li><a href="./?{$query}page={$this-page}"><xsl:value-of select="format-number($this-page, '#,###')"/></a></li>
        </xsl:otherwise>
      </xsl:choose>
      </xsl:if>
    </xsl:if>
<!--
    <xsl:if test="number($end-page) &lt; number($page-nr) and number($end-page) = number($this-page)">
      <li><a href="./?{$query}page={number($end-page) + 1}">...</a></li>
    </xsl:if>
-->
    <xsl:if test="number($this-page) = number($end-page)">
      <xsl:choose>
        <xsl:when test="number($end-page) = (number($page-nr) - 2)">
          <li><a href="./?{$query}page={number($page-nr) - 1}"><xsl:value-of select="format-number((number($page-nr) - 1), '#,###')"/></a></li>
        </xsl:when>
        <xsl:when test="number($end-page) &lt; (number($page-nr) - 1)">
          <li class="disabled"><a href="./?{$query}page={number($this-page) + 1}">...</a></li>
        </xsl:when>
      </xsl:choose>
      <xsl:choose>
        <xsl:when test="number($active-page) = $page-nr">
          <xsl:if test="$page-nr &gt; 1">
          <li class="active"><a href="./?{$query}page={$page-nr}"><xsl:value-of select="format-number($page-nr, '#,###')"/></a></li>
          </xsl:if>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="$page-nr &gt; 1">
            <li><a href="./?{$query}page={$page-nr}"><xsl:value-of select="format-number($page-nr, '#,###')"/></a></li>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:choose>
        <xsl:when test="number($active-page) &gt;= number($page-nr)">
          <li class="disabled"><a href="./?{$query}page={$page-nr}" aria-label="Next"><span aria-hidden="true">&#xbb;</span></a></li>
        </xsl:when>
        <xsl:otherwise>
          <li><a href="./?{$query}page={number($active-page) + 1}" aria-label="Next"><span aria-hidden="true">&#xbb;</span></a></li>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:if>

    <xsl:if test="number($end-page) &gt; number($this-page)">
      <xsl:call-template name="pages">
        <xsl:with-param name="page-nr" select="number($page-nr)"/>
        <xsl:with-param name="this-page" select="number($this-page) + 1"/>
        <xsl:with-param name="active-page" select="$active-page"/>
        <xsl:with-param name="query" select="$query"/>
      </xsl:call-template>
    </xsl:if>
    
  </xsl:if>
</xsl:template>  

  <xsl:template name="Dataset" match="rdf:RDF/dcat:Dataset|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Dataset']">  
    <section class="record">
      <xsl:if test="$request != 'resource-list'">
        <div>Alternative formats: <xsl:value-of select="$alt-formats" disable-output-escaping="yes"/></div>
      </xsl:if>
      <xsl:choose>
        <xsl:when test="$request = 'resource-list'">
          <h2><a href="{@rdf:about}">Dataset: <span xml:lang="{dcterms:title[normalize-space(.) != '']/@xml:lang}" lang="{dcterms:title[normalize-space(.) != '']/@xml:lang}"><xsl:value-of select="dcterms:title[normalize-space(.) != '']"/></span></a></h2>
        </xsl:when>
        <xsl:otherwise>
          <h2>Dataset: <span xml:lang="{dcterms:title[normalize-space(.) != '']/@xml:lang}" lang="{dcterms:title[normalize-space(.) != '']/@xml:lang}"><xsl:value-of select="dcterms:title[normalize-space(.) != '']"/></span></h2>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:call-template name="Agent"/>
      <p xml:lang="{dcterms:description[normalize-space(.) != '']/@xml:lang}" lang="{dcterms:description[normalize-space(.) != '']/@xml:lang}">
        <xsl:choose>
          <xsl:when test="$request = 'resource-list' and string-length(dcterms:description[normalize-space(.) != '']) &gt; 400">
            <xsl:value-of select="concat(substring(dcterms:description[normalize-space(.) != ''],1,400),' ... ')"/>
            <a href="{@rdf:about}">(read more)</a>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="dcterms:description[normalize-space(.) != '']"/>
          </xsl:otherwise>
        </xsl:choose>
      </p>
      <xsl:if test="$request != 'resource-list'">
        <xsl:call-template name="metadata"/>
      </xsl:if>
    </section>
  </xsl:template>

  <xsl:template name="Service" match="rdf:RDF/dcat:Catalog|rdf:RDF/dctype:Service|rdf:RDF/rdf:Description[rdf:type/@rdf:resource='http://www.w3.org/ns/dcat#Catalog' or rdf:type/@rdf:resource='http://purl.org/dc/dcmitype/Service']">
    <section class="record">
      <xsl:if test="$request != 'resource-list'">
        <div>Alternative formats: <xsl:value-of select="$alt-formats" disable-output-escaping="yes"/></div>
      </xsl:if>
      <xsl:choose>
        <xsl:when test="$request = 'resource-list'">
          <h2><a href="{@rdf:about}">Service: <span xml:lang="{dcterms:title[normalize-space(.) != '']/@xml:lang}" lang="{dcterms:title[normalize-space(.) != '']/@xml:lang}"><xsl:value-of select="dcterms:title[normalize-space(.) != '']"/></span></a></h2>
        </xsl:when>
        <xsl:otherwise>
          <h2>Service: <span xml:lang="{dcterms:title[normalize-space(.) != '']/@xml:lang}" lang="{dcterms:title[normalize-space(.) != '']/@xml:lang}"><xsl:value-of select="dcterms:title[normalize-space(.) != '']"/></span></h2>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:call-template name="Agent"/>
      <p xml:lang="{dcterms:description[normalize-space(.) != '']/@xml:lang}" lang="{dcterms:description[normalize-space(.) != '']/@xml:lang}">
        <xsl:choose>
          <xsl:when test="$request = 'resource-list' and string-length(dcterms:description[normalize-space(.) != '']) &gt; 400">
            <xsl:value-of select="concat(substring(dcterms:description[normalize-space(.) != ''],1,400),' ... ')"/>
            <a href="{@rdf:about}">(read more)</a>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="dcterms:description[normalize-space(.) != '']"/>
          </xsl:otherwise>
        </xsl:choose>
      </p>
      <xsl:if test="$request != 'resource-list'">
        <xsl:call-template name="metadata"/>
      </xsl:if>
    </section>
  </xsl:template>
  
  <xsl:template name="Agent">
    <address>
      <dl>
        <xsl:for-each select="dcterms:publisher">
          <xsl:variable name="org" select="*/foaf:name"/>
          <xsl:variable name="email" select="*/foaf:mbox/@rdf:resource"/>
          <xsl:variable name="url" select="*/foaf:workplaceHomepage/@rdf:resource"/>
          <xsl:variable name="name" select="$org"/>
          <dt>Publisher</dt>
          <dd>
          <xsl:choose>
            <xsl:when test="$url != ''">
              <a href="{$url}"><xsl:value-of select="$name"/></a>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="$name"/>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:if test="$email != ''">
            <xsl:text> (</xsl:text><a href="{$email}"><xsl:value-of select="substring-after($email, 'mailto:')"/></a><xsl:text>)</xsl:text>
          </xsl:if>
          </dd>
        </xsl:for-each>
        <xsl:for-each select="dcat:contactPoint">
          <xsl:variable name="individual" select="*/vcard:fn|vcard:fn"/>
          <xsl:variable name="org" select="*/vcard:organization-name|vcard:organization-name"/>
          <xsl:variable name="email" select="*/vcard:hasEmail/@rdf:resource|vcard:hasEmail/@rdf:resource"/>
          <xsl:variable name="url" select="*/vcard:hasURL/@rdf:resource|vcard:hasURL/@rdf:resource"/>
          <xsl:variable name="name">
            <xsl:choose>
              <xsl:when test="$individual != '' and $org != ''">
                <xsl:value-of select="$individual"/>
                <xsl:text>, </xsl:text>
                <xsl:value-of select="$org"/>
              </xsl:when>
              <xsl:otherwise>
                <xsl:value-of select="$individual"/>
                <xsl:value-of select="$org"/>
              </xsl:otherwise>
            </xsl:choose>
          </xsl:variable>
          <dt>Contact point</dt>
          <dd>
          <xsl:choose>
            <xsl:when test="$url != ''">
              <a href="{$url}"><xsl:value-of select="$name"/></a>
            </xsl:when>
            <xsl:otherwise>
              <xsl:value-of select="$name"/>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:if test="$email != ''">
            <xsl:text> (</xsl:text><a href="{$email}"><xsl:value-of select="substring-after($email, 'mailto:')"/></a><xsl:text>)</xsl:text>
          </xsl:if>
          </dd>
        </xsl:for-each>
      </dl>
    </address>
  </xsl:template>
  
  <xsl:template name="metadata">
    <section id="about" class="metadata">
      <h3>Metadata</h3>
        <details>
          <summary>Details</summary>
          <xsl:call-template name="subject"/>
        </details>
    </section>
  </xsl:template>

  <xsl:template name="subject">
    <xsl:param name="ename">
      <xsl:call-template name="setEname"/>  
    </xsl:param>
    <xsl:param name="predicate">
      <xsl:call-template name="label"/>
      <dd>
      <xsl:for-each select="*">
        <xsl:call-template name="predicate"/>
      </xsl:for-each>
      </dd>
    </xsl:param>
    <xsl:choose>
      <xsl:when test="@rdf:about">
        <dl about="{@rdf:about}" typeof="{$ename}">
          <xsl:copy-of select="$predicate"/>
        </dl>
      </xsl:when>
      <xsl:when test="@rdf:ID">
        <dl about="#{@rdf:ID}" typeof="{$ename}">
          <xsl:copy-of select="$predicate"/>
        </dl>
      </xsl:when>
      <xsl:when test="@rdf:nodeID">
        <dl typeof="{$ename}">
          <xsl:copy-of select="$predicate"/>
        </dl>
      </xsl:when>
      <xsl:otherwise>
        <dl typeof="{$ename}">
          <xsl:copy-of select="$predicate"/>
        </dl>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="predicate">
    <xsl:param name="ename">
      <xsl:call-template name="setEname"/>  
    </xsl:param>
    <xsl:choose>
<!-- Object properties -->    
      <xsl:when test="(child::* and not(@rdf:parseType='Literal')) or @rdf:resource">
        <dl rel="{$ename}">
          <xsl:call-template name="label"/>
          <xsl:choose>
            <xsl:when test="@rdf:resource">
              <dd><a resource="{@rdf:resource}" href="{@rdf:resource}"><xsl:value-of select="@rdf:resource"/></a></dd>
            </xsl:when>
            <xsl:when test="@rdf:parseType = 'Resource'">
              <dd>
                <dl typeof="{$bnodeClassURI}">
                  <dt><xsl:value-of select="$bnodeClassName"/></dt>
                  <dd>
                  <xsl:for-each select="*">
                    <xsl:call-template name="predicate"/>
                  </xsl:for-each>
                  </dd>
                </dl>
              </dd>
            </xsl:when>
            <xsl:otherwise>
              <dd>
                <xsl:for-each select="*">
                  <xsl:call-template name="subject"/>
                </xsl:for-each>
              </dd>
            </xsl:otherwise>
          </xsl:choose>
        </dl>
      </xsl:when>
<!-- Datatype properties -->      
      <xsl:otherwise>
        <dl>
          <xsl:call-template name="label"/>
          <xsl:choose>
            <xsl:when test="@xml:lang">
              <dd property="{$ename}" content="{.}" xml:lang="{@xml:lang}" lang="{@xml:lang}"><xsl:value-of select="."/></dd>
            </xsl:when>
            <xsl:when test="@rdf:datatype">
              <dd property="{$ename}" content="{.}" datatype="{@rdf:datatype}"><code><xsl:value-of select="."/></code></dd>
            </xsl:when>
            <xsl:when test="@rdf:parseType = 'Literal'">
              <dd property="{$ename}" content="{.}" datatype="{$rdf}XMLLiteral"><code><xsl:value-of select="."/></code></dd>
            </xsl:when>
            <xsl:otherwise>
              <dd property="{$ename}" content="{.}"><xsl:value-of select="."/></dd>
            </xsl:otherwise>
          </xsl:choose>
        </dl>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template name="label">
    <xsl:param name="qname">
      <xsl:call-template name="setQname"/>  
    </xsl:param>
    <xsl:choose>
<!-- Object properties & individuals -->
      <xsl:when test="child::* or @rdf:about or @rdf:resource">
        <xsl:choose>
          <xsl:when test="@rdf:parseType">
            <dt><xsl:value-of select="$qname"/></dt>
          </xsl:when>
          <xsl:when test="@rdf:nodeID">
            <dt><xsl:value-of select="$qname"/></dt>
          </xsl:when>
          <xsl:when test="@rdf:about">
            <dt><a href="{@rdf:about}"><xsl:value-of select="@rdf:about"/></a><xsl:text> (</xsl:text><xsl:value-of select="$qname"/><xsl:text>)</xsl:text></dt>
          </xsl:when>
          <xsl:when test="@rdf:ID">
            <dt><a href="#{@rdf:ID}"><xsl:value-of select="concat('#',@rdf:about)"/></a><xsl:text> (</xsl:text><xsl:value-of select="$qname"/><xsl:text>)</xsl:text></dt>
          </xsl:when>
          <xsl:otherwise>
            <dt><xsl:value-of select="$qname"/></dt>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>
<!-- Datatype properties, plain and typed literals -->      
      <xsl:otherwise>
        <xsl:choose>
          <xsl:when test="@rdf:datatype">
            <dt><xsl:value-of select="$qname"/><xsl:text> (</xsl:text><a href="{@rdf:datatype}"><xsl:value-of select="@rdf:datatype"/></a><xsl:text>)</xsl:text></dt>
          </xsl:when>
          <xsl:when test="@xml:lang">
            <dt><xsl:value-of select="$qname"/><xsl:text> (</xsl:text><xsl:value-of select="@xml:lang"/><xsl:text>)</xsl:text></dt>
          </xsl:when>
          <xsl:otherwise>
            <dt><xsl:value-of select="$qname"/></dt>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template name="setQname">
    <xsl:choose>
      <xsl:when test="name(.) = 'rdf:Description'">
        <xsl:value-of select="$bnodeClassName"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="name(.)"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="setEname">
    <xsl:choose>
      <xsl:when test="name(.) = 'rdf:Description'">
        <xsl:value-of select="$bnodeClassURI"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="concat(namespace-uri(.),local-name(.))"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:transform>
