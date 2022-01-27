<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;

class ListController extends AbstractController
{
    private $Repository;
    public function __construct(MovieRepository $MovieRepository)
    {
        $this->Repository=$MovieRepository;
    }
    /**
     * @Route("/list", name="list")
     */
    public function index(): Response
    {
        $movies = $this->Repository->findAll();
        return $this->render('list/index.html.twig', [
            'movies' => $movies,
        ]);
    }
}
