<?php

declare(strict_types=1);

namespace Pudo\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PudoPointClient
{
    private ?Client $client;
    private LoggerInterface $logger;
    private SystemConfigService $systemConfigService;

    private string $baseUri;
    private string $signature;
    private string $apiEndpoint;
    private string $partnerCode;
    private string $partnerPassword;
    private float $maxDealerDistance;

    private const DEFAULT_WEIGHT = 5.0;
    private const DEFAULT_HEIGHT = 2.0;
    private const DEFAULT_WIDTH = 10.0;
    private const DEFAULT_LENGTH = 10.0;
    private const WEIGHT_UNIT = 'LBS';
    private const DIMENSION_UNIT = 'IN';

    private const NUMBER_OF_DEALERS = 10;

    public function __construct(SystemConfigService $systemConfigService, LoggerInterface $logger)
    {
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;

        $this->signature = $this->getConfig('Pudo.config.signature');
        $this->baseUri = $this->getConfig('Pudo.config.baseUri');
        $this->apiEndpoint = $this->getConfig('Pudo.config.apiEndpoint');
        $this->partnerCode = $this->getConfig('Pudo.config.partnerCode');
        $this->partnerPassword = $this->getConfig('Pudo.config.partnerPassword');
        $this->maxDealerDistance = (float) $this->getConfig('Pudo.config.maxDealerDistance');

        $this->client = $this->baseUri ? new Client(['base_uri' => $this->baseUri]) : null;
    }

    private function getConfig(string $key): string
    {
        return $this->systemConfigService->get($key) ?? '';
    }

    public function request(string $zip)
    {
        if (!$this->client) {
            return $this->getEmptyResponse();
        }

        $fullEndpointUrl = $this->baseUri . $this->apiEndpoint;
        $body = $this->buildRequestBody($zip);

        try {
            $response = $this->client->request('POST', $fullEndpointUrl, [
                'headers' => $this->getRequestHeaders(),
                'body' => json_encode($body)
            ]);

            return $this->processResponse($response);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage());
        }

        return $this->getEmptyResponse();
    }

    private function buildRequestBody(string $zip): array
    {
        return [
            'partnerCode' => $this->partnerCode,
            'partnerPassword' => $this->partnerPassword,
            'weight' => self::DEFAULT_WEIGHT,
            'width' => self::DEFAULT_WIDTH,
            'height' => self::DEFAULT_HEIGHT,
            'length' => self::DEFAULT_LENGTH,
            'weightUnit' => self::WEIGHT_UNIT,
            'dimensionUnit' => self::DIMENSION_UNIT,
            'address' => $zip,
        ];
    }

    private function getRequestHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'x-signature' => $this->signature,
        ];
    }

    private function processResponse($response): object
    {
        $response = json_decode($response->getBody()->getContents());
        $response->dealers = $this->filterAndLimitDealers($response->dealers);

        return $response;
    }

    private function filterAndLimitDealers(array $dealers): array
    {
        $filteredDealers = array_filter($dealers, fn($dealer) => $dealer->dealerDistance <= $this->maxDealerDistance);
        return array_slice($filteredDealers, 0, self::NUMBER_OF_DEALERS);
    }

    private function getEmptyResponse(): object
    {
        return (object)[
            'dealers' => [],
            'selectedPudoPoint' => null,
        ];
    }
}