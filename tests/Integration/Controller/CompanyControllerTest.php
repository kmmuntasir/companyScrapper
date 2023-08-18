<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CompanyControllerTest extends WebTestCase
{
    private CompanyRepository $companyRepository;
    protected AbstractBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        unset($this->companyRepository);
        parent::tearDown();
    }

//    public function testAddCompany(): void
//    {
//        $payload = ["156514670"];
//        dump(json_encode($payload));
//        $this->client->request(
//            method: Request::METHOD_POST,
//            uri: '/api/company',
//            content: json_encode($payload)
//        );
//
//        $response = $this->client->getResponse();
//        dump($response->getContent());
//        self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
//
//        self::assertTrue(true);
//    }
}
