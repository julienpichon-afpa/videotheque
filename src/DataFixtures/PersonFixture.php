<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\PersonRepository;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Entity\Person;
use App\Entity\Movie;

class PersonFixture extends Fixture
{
    private $client;
    private  $personRepository;
    private  $movieRepository;
    private  $genreRepository;
    


    public function __construct(HttpClientInterface $client,
     PersonRepository $personRepository, MovieRepository $movieRepository,
     GenreRepository $genreRepository)
    {
        $this->client = $client;
        $this->personRepository = $personRepository;
        $this->movieRepository = $movieRepository;
        $this->genreRepository = $genreRepository;
    }

    public function fetchtMdbPersons(int $page): array
    {
        //  https://api.themoviedb.org/3/movie/popular?api_key=<<api_key>>&language=en-US&page=1
            $response = $this->client->request(
            'GET',
            'https://api.themoviedb.org/3/person/popular', [
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
        for ($i = 1; $i < 3; $i++){
            $data = $this->fetchtMdbPersons($i);

            foreach ($data["results"] as $person){
                $personFixture = new Person();
                $personFixture->setId($person["id"])
                              ->setAdult($person["adult"])
                              ->setGender($person["gender"])
                              ->setName($person["name"])
                              ->setKnownForDepartment($person["known_for_department"])
                              ->setProfilePath($person["profile_path"]);
    
                foreach ($person["known_for"] as $movie){
                    if($movie["media_type"]=="movie"){
                        if($this->movieRepository->findOneById($movie["id"])){
                        $personFixture->addMovie($this->movieRepository->findOneById($movie["id"]));
                        }else{
                            $newMovie =new Movie();
                            $date = new \DateTime($movie["release_date"]);
                            $newMovie->setId($movie["id"])
                               ->setAdult($movie["adult"])
                               ->setReleaseDate($date)
                               ->setOverview($movie["overview"])
                               ->setOriginalTitle($movie["original_title"])
                               ->setOriginalLanguage($movie["original_language"])
                               ->setTitle($movie["title"])
                               ->setOriginalTitle($movie["original_title"]);
                               if(isset($movie["poster_path"])){
                                $newMovie->setPosterPath($movie["poster_path"]);
                               }
                               foreach ($movie["genre_ids"] as $id_genre){
                               $genreFixture = $this->genreRepository->findOneBySomeId($id_genre);
                               $newMovie->addGenre($genreFixture);
                               }
                            $manager->persist($newMovie);
                            $manager->flush();
                            $personFixture->addMovie($this->movieRepository->findOneById($movie["id"]));
                        }
                    }
                }
                $manager->persist($personFixture);
                $manager->flush();
            }
        }
    }
}
