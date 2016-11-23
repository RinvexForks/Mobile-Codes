<?php

/*
 * This file is part of Mobile Codes.
 *
 * (c) Brian Faust <hello@brianfaust.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';

use BrianFaust\Payload\Json;
use BrianFaust\Payload\YamlInline;
use BrianFaust\Payload\Yaml;

$crawler = (new Goutte\Client())->request('GET', 'http://mcc-mnc.com/');

$datasets = [];
$crawler->filter('table > tbody > tr')->each(function ($node) use (&$datasets) {
    $datasets[] = [
        'mcc' => $node->filter('td:nth-child(1)')->text(),
        'mnc' => $node->filter('td:nth-child(2)')->text(),
        'iso' => $node->filter('td:nth-child(3)')->text(),
        'country_name' => $node->filter('td:nth-child(4)')->text(),
        'country_code' => $node->filter('td:nth-child(5)')->text(),
        'network' => $node->filter('td:nth-child(6)')->text(),
    ];
});

$typeJson = new Json();
$typeYaml = new Yaml();
$typeYamlInline = new YamlInline();

$typeJson->write(__DIR__.'/../dist/unsorted/data.json', $datasets);
$typeYaml->write(__DIR__.'/../dist/unsorted/data.yml', $datasets);
$typeYamlInline->write(__DIR__.'/../dist/unsorted/data-inline.yml', $datasets);

$records = [];
foreach ($datasets as $dataset) {
    $records[$dataset['country_name']][] = $dataset;
}

$typeJson->write(__DIR__.'/../dist/sorted-by-country/data.json', $records);
$typeYaml->write(__DIR__.'/../dist/sorted-by-country/data.yml', $records);
$typeYamlInline->write(__DIR__.'/../dist/sorted-by-country/data-inline.yml', $records);
