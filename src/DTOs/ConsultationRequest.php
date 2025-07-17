<?php

namespace Ferreiramg\TecnospeedSerasa\DTOs;

class ConsultationRequest
{
    public function __construct(
        private string $documento,
        private int $codConsulta = 602,
        private ?string $uf = null,
        private string $retorno = 'HTML'
    ) {}

    public function toArray(): array
    {
        $data = [
            'documento' => $this->documento,
            'codConsulta' => $this->codConsulta,
            'retorno' => $this->retorno,
        ];

        if ($this->uf !== null) {
            $data['uf'] = $this->uf;
        }

        return $data;
    }

    public function getDocumento(): string
    {
        return $this->documento;
    }

    public function getCodConsulta(): int
    {
        return $this->codConsulta;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }

    public function getRetorno(): string
    {
        return $this->retorno;
    }

    public function getHeaders(array $credentials): array
    {
        return [
            'cnpjsh' => $credentials['cnpjsh'],
            'tokensh' => $credentials['tokensh'],
            'cnpjUsuario' => $credentials['cnpjUsuario'],
            'login' => $credentials['login'],
            'password' => $credentials['password'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
