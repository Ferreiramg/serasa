<?php

namespace Ferreiramg\TecnospeedSerasa\DTOs;

class ConsultationResponse
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getProtocolo(): ?string
    {
        return $this->data['protocolo'] ?? null;
    }

    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }

    public function getDocumento(): ?string
    {
        return $this->data['documento'] ?? null;
    }

    public function getCodConsulta(): ?string
    {
        return $this->data['codConsulta'] ?? null;
    }

    // Para respostas de erro
    public function getCode(): ?int
    {
        return $this->data['code'] ?? null;
    }

    public function getMessage(): ?string
    {
        return $this->data['message'] ?? null;
    }

    public function getErrors(): array
    {
        return $this->data['errors'] ?? [];
    }

    public function getInternalCode(): ?int
    {
        $errors = $this->getErrors();

        return ! empty($errors) ? ($errors[0]['internalCode'] ?? null) : null;
    }

    public function getErrorMessage(): ?string
    {
        $errors = $this->getErrors();
        if (! empty($errors)) {
            return $errors[0]['message'] ?? null;
        }

        return $this->getMessage();
    }

    // Para resultado da consulta (quando consultado via protocolo)
    public function getResultado(): ?string
    {
        return $this->data['resultado'] ?? null;
    }

    public function getHtml(): ?string
    {
        return $this->data['html'] ?? null;
    }

    public function isSuccess(): bool
    {
        return $this->getStatus() === 'processando' ||
               $this->getStatus() === 'concluido' ||
               $this->getStatus() === 'finalizado' ||
               ($this->getCode() === null && $this->getProtocolo() !== null);
    }

    public function hasError(): bool
    {
        return ! $this->isSuccess() || $this->getCode() !== null;
    }

    public function isProcessing(): bool
    {
        return $this->getStatus() === 'processando';
    }

    public function isCompleted(): bool
    {
        return $this->getStatus() === 'concluido' || $this->getStatus() === 'finalizado';
    }

    public function isUnauthorized(): bool
    {
        return $this->getCode() === 401;
    }

    public function isUnprocessableEntity(): bool
    {
        return $this->getCode() === 422;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function toJson(): string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
