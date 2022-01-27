<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends AbstractController
{

    private $Repository;

    public function __construct(MovieRepository $MovieRepository)
    {
        $this->Repository=$MovieRepository;
    }

    /**
     * @Route("/list/{id}", name="detail" )
     */
    public function index(int $id): Response
    {
        $details = $this->Repository->findById($id);


        return $this->render('detail/index.html.twig', [
            'controller_name' => 'DetailController',
            'details' => $details
        ]);
    }
}
