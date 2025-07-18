<?php

namespace Ferreiramg\TecnospeedSerasa\Services;

use Ferreiramg\TecnospeedSerasa\DTOs\ConsultationRequest;
use Ferreiramg\TecnospeedSerasa\DTOs\ConsultationResponse;
use Ferreiramg\TecnospeedSerasa\Exceptions\TecnospeedSerasaException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TecnospeedSerasaService
{
    protected Client $client;

    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        $baseUrl = $this->getBaseUrl();

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => $config['timeout'],
            'verify' => true,
        ]);
    }

    /**
     * Get base URL based on environment
     */
    private function getBaseUrl(): string
    {
        $environment = $this->config['environment'] ?? 'homologacao';

        return $this->config['base_url'][$environment];
    }

    /**
     * Solicitar consulta assíncrona
     */
    public function solicitarConsulta(ConsultationRequest $request): ConsultationResponse
    {
        $this->validateCredentials();

        try {
            $headers = $request->getHeaders($this->config['credentials']);

            $response = $this->client->post($this->config['endpoints']['consulta_assincrona'], [
                'headers' => $headers,
                'json' => $request->toArray(),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return new ConsultationResponse($data);
        } catch (GuzzleException $e) {
            $this->handleGuzzleException($e);
        }
    }

    /**
     * Consultar por documento (método de conveniência)
     */
    public function consultarPorDocumento(
        string $documento,
        int $codConsulta = 602,
        ?string $uf = null,
        string $retorno = 'HTML'
    ): ConsultationResponse {
        if (! $this->validarDocumento($documento)) {
            throw new TecnospeedSerasaException("Documento inválido: {$documento}");
        }

        $request = new ConsultationRequest($documento, $codConsulta, $uf, $retorno);

        return $this->solicitarConsulta($request);
    }

    /**
     * Consultar protocolo para obter resultado
     */
    public function consultarProtocolo(string $protocolo): string|ConsultationResponse
    {
        $this->validateCredentials();

        try {
            $headers = [
                'cnpjsh' => $this->config['credentials']['cnpjsh'],
                'tokensh' => $this->config['credentials']['tokensh'],
                'cnpjUsuario' => $this->config['credentials']['cnpjUsuario'],
                'login' => $this->config['credentials']['login'],
                'password' => $this->config['credentials']['password'],
                'Accept' => 'application/json',
            ];

            $response = $this->client->get($this->config['endpoints']['consulta_protocolo'], [
                'headers' => $headers,
                'query' => ['protocolo' => $protocolo],
            ]);

            // caso sucesso, retorna o conteúdo diretamente como HTML
            if ($response->getStatusCode() === 200) {
                $data = [
                    'html' => $response->getBody()->getContents(),
                    'status' => 'concluido',
                    'protocolo' => $protocolo,
                ];
            } else {
                $data = json_decode($response->getBody()->getContents(), true);
            }

            return new ConsultationResponse($data);
        } catch (GuzzleException $e) {
            $this->handleGuzzleException($e);
        }
    }

    /**
     * Validar formato do documento
     */
    public function validarDocumento(string $documento): bool
    {
        $documento = preg_replace('/[^0-9]/', '', $documento);

        // CPF
        if (strlen($documento) === 11) {
            return $this->validarCPF($documento);
        }

        // CNPJ
        if (strlen($documento) === 14) {
            return $this->validarCNPJ($documento);
        }

        return false;
    }

    /**
     * Validar credenciais
     */
    private function validateCredentials(): void
    {
        $required = ['cnpjsh', 'tokensh', 'cnpjUsuario', 'login', 'password'];

        foreach ($required as $field) {
            if (empty($this->config['credentials'][$field])) {
                throw new TecnospeedSerasaException("Credencial obrigatória não informada: {$field}");
            }
        }
    }

    /**
     * Handle Guzzle exceptions
     *
     * @throws TecnospeedSerasaException
     */
    private function handleGuzzleException(GuzzleException $e): never
    {
        $message = 'Erro na requisição: '.$e->getMessage();

        if ($e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            if ($responseData) {
                if (isset($responseData['message'])) {
                    $message = $responseData['message'];
                }

                if (isset($responseData['errors']) && ! empty($responseData['errors'])) {
                    $errorMessages = array_map(function ($error) {
                        return $error['message'] ?? 'Erro desconhecido';
                    }, $responseData['errors']);

                    $message .= ' - '.implode(', ', $errorMessages);
                }
            }
        }

        throw new TecnospeedSerasaException($message, 0, $e instanceof \Exception ? $e : null);
    }

    /**
     * Validar CPF
     */
    private function validarCPF(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ((int) $cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validar CNPJ
     */
    private function validarCNPJ(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14) {
            return false;
        }

        $weights = [6, 7, 8, 9, 2, 3, 4, 5, 6, 7, 8, 9];

        for ($i = 0, $sum = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if ((int) $cnpj[12] != $digit1) {
            return false;
        }

        $weights = [5, 6, 7, 8, 9, 2, 3, 4, 5, 6, 7, 8, 9];

        for ($i = 0, $sum = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return (int) $cnpj[13] == $digit2;
    }

    /**
     * Get available consultation types
     */
    public function getTiposConsulta(): array
    {
        return $this->config['tipos_consulta'] ?? [];
    }

    /**
     * Get available return types
     */
    public function getTiposRetorno(): array
    {
        return $this->config['tipos_retorno'] ?? [];
    }
}
