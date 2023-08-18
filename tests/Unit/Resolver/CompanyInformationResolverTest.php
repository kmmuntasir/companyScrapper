<?php

declare(strict_types=1);

namespace App\Tests\Unit\Resolver;

use App\Exception\CompanyNotFoundException;
use App\Resolver\CompanyInformationResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CompanyInformationResolverTest extends TestCase
{
    public function testResolverResolvesSuccessfullyWhenDataFound(): void
    {
        $httpClient = new MockHttpClient([
            $this->getMockResponse(
                file_get_contents(__DIR__ . '/../../Fixtures/CompanySearchResult.html'),
            ),
            $this->getMockResponse(
                file_get_contents(__DIR__ . '/../../Fixtures/CompanyDetails.html'),
            ),
            $this->getMockResponse(
                file_get_contents(__DIR__ . '/../../Fixtures/CompanyTurnoverInfo.html'),
            ),
        ]);

        $companyInfo = (new CompanyInformationResolver($httpClient))
            ->resolve('156514670');

        $this->assertCount(2, $companyInfo);
        $this->assertSame($this->getExpectedCompanyData(), $companyInfo);
    }

    public function testResolverThrowsExceptionWhenDataNotFound(): void
    {
        $httpClient = new MockHttpClient([
            $this->getMockResponse(''),
            $this->getMockResponse(''),
            $this->getMockResponse(''),
        ]);

        $this->expectException(CompanyNotFoundException::class);
        $this->expectExceptionMessage('Company Not Found');

        $companyInfo = (new CompanyInformationResolver($httpClient))
            ->resolve('156514670');
    }

    private function getMockResponse(
        string $content,
        int $status = Response::HTTP_OK,
    ): ResponseInterface
    {
        return new MockResponse(
            body: $content,
            info: [
                'http_code' => $status,
                'header' => [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ],
            ],
        );
    }

    private function getExpectedCompanyData(): array
    {
        return [
            "details" => [
                "vat" => "LT565146716",
                "address" => "Ruklos g. 14, LT-55198 Jonava",
                "mobilePhone" => "",
                "name" => "A grupė",
                "registrationCode" => "156514670"
            ],
            "turnover" => [
                "2018" => [
                    "nonCurrentAssets" => "1 394 588 €",
                    "currentAssets" => "768 467 €",
                    "equity" => "1 443 667 €",
                    "liabilities" => "662 495 €",
                    "salesRevenue" => "1 612 415 €",
                    "profitBeforeTaxes" => "73 684 €",
                    "profitBeforeTaxesMargin" => "4,57 %",
                    "netProfit" => "73 684 €",
                    "netProfitMargin" => "4,57 %",
                    "year" => 2018
                ],
                "2019" => [
                    "nonCurrentAssets" => "1 345 753 €",
                    "currentAssets" => "968 906 €",
                    "equity" => "1 492 025 €",
                    "liabilities" => "769 809 €",
                    "salesRevenue" => "1 690 435 €",
                    "profitBeforeTaxes" => "48 358 €",
                    "profitBeforeTaxesMargin" => "2,86 %",
                    "netProfit" => "48 358 €",
                    "netProfitMargin" => "2,86 %",
                    "year" => 2019
                ],
                "2020" => [
                    "nonCurrentAssets" => "1 538 520 €",
                    "currentAssets" => "893 056 €",
                    "equity" => "1 533 652 €",
                    "liabilities" => "765 569 €",
                    "salesRevenue" => "1 780 318 €",
                    "profitBeforeTaxes" => "41 627 €",
                    "profitBeforeTaxesMargin" => "2,34 %",
                    "netProfit" => "41 627 €",
                    "netProfitMargin" => "2,34 %",
                    "year" => 2020
                ],
                "2021" => [
                    "nonCurrentAssets" => "1 684 576 €",
                    "currentAssets" => "897 985 €",
                    "equity" => "1 682 194 €",
                    "liabilities" => "676 077 €",
                    "salesRevenue" => "2 192 224 €",
                    "profitBeforeTaxes" => "148 542 €",
                    "profitBeforeTaxesMargin" => "6,78 %",
                    "netProfit" => "148 542 €",
                    "netProfitMargin" => "6,78 %",
                    "year" => 2021
                ],
                "2022" => [
                    "nonCurrentAssets" => "2 205 600 €",
                    "currentAssets" => "1 121 746 €",
                    "equity" => "1 793 038 €",
                    "liabilities" => "1 352 963 €",
                    "salesRevenue" => "2 148 789 €",
                    "profitBeforeTaxes" => "110 844 €",
                    "profitBeforeTaxesMargin" => "5,16 %",
                    "netProfit" => "110 844 €",
                    "netProfitMargin" => "5,16 %",
                    "year" => 2022
                ]
            ]
        ];
    }
}