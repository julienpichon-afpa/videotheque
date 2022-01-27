<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FilterType;

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
    public function index(Request $request): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $movies = $this->Repository->findMoviesByKeyword($form->getData()['userinput']);
        }
        else{
            $movies = $this->Repository->findAll();
        }
        return $this->render('list/index.html.twig', [
            'movies' => $movies,
            'form' => $form->createView(),
        ]);
    }
}
