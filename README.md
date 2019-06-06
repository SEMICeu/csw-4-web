# Purpose and usage

CSW-4-Web is a proof-of-concept API designed to expose a [CSW](http://www.opengeospatial.org/standards/cat) endpoint in a Web-friendly way, and enabling the exploration of its content without the need of specific client applications.

A working demo of the CSW-4-Web API is available at: 

http://geodcat-ap.semic.eu/csw-4-web/


## CSW-4-Web at a glance

Basically, CSW-4-Web turns a CSW endpoint into a Web site, consisting of three main sections:
1. The description of the service itself 
2. The list of resources available from the endpoint
3. The detailed description of each resource

These pages are generated via a customised version of the [GeoDCAT-AP API](https://github.com/SEMICeu/iso-19139-to-dcat-ap/tree/master/api), which converts the XML output of the relevant CSW calls into an HTML+RDFa representation - i.e., an HTML page, with metadata embedded as per the [W3C HTML+RDFa Recommendation](https://www.w3.org/TR/html-rdfa/). 

Alternative machine-readable representations are linked from the generated HTML pages, and also accessible via [HTTP content negotiation](https://tools.ietf.org/html/rfc7231#section-3.4).

## How it works

The HTML pages mentioned in the previous section are generated based on the output of the following CSW request types:
- `GetCapabilities` for the description of the service
- `GetRecords` for the list of resources available from the CSW endpoint
- `GetRecordById` for the pages describing each single resource

In order to achieve this, the CSW-4-Web API needs only the URL corresponding to the `GetCapabilities` request of a given CSW endpoint. Based on the available metadata, it automatically builds the relevant `GetRecords` and `GetRecordById` requests, and submits them to the GeoDCAT-AP API, which returns the HTML and RDF representations, as per the [GeoDCAT-AP specification](http://data.europa.eu/w21/c9dae5aa-c3d0-43a7-96e3-9f16cd8d5b6d).

The resulting representations of the CSW records are given URLs based on the following pattern:

````regex
/<service_ID>/(resource/(<resource_ID>)?)?(.<file_extension>)?
````

where:

- <var>service_ID</var> is a system-generated ID for the relevant CSW endpoint, mapping to the URL of the `GetCapabilities` request;
- <var>resource_ID</var> corresponds to the metadata file identifier of the relevant record, used as a parameter to the `GetRecordById` request.
- <var>file_extension</var> is an optional component, mapping to the media type of the requested serialisation, used as value of the CSW `outputFormat` parameter. If omitted, the serialisation to be returned is determined via HTTP content negotiation.

# API specification

Technically, CSW-4-Web is just a wrapper for the GeoDCAT-AP API, which maps a given URL pattern to CSW requests. 

The URL re-writing rules are specified in the [`.htaccess`](./.htaccess) file, and can be summarised as follows:

|URL|CSW request|
|---|---|
|`/<service_ID>/`|`GetCapabilities`|
|`/<service_ID>/resource/`|`GetRecords`|
|`/<service_ID>/resource/<resource_ID>`|`GetRecordById`|

The optional URL component <var>file_extension</var> can be appended to any of the above URL patterns to request a specific serialisation, which is otherwise determined via HTTP content negotiation.

The available serialisation are those supported by the GeoDCAT-AP API, plus `.xml`, which returns the original output of the relevant CSW request.

Besides this, CSW-4-Web is in charge of submitting additional parameters to the GeoDCAT-AP API, in order to fine tune the resulting output. Such parameters are not an essential part of the API, as they need to be changed depending on how you want to shape the output.

About the customisation of the GeoDCAT-AP API & XSLT used here, there are two notable enhancements that are needed for CSW-4-Web:
1. The definition of mapping rules for the output of `GetCapabilities` requests in the GeoDCAT-AP XSLT. Note that the standard GeoDCAT-AP XSLT defines mapping rules only for metadata encoded in ISO 19139. 
2. The ability to submit `GetRecords` requests to CSW endpoints supporting only the HTTP `POST` method. Note that the standard GeoDCAT-AP API supports only CSW requests using the HTTP `GET` method.

# Implementation details

CSW-4-Web is implemented in [PHP5](http://php.net/), and runs on top of an [Apache 2 HTTP server](http://httpd.apache.org/).

A customised version of the [GeoDCAT-AP API](https://github.com/SEMICeu/iso-19139-to-dcat-ap/tree/master/api) is used to generate the returned HTML and RDF representations. The actual transformation rules are defined in customised versions of:
- The [GeoDCAT-AP XSLT](https://github.com/SEMICeu/iso-19139-to-dcat-ap) (for the RDF representation).
- The [DCAT-AP in HTML+RDFa](https://github.com/SEMICeu/dcat-ap-rdf2html/) XSLT (for the HTML representation).

# Installation instructions

CSW-4-Web has been tested on both Linux and Windows, with Apache 2 and PHP 5.3.2 (or later) installed and running.

**NB**: CSW-4-Web makes use of the [PHP XSL extension](http://php.net/manual/en/xsl.installation.php) and requires the Apache [`mod_rewrite`](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) module to be enabled. Other directives are specified in the [`.htaccess`](./.htaccess) file.

The repository includes in folder [`api/`](./api/) the customised version of the GeoDCAT-AP API and of the XSLTs used to generate the HTML and RDF representations (available in folder [`./api/xslt/`](./api/xslt/)). 

The [EasyRDF](http://www.easyrdf.org/) and [ML/JSON-LD](https://github.com/lanthaler/JsonLD) PHP libraries used by the GeoDCAT-AP API must be installed separately by using [Composer](https://getcomposer.org/).

More precisely:

- Go to folder [`./api/lib/composer/`](./api/lib/composer/).
- [Download Composer](https://getcomposer.org/download/). E.g.: `curl -s https://getcomposer.org/installer | php`
- Run `php composer.phar install`

After having done that, you need to modify directive `RewriteBase` in the [`.htaccess`](./.htaccess) file, by specifying the absolute path from the document root to the CSW-4-Web folder:

````apache
RewriteBase /csw-4-web/
````

You will now be able to run the API from a Web folder.
