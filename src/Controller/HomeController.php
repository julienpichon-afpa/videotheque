<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $Repository;
    public function __construct(MovieRepository $MovieRepository)
    {
        $this->Repository=$MovieRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $moviesSoon = $this->Repository->findBy(
            array(),
            array('release_date' => 'DESC'),
            4,
            0
        );

        $moviesLast = $this->Repository->findBy(
            array(),
            array('id' => 'ASC'),
            6,
            0
        );


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'moviesSoon' => $moviesSoon,
            'moviesLast' => $moviesLast
        ]);
    }
}
