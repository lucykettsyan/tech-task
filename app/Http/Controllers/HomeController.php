<?php
namespace App\Http\Controllers;
use App\Contracts\SearchInterface;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;


class HomeController extends Controller {

     /**
     * Class constructor for [HomeController]
     *
     * @param \App\Contracts\SearchInterface $searchInterface
     */
    public function __construct(
        protected SearchInterface $searchInterface
    ) {}


    /**
     * Landing page
     *
     * @return \Inertia\Response
     */
    public function index(): Response {
        $departmentsOptions = $this->searchInterface->getDepartments();

        return Inertia::render('home', ["departmentsOptions" => $departmentsOptions]);
    }

}