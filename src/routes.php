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
    $timeZones   = DateTimeZone::listAbbreviations(DateTimeZone::ALL);
    $searchTerm  = $_POST['term'];
    $suggestions = [];

    $this->logger->info('Hitting the time-zone endpoint');
    $this->logger->info($searchTerm);

    if (!$searchTerm) {
        return $response->withJson([]);
    }

    foreach ($timeZones as $abbr => $timeZoneInfo) {
        $id = $timeZoneInfo[0]['timezone_id'];

        if (!$id) {
            continue;
        }

        $dateTimeZone = new DateTimeZone($id);
        $name         = preg_replace('/_/', ' ', $dateTimeZone->getName());
        $location     = $dateTimeZone->getLocation();
        $country      = $location['country_code'];
        $comments     = $location['comments'];

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
