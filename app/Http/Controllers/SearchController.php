<?php
namespace App\Http\Controllers;
use App\Contracts\SearchInterface;
use App\DTOs\SearchResultsDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SearchController extends Controller {

     /**
     * Class constructor for [SearchController].
     *
     * @param \App\Contracts\SearchInterface $searchInterface
     * @param \App\DTOs\SearchResultsDTO $searchResultsDTO
     */
    public function __construct(
        protected SearchInterface $searchInterface,
        protected SearchResultsDTO $searchResultsDTO
    ) {}


    /**
     * Handles the search functionality, retrieving search results based on a department ID and query.
     *
     * @param \Illuminate\Http\Request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $department_id = $request->query('department_id');
        $query = $request->query('query');
        
        // send request to museum search service
        $results = $this->searchInterface->search($department_id, $query);

        // reformat data before sending to frontend
        $transformedResults = $this->searchResultsDTO->transform($results);

        return response()->json($transformedResults, status: Response::HTTP_OK);
    }

}