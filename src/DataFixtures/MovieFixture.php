<?php

namespace App\DataFixtures;

use App\Repository\GenreRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieFixture extends Fixture
{
    private $client;
    private  $genreRepository;

    public function __construct(HttpClientInterface $client, GenreRepository $genreRepository)
    {
        $this->client = $client;
        $this->genreRepository = $genreRepository;
    }

    public function fetchtMdbMovieInformation(int $page): array
    {
        //  https://api.themoviedb.org/3/movie/popular?api_key=<<api_key>>&language=en-US&page=1
            $response = $this->client->request(
            'GET',
            'https://api.themoviedb.org/3/movie/popular', [
                'query' => [
                    "api_key" => 'fb2a32d630bbdc97ef07290e95700e2f',
                    "language" => 'en-US',
                    'page' => $page,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]
        );
        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        $content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content;
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 11; $i++){
            $datas = $this->fetchtMdbMovieInformation($i);

            foreach ($datas["results"] as $movie){
                $movieFixture = new \App\Entity\Movie();
                $date = new \DateTime($movie["release_date"]);
//                $date = $date->format('Y-m-d');
                $movieFixture
                    ->setPosterPath($movie["poster_path"])
                    ->setAdult($movie["adult"])
                    ->setReleaseDate($date)
                    ->setOverview($movie["overview"])
                    ->setOriginalTitle($movie["original_title"])
                    ->setOriginalLanguage($movie["original_language"])
                    ->setTitle($movie["title"])
                    ->setOriginalTitle($movie["original_title"]);
                foreach ($movie["genre_ids"] as $id_genre){
                    $genreFixture = $this->genreRepository->findOneBySomeId($id_genre);
                    $movieFixture->addGenre($genreFixture);
                }
                $manager->persist($movieFixture);
                $manager->flush();
            }
        }
        // $product = new Product();
        // $manager->persist($product);
    }
}
