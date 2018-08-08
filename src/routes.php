<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info($args['Landed on index page']);
    

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/api/timezones', function (Request $request, Response $response, array $args) {
    $regions     = [
        'Africa'     => DateTimeZone::AFRICA,
        'America'    => DateTimeZone::AMERICA,
        'Antarctica' => DateTimeZone::ANTARCTICA,
        'Asia'       => DateTimeZone::ASIA,
        'Atlantic'   => DateTimeZone::ATLANTIC,
        'Europe'     => DateTimeZone::EUROPE,
        'Indian'     => DateTimeZone::INDIAN,
        'Pacific'    => DateTimeZone::PACIFIC
    ];
    $timeZones   = [];
    $searchTerm  = $_POST['term'];
    $suggestions = [];

    $this->logger->info('Hitting the time-zone endpoint');
    $this->logger->info($searchTerm);

    if (!$searchTerm) {
        return $response->withJson([]);
    }

    foreach ($regions as $regionName => $regionId) {
        $regionTimeZones = DateTimeZone::listIdentifiers($regionId);
        $this->logger->info($regionId);
        $timeZones = array_merge($timeZones, $regionTimeZones);
    }

    foreach ($timeZones as $id) {
        $dateTimeZone = new DateTimeZone($id);
        $dateTime     = new DateTime(null, $dateTimeZone);
        $name         = preg_replace('/_/', ' ', $dateTimeZone->getName());
        $location     = $dateTimeZone->getLocation();
        $country      = $location['country_code'];
        $comments     = $location['comments'];
        $abbr         = $dateTime->format('T');

        if (stripos($name, $searchTerm) > -1 ||
            stripos($abbr, $searchTerm) > -1 ||
            stripos($comments, $searchTerm) > -1) {
            $suggestion = [
                'name'         => $name,
                'abbreviation' => $abbr,
                'country'      => $country,
                'comments'     => $comments
            ];
            
            $suggestions[] = $suggestion;
        }
    }

    return $response->withJson($suggestions);
});
