<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Exception\CompanyNotFoundException;
use App\Exception\ResolveFailedException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CompanyInformationResolver
{
    private const DETAILS_KEYS = [
        'VAT' => 'vat',
        'Address' => 'address',
        'Mobile phone' => 'mobilePhone',
    ];

    private const TURNOVER_KEYS = [
        'Non-current assets' => 'nonCurrentAssets',
        'Current assets' => 'currentAssets',
        'Equity capital' => 'equity',
        'Amounts payable and other liabilities' => 'liabilities',
        'Sales revenue' => 'salesRevenue',
        'Profit (loss) before taxes' => 'profitBeforeTaxes',
        'Profit before taxes margin' => 'profitBeforeTaxesMargin',
        'Net profit (loss)' => 'netProfit',
        'Net profit margin' => 'netProfitMargin',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function resolve(string $registrationCode): array
    {
        try {
            [
                $companyName,
                $companyUrl,
            ] = $this->getCompanyNameAndUrl(
                registrationCode: $registrationCode
            );

            if (null === $companyName || null === $companyUrl) {
                throw (new CompanyNotFoundException('Company Not Found'))
                    ->setContext([
                        'error' => "HTML parsing failed for company with registration code $registrationCode",
                    ]);
            }

            $companyDetails = $this->getCompanyDetails($companyUrl);
            $companyDetails['name'] = $companyName;
            $companyDetails['registrationCode'] = $registrationCode;

            $companyTurnoverInfo = $this->getCompanyTurnoverInfo(
                companyTurnoverUrl: $companyUrl . '/turnover',
            );

            array_walk($companyTurnoverInfo, function (&$turnover, $year) {
                $turnover['year'] = $year;
            });

            return [
                'details' => $companyDetails,
                'turnover' => $companyTurnoverInfo,
            ];
        } catch (HttpExceptionInterface $exception) {
            throw (new ResolveFailedException('Connection to the server failed'))
                ->setContext([
                    'error' => "Connection to the server failed for company with registration code $registrationCode",
                    'httpExceptionCode' => $exception->getCode(),
                    'httpExceptionMessage' => $exception->getMessage(),
                ]);
        }
    }

    private function getCompanyTurnoverInfo(string $companyTurnoverUrl): array
    {
        $response = $this->httpClient->request(
            method: Request::METHOD_GET, // NOSONAR
            url: $companyTurnoverUrl
        );

        $crawler = new Crawler($response->getContent());

        $years = [];

        $yearData = iterator_to_array($crawler->filter('table.finances-table > thead > tr > th.years')->getIterator());
        foreach ($yearData as $d) {
            $years[] = $this->sanitizeText($d->textContent);
        }

        $companyTurnoverInfo = [];

        $crawler->filter('table.finances-table > tr')->each(
            function (Crawler $row) use (&$companyTurnoverInfo, &$years) {
                $key = $row->filter('td')->first()->text();
                if (isset(self::TURNOVER_KEYS[$key])) {
                    $newKey = self::TURNOVER_KEYS[$key];
                    $data = iterator_to_array($row->filter('td.year-value')->getIterator());
                    $i = 0;
                    foreach ($data as $val) {
                        $companyTurnoverInfo[$years[$i]][$newKey] = $this->sanitizeText($val->textContent);
                        ++$i;
                    }
                }
            }
        );

        return $companyTurnoverInfo;
    }

    private function getCompanyDetails(string $companyUrl): array
    {
        $response = $this->httpClient->request(
            method: Request::METHOD_GET, // NOSONAR
            url: $companyUrl
        );

        $crawler = new Crawler($response->getContent());

        $companyDetails = [];

        $crawler->filter('div.details-block__1 > div.information > table > tbody > tr')->each(
            function (Crawler $row) use (&$companyDetails) {
                $key = $row->filter('td.name')->first()->text();
                $value = $row->filter('td.value')->first()->text();
                if (isset(self::DETAILS_KEYS[$key])) {
                    $newKey = self::DETAILS_KEYS[$key];
                    $companyDetails[$newKey] = $value;
                }
            }
        );

        $crawler->filter('div.details-block__2 > table > tbody > tr')->each(
            function (Crawler $row) use (&$companyDetails) {
                $key = $row->filter('td.name')->first()->text();
                $value = $row->filter('td.value')->first()->text();
                if (isset(self::DETAILS_KEYS[$key])) {
                    $newKey = self::DETAILS_KEYS[$key];
                    $companyDetails[$newKey] = $value;
                }
            }
        );

        return $companyDetails;
    }


    private function getCompanyNameAndUrl(string $registrationCode): array
    {
        $response = $this->httpClient->request(
            method: Request::METHOD_POST, // NOSONAR
            url: 'https://rekvizitai.vz.lt/en/company-search/1/',
            options: [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => [
                    "code" => $registrationCode,
                    "order" => "1",
                    "resetFilter" => "0",
                ],
            ],
        );

        $crawler = new Crawler($response->getContent());
        $nameContainer = $crawler->filter(
            'div.company-info > div.company-info-wr > div.titles-block > a.company-title'
        );
        $companyName = $nameContainer->count() > 0 ? $nameContainer->first()->text() : null;

        $urlContainer = $crawler->filter(
            'div.company-info > div.company-info-wr > div.titles-block > a.company-title'
        );

        $companyUrl = $urlContainer->count() > 0 ? $urlContainer->first()->attr('href') : null;

        return [
            $companyName,
            $companyUrl,
        ];
    }

    private function sanitizeText($inputString): string
    {
        return preg_replace('/\s+/', ' ', trim($inputString));
    }

    private function throwNotFoundException(string $registrationCode): void
    {
        throw (new CompanyNotFoundException('Company Not Found'))
            ->setContext([
                'error' => "HTML parsing failed for company with registration code $registrationCode",
            ]);
    }
}
