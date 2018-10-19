<?php
namespace App\Controller;

use \DateTime;
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
        DateTimeZone::INDIAN     => 'Indian',
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
        $requestBody = $request->getParsedBody();
        $searchTerm  = $requestBody['searchTerm'];

        if (!$searchTerm) {
            return $response->withJson([
                'status' => 'OK',
                'Message' => 'No search term provided'
            ]);
        }

        $regionIds   = array_keys($this->regions);
        $timeZones   = array_reduce($regionIds, [$this, 'toIdentifiers'], []);

        $suggestions = array_reduce(
            $timeZones,
            function (array $suggestions, string $timeZone) use ($searchTerm) {
                $newSuggestion = $this->suggestion($searchTerm, $timeZone);

                if (!empty($newSuggestion)) {
                    $suggestions[] = $newSuggestion;
                }

                return $suggestions;
            },
            []
        );

        return $response->withJson($suggestions);
    }

    /**
     * Returns the identifiers associated with the region id.
     *
     * @param int $regionId
     *
     * @return array
     */
    private function toIdentifiers(array $timeZones, int $regionId): array
    {
        return array_merge(
            $timeZones,
            DateTimeZone::listIdentifiers($regionId)
        );
    }

    /**
     * Returns a list of suggestions given a search term and a time zone.
     *
     * @param string $searchTerm
     * @param string $timeZone
     *
     * @return array
     */
    private function suggestion(string $searchTerm, string $timeZone): array
    {
        $suggestion   = [];
        $dateTimeZone = new DateTimeZone($timeZone);
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
                'comments'     => $comments,
            ];
        }

        return $suggestion;
    }
}