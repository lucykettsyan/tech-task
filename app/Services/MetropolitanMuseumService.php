<?php

namespace App\Services;

use App\Contracts\SearchInterface;
use App\Exceptions\MuseumServiceException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Promise;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Response;

class MetropolitanMuseumService implements SearchInterface {

    protected $url;
    protected const OBJECT_COUNT_LIMIT = 10;

    public function __construct(
        protected GuzzleClient $guzzleClient, 
        protected HttpClient $httpClient
    ) {
        $this->url = env('METROPOLITAN_MUSEUM_API_URL');
    }


    public function search(string $department_id, string $query): array {
        try {
            // get object ids who's title contains the requested query
            $objectIds = $this->getObjectIds($department_id, $query);

            // get the object details based on the object ids retrieved previously
            $searchedObjects = [];
            if($objectIds) {
                $objectIds_limit = array_slice($objectIds, 0, self::OBJECT_COUNT_LIMIT);
                
                $requests = [];
                foreach($objectIds_limit as $key => $id) {
                    $requests["request{$key}"] = $this->guzzleClient->getAsync("{$this->url}/objects/{$id}");
                }
        
                $responses = Promise\Utils::unwrap($requests);
                
                foreach ($responses as $response) {
                    if ($response->getStatusCode() === Response::HTTP_OK) {
                        $content = $response->getBody()->getContents();
                        $searchedObjects[] = json_decode($content, true);
                    }
                }
            }
            
            return $searchedObjects;
        } catch (\Exception $e) {
            throw new MuseumServiceException("Museum API call failed, unable to get object details! ". $e->getMessage());
        }
    }


    private function getObjectIds(string $department_id, string $query) {
        try {
            $result = [];
            $response = $this->httpClient->get("{$this->url}/search?departmentId={$department_id}&title=true&q={$query}");
            if ($response->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($response->getBody()->getContents(), true);
                $result = $data["objectIDs"];
            }
            return $result;
        } catch (\Exception $e) {
            throw new MuseumServiceException("Museum API call failed, unable to get object ids!");
        }

    }


    public function getDepartments(): array {
        try {
            $data = [];
            $response = $this->httpClient->get("{$this->url}/departments");
            if ($response->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($response->getBody()->getContents(), true);
                $data = $data["departments"];
            }
            return $data;
        } catch (\Exception $e) {
            throw new MuseumServiceException("Museum API call failed, unable to get departments");
        }
    }

}