<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SireneSiretValidator
{
    private string $normalizedApiKey;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        ?string $apiKey,
        private readonly string $baseUrl,
    ) {
        $this->normalizedApiKey = trim((string) $apiKey);
    }

    /**
     * @return array{valid: bool, message: string}
     */
    public function validate(string $siret): array
    {
        if (preg_match('/^\d{14}$/', $siret) !== 1) {
            return [
                'valid' => false,
                'message' => 'Le SIRET doit contenir 14 chiffres.',
            ];
        }

        if ($this->normalizedApiKey === '') {
            return [
                'valid' => false,
                'message' => 'La vérification SIRET est temporairement indisponible.',
            ];
        }

        try {
            $response = $this->httpClient->request('GET', sprintf('%s/siret/%s', rtrim($this->baseUrl, '/'), $siret), [
                'headers' => [
                    'Accept' => 'application/json',
                    'X-INSEE-Api-Key-Integration' => $this->normalizedApiKey,
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 404) {
                return [
                    'valid' => false,
                    'message' => "Ce numéro de SIRET n'est pas valide.",
                ];
            }

            if ($statusCode < 200 || $statusCode >= 300) {
                return [
                    'valid' => false,
                    'message' => 'La vérification SIRET est temporairement indisponible.',
                ];
            }

            $data = $response->toArray(false);
            $etablissement = $data['etablissement'] ?? null;

            if (!is_array($etablissement)) {
                return [
                    'valid' => false,
                    'message' => "Ce numéro de SIRET n'est pas valide.",
                ];
            }

            if ($this->isClosed($etablissement)) {
                return [
                    'valid' => false,
                    'message' => 'Ce SIRET correspond à un établissement fermé.',
                ];
            }

            return [
                'valid' => true,
                'message' => 'SIRET valide.',
            ];
        } catch (TransportExceptionInterface) {
            return [
                'valid' => false,
                'message' => 'La vérification SIRET est temporairement indisponible.',
            ];
        }
    }

    /**
     * @param array<string, mixed> $etablissement
     */
    private function isClosed(array $etablissement): bool
    {
        if (($etablissement['dateFin'] ?? null) !== null) {
            return true;
        }

        $periodes = $etablissement['periodesEtablissement'] ?? [];
        $currentPeriod = is_array($periodes) ? ($periodes[0] ?? null) : null;

        return is_array($currentPeriod) && ($currentPeriod['etatAdministratifEtablissement'] ?? null) === 'F';
    }
}
