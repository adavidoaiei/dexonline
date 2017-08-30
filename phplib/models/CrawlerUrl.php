<?php

class CrawlerUrl extends BaseObject implements DatedObject {
  static $_table = 'CrawlerUrl';

  private $siteId = null;
  private $rawHtml = null;
  private $parser = null;
  private $body = null;

  static function create($url, $siteId) {
    $cu = Model::factory('CrawlerUrl')->create();
    $cu->url = $url;
    $cu->siteId = $siteId;
    return $cu;
  }

  // fetches the URL and instantiates a parser
  function fetch() {
    $this->rawHtml = @file_get_contents($this->url);
    if (!$this->rawHtml) {
      throw new CrawlerException("could not fetch {$this->url}");
    }

    $this->parser = str_get_html($this->rawHtml);
  }

  function extractAuthor($selector, $regexp) {
    $authors = $this->parser->find($selector);

    if (count($authors) > 1) {
      throw new CrawlerException('expected 1 author, got ' . count($authors));
    } else if (!empty($authors)) {
      $authorWrapper = $authors[0]->plaintext;

      if (!preg_match($regexp, $authorWrapper, $matches)) {
        throw new CrawlerException("Cannot extract author from string [{$authorWrapper}]");
      }

      $this->author = $matches[1];
    }
  }

  function extractTitle($selector) {
    $titles = $this->parser->find($selector);

    if (count($titles) != 1) {
      throw new CrawlerException('expected 1 title, got ' . count($titles));
    }

    $titleWrapper = $titles[0];

    // strip away all the children nodes
    foreach ($titleWrapper->children() as $child ) {
      $child->outertext = '';
    }

    $this->title = trim($titleWrapper->innertext);
  }

  function extractBody($selector) {
    $bodies = $this->parser->find($selector);
    if (count($bodies) != 1) {
      throw new CrawlerException('expected 1 body, got ' . count($bodies));
    }
    $this->body = trim($bodies[0]->plaintext);
  }

  function fetchAndExtract($authorSelector, $authorRegexp, $titleSelector, $bodySelector) {
    $this->fetch();
    $this->extractAuthor($authorSelector, $authorRegexp);
    $this->extractTitle($titleSelector);
    $this->extractBody($bodySelector);
  }

  function saveData($data, $file) {
    if (!$this->id) {
      throw new CrawlerException('cannot save data before the CrawlerUrl object has an ID');
    }
    @mkdir(dirname($file), 0777, true);
    file_put_contents($file, $data);
  }

  function saveBody($path) {
    $fileName = sprintf('%s/%s/body/%s.txt', $path, $this->siteId, $this->id);
    $this->saveData($this->body, $fileName);
  }

  function saveHtml($path) {
    $fileName = sprintf('%s/%s/raw/%s.html', $path, $this->siteId, $this->id);
    $this->saveData($this->rawHtml, $fileName);
  }
}