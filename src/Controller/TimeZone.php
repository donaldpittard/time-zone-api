<?php
namespace App\Controller;

use \DateTimeZone;
use Slim\Http\Request;
use Slim\Http\Response;

class TimeZone extends Controller
{
    private $regions = [
        DateTimeZone::AFRICA     => 'Africa',
        DateTimeZone::AMERICA    => 'America',
        DateTimeZone::ANTARCTICA => 'Antarctica',
        DateTimeZone::ARCTIC     => 'Arctic',
        DateTimeZone::ASIA       => 'Asia',
        DateTimeZone::ATLANTIC   => 'Atlantic',
        DateTimeZone::AUSTRALIA  => 'Australia',
        DateTimeZone::EUROPE     => 'Europe',
        DateTimeZone::Indian     => 'Indian',
        DateTimeZone::PACIFIC    => 'Pacific',
    ];

    /**
     * Given a query term, this function suggests a possible time zone.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function suggest(Request $request, Response $response, array $args)
    {
        $requestBody = json_decode($request->getBody());
        $searchTerm  = $request->searchTerm;
        $suggestions = [];

        if (!$searchTerm) {
            return $response->withJson([]);
        }

        $regionIds = array_keys($this->regions);
        $timeZones = array_reduce([$this, 'toIdentifiers'], $regionIds);

        $this->logger->info($timeZones);
    }

    /**
     * Returns the identifiers associated with the region id.
     *
     * @param int $regionId
     *
     * @return array
     */
    private function toIdentifiers(int $regionId): array
    {
        return DateTimeZone::listIdentifiers($regionId);
    }
}