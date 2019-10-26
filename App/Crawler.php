<?php
namespace App;

class Crawler{

    private $imagePath = 'images/';
    private $baseUrl;
    private $pageUrl;
    private $jsonFile = 'content.json';

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
    
    public function readFile($pageUrl)
    {
        return file_get_contents($this->setUrl($pageUrl));
    }

    public function putFile($path, $file)
    {
        return file_put_contents($path, $file);
    }
  
    public function downloadImage($url)
    {
        $fileName = basename($url);
        $saved_doc = $this->imagePath.$fileName;
        return $this->putFile($saved_doc, $this->readFile($url));
    }

    public function crawlContents($url)
    {
        $this->pageUrl = $url;
        $html = $this->readFile($url);
        preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $matchedImages ); 
        preg_match_all( '|<a.*?href=[\'"](.*?)[\'"].*?>|i',$html, $matchedLinks ); 
        
        $this->formatCrawledContent($matchedImages[1], $matchedLinks[1]);
    }
    
    public function formatCrawledContent($images, $links)
    {
        $formattedImages = $this->extractImages($images);
        $formattedLinks = $this->extractLinks($links);
        
        $results = [
            'page' => $this->setUrl($this->pageUrl),
            'total_images' => $formattedImages['total_images'] . ' images found',
            'total_links' => $formattedLinks['total_links'] . ' links found',
            'image_urls' => $formattedImages['images'],
            'link_urls' => $formattedLinks['links'],
        ];

        return $this->putFile($this->jsonFile, json_encode($results));
    }

    private function extractImages($images)
    {
        $array = [];
        foreach ($images as $image) {
            $this->downloadImage($image);
            $array['total_images'] = count($images);
            $array['images'] = $images;

        }
        return $array;
    }

    private function extractLinks($links)
    {
        $array = [];
        foreach ($links as $link) {
            $array['total_links'] = count($links);
            $array['links'] = $links;
        }
        return $array;
    }

    public function dd($array)
    {
        echo '<pre>';
        die(var_dump($array));
        echo '</pre>';
    }

    Private function setUrl($path)
    {
        return $this->baseUrl.'/'.$path;
    }
}