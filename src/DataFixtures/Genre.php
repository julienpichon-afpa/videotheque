<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Genre extends Fixture
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchtmdbInformation(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.themoviedb.org/3/genre/movie/list',[
                'query' => [
                    "api_key" => 'fb2a32d630bbdc97ef07290e95700e2f',
                    "language" => 'en-US'
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

        $datas = $this->fetchtmdbInformation();
        $datas = $datas["genres"];
        foreach ($datas as $genre) {
            $fixtureGenre = new \App\Entity\Genre();
            $fixtureGenre->setId($genre['id'])->setName($genre['name']);
            $manager->persist($fixtureGenre);
            $manager->flush();
        }
    }
}
