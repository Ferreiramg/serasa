# Tecnospeed Serasa Package para Laravel

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://packagist.org/packages/ferreiramg/serasa)
[![Laravel Version](https://img.shields.io/badge/laravel-%5E12.0-red)](https://packagist.org/packages/ferreiramg/serasa)
[![Latest Stable Version](https://img.shields.io/packagist/v/ferreiramg/serasa)](https://packagist.org/packages/ferreiramg/serasa)
[![Total Downloads](https://img.shields.io/packagist/dt/ferreiramg/serasa)](https://packagist.org/packages/ferreiramg/serasa)
[![Tests](https://github.com/Ferreiramg/serasa/actions/workflows/ci.yml/badge.svg)](https://github.com/Ferreiramg/serasa/actions/workflows/ci.yml)
[![Code Coverage](https://codecov.io/gh/Ferreiramg/serasa/branch/main/graph/badge.svg)](https://codecov.io/gh/Ferreiramg/serasa)
[![Code Style](https://img.shields.io/badge/code%20style-Laravel%20Pint-orange)](https://github.com/laravel/pint)
[![Static Analysis](https://img.shields.io/badge/static%20analysis-PHPStan-blue)](https://phpstan.org/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

Um pacote Laravel para consumir a API oficial Tecnospeed Serasa, permitindo consultas de crédito assíncronas através de CPF ou CNPJ.

## Características

- ✅ API oficial Tecnospeed Serasa (v2)
- ✅ Consultas assíncronas com protocolo
- ✅ Validação de CPF e CNPJ
- ✅ Múltiplos tipos de consulta (Crednet, Relatórios Básicos e Avançados)
- ✅ Suporte a ambiente de homologação e produção
- ✅ Tratamento de erros personalizado
- ✅ Suporte a Laravel 12 e PHP 8.2+
- ✅ Facade para facilitar o uso
- ✅ Testes unitários e de integração (83%+ cobertura)
- ✅ Code Style com Laravel Pint
- ✅ Análise estática com PHPStan

## Qualidade e Testes

Este pacote segue as melhores práticas de desenvolvimento Laravel:

- **83%+ Cobertura de Testes**: Testes unitários e de integração abrangentes com Pest
- **Code Style**: Formatação automática com Laravel Pint
- **Análise Estática**: Verificação de tipos e bugs com PHPStan + Larastan
- **CI/CD**: GitHub Actions com validação automática de qualidade
- **PSR-12**: Padrão de codificação seguido rigorosamente

```bash
# Executar todos os testes
composer test

# Executar testes com cobertura
composer test-coverage

# Verificar code style
vendor/bin/pint --test

# Análise estática
vendor/bin/phpstan analyse
```

## Instalação

Instale o pacote via Composer:

```bash
composer require ferreiramg/tecnospeed-serasa
```

### Publicar Configuração

Publique o arquivo de configuração:

```bash
php artisan vendor:publish --tag=tecnospeed-serasa-config
```

### Configuração do Ambiente

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
# Ambiente (homologacao ou producao)
TECNOSPEED_SERASA_ENVIRONMENT=homologacao

# URLs da API (normalmente não precisam ser alteradas)
TECNOSPEED_SERASA_BASE_URL_HML=https://api.consultanegativacao.com.br/v2/homologacao
TECNOSPEED_SERASA_BASE_URL_PROD=https://api.consultanegativacao.com.br/v2

# Credenciais de autenticação (fornecidas pela Tecnospeed)
TECNOSPEED_SERASA_CNPJ_SH=seu_cnpj_software_house
TECNOSPEED_SERASA_TOKEN_SH=seu_token_software_house
TECNOSPEED_SERASA_CNPJ_USUARIO=cnpj_do_usuario_final
TECNOSPEED_SERASA_LOGIN=login_scc
TECNOSPEED_SERASA_PASSWORD=senha_scc

# Configurações opcionais
TECNOSPEED_SERASA_TIMEOUT=30
TECNOSPEED_SERASA_RETRIES=3
TECNOSPEED_SERASA_CACHE_ENABLED=true
TECNOSPEED_SERASA_CACHE_TTL=300
```

## Uso Básico

### Usando a Facade

```php
use Ferreiramg\TecnospeedSerasa\Facades\TecnospeedSerasa;

// Validar documento
$documento = '11144477735';
if (TecnospeedSerasa::validarDocumento($documento)) {
    // Solicitar consulta
    $response = TecnospeedSerasa::consultarPorDocumento($documento, 602, 'PR', 'HTML');
    
    if ($response->isSuccess()) {
        $protocolo = $response->getProtocolo();
        echo "Protocolo: {$protocolo}";
        
        // Consultar resultado posteriormente
        $resultado = TecnospeedSerasa::consultarProtocolo($protocolo);
        
        if ($resultado->isCompleted()) {
            echo "HTML: " . $resultado->getHtml();
        }
    }
}
```

### Usando Injeção de Dependência

```php
use Ferreiramg\TecnospeedSerasa\Services\TecnospeedSerasaService;
use Ferreiramg\TecnospeedSerasa\DTOs\ConsultationRequest;

class MeuController extends Controller
{
    public function consultar(TecnospeedSerasaService $serasa, $documento)
    {
        try {
            // Método 1: Usando método de conveniência
            $response = $serasa->consultarPorDocumento($documento, 602, 'PR', 'HTML');
            
            // Método 2: Usando DTO (mais flexível)
            $request = new ConsultationRequest($documento, 602, 'PR', 'HTML');
            $response = $serasa->solicitarConsulta($request);
            
            return response()->json([
                'protocolo' => $response->getProtocolo(),
                'status' => $response->getStatus()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

## Tipos de Consulta Disponíveis

| Código | Descrição |
|--------|-----------|
| 1 | Crednet PF ou PJ TOP |
| 600 | Relatório Básico PF |
| 601 | Relatório Básico PJ |
| 602 | Relatório Avançado PF |
| 603 | Relatório Avançado PJ |

## Métodos Disponíveis

### TecnospeedSerasaService

#### `consultarPorDocumento(string $documento, int $codConsulta = 602, ?string $uf = null, string $retorno = 'HTML'): ConsultationResponse`
Método de conveniência para solicitar consulta.

#### `solicitarConsulta(ConsultationRequest $request): ConsultationResponse`
Solicita consulta usando objeto DTO.

#### `consultarProtocolo(string $protocolo): ConsultationResponse`
Consulta o resultado usando o protocolo retornado.

#### `validarDocumento(string $documento): bool`
Valida se o documento (CPF ou CNPJ) é válido.

#### `getTiposConsulta(): array`
Retorna os tipos de consulta disponíveis.

### ConsultationResponse

#### Métodos de Dados da Solicitação
- `getProtocolo(): ?string` - Protocolo da consulta
- `getStatus(): ?string` - Status da consulta
- `getDocumento(): ?string` - Documento consultado
- `getCodConsulta(): ?string` - Código da consulta

#### Métodos de Resultado
- `getResultado(): ?string` - Resultado da consulta
- `getHtml(): ?string` - HTML do resultado (quando disponível)

#### Métodos de Erro
- `getCode(): ?int` - Código de erro HTTP
- `getMessage(): ?string` - Mensagem de erro
- `getErrors(): array` - Lista de erros detalhados
- `getErrorMessage(): ?string` - Primeira mensagem de erro

#### Métodos de Status
- `isSuccess(): bool` - Verifica se a solicitação foi bem-sucedida
- `isProcessing(): bool` - Verifica se está processando
- `isCompleted(): bool` - Verifica se foi finalizada
- `isUnauthorized(): bool` - Verifica se erro 401
- `isUnprocessableEntity(): bool` - Verifica se erro 422

## Fluxo de Consulta Assíncrona

A API Tecnospeed Serasa funciona de forma assíncrona:

1. **Solicitar Consulta**: Envie os dados e receba um protocolo
2. **Aguardar Processamento**: A consulta é processada em background
3. **Consultar Resultado**: Use o protocolo para obter o resultado

```php
// 1. Solicitar consulta
$response = TecnospeedSerasa::consultarPorDocumento('11144477735', 602);
$protocolo = $response->getProtocolo();

// 2. Aguardar (pode implementar polling, webhook, etc.)
sleep(5);

// 3. Consultar resultado
$resultado = TecnospeedSerasa::consultarProtocolo($protocolo);

if ($resultado->isCompleted()) {
    $html = $resultado->getHtml();
    // Processar resultado
}
```

## Exemplo de Controller Laravel Completo

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ferreiramg\TecnospeedSerasa\Facades\TecnospeedSerasa;

class SerasaController extends Controller
{
    public function solicitarConsulta(Request $request)
    {
        $request->validate([
            'documento' => 'required|string|min:11|max:18',
            'codConsulta' => 'integer|in:1,600,601,602,603',
            'uf' => 'string|size:2',
            'retorno' => 'string|in:HTML,JSON'
        ]);

        $documento = preg_replace('/[^0-9]/', '', $request->documento);

        if (!TecnospeedSerasa::validarDocumento($documento)) {
            return response()->json([
                'success' => false,
                'message' => 'Documento inválido'
            ], 400);
        }

        try {
            $response = TecnospeedSerasa::consultarPorDocumento(
                $documento,
                $request->input('codConsulta', 602),
                $request->input('uf'),
                $request->input('retorno', 'HTML')
            );

            if ($response->isSuccess()) {
                return response()->json([
                    'success' => true,
                    'protocolo' => $response->getProtocolo(),
                    'status' => $response->getStatus()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response->getErrorMessage()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function consultarProtocolo($protocolo)
    {
        try {
            $response = TecnospeedSerasa::consultarProtocolo($protocolo);

            return response()->json([
                'success' => true,
                'completed' => $response->isCompleted(),
                'processing' => $response->isProcessing(),
                'status' => $response->getStatus(),
                'html' => $response->getHtml()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
```

## Tratamento de Erros

### Códigos de Erro Comuns

- **401**: Credenciais incorretas
- **422**: Dados inválidos ou empresa não cadastrada

```php
use Ferreiramg\TecnospeedSerasa\Exceptions\TecnospeedSerasaException;

try {
    $response = TecnospeedSerasa::consultarPorDocumento($documento);
} catch (TecnospeedSerasaException $e) {
    if (strpos($e->getMessage(), 'CNPJ ou TOKEN incorretos') !== false) {
        // Problema de autenticação
    } elseif (strpos($e->getMessage(), 'Empresa não encontrada') !== false) {
        // Empresa precisa ser cadastrada
    }
}
```

## Configuração

O arquivo `config/tecnospeed-serasa.php` permite configurar:

```php
return [
    'base_url' => [
        'homologacao' => 'https://api.consultanegativacao.com.br/v2/homologacao',
        'producao' => 'https://api.consultanegativacao.com.br/v2',
    ],
    'environment' => 'homologacao',
    'credentials' => [
        'cnpjsh' => env('TECNOSPEED_SERASA_CNPJ_SH'),
        'tokensh' => env('TECNOSPEED_SERASA_TOKEN_SH'),
        'cnpjUsuario' => env('TECNOSPEED_SERASA_CNPJ_USUARIO'),
        'login' => env('TECNOSPEED_SERASA_LOGIN'),
        'password' => env('TECNOSPEED_SERASA_PASSWORD'),
    ],
    'tipos_consulta' => [
        1 => 'Crednet PF ou PJ TOP',
        600 => 'Relatório Básico PF',
        601 => 'Relatório Básico PJ',
        602 => 'Relatório Avançado PF',
        603 => 'Relatório Avançado PJ',
    ],
];
```

## Testes

Execute os testes com:

```bash
# Executar todos os testes
composer test

# Executar testes com cobertura de código
composer test-coverage

# Verificar formatação do código
vendor/bin/pint --test

# Corrigir formatação automaticamente
vendor/bin/pint

# Análise estática de código
vendor/bin/phpstan analyse

# Executar todos os checks de qualidade
composer test-coverage && vendor/bin/pint --test && vendor/bin/phpstan analyse
```

### Estatísticas de Teste

- **✅ 41 testes** executando com sucesso
- **✅ 104 assertions** validadas
- **✅ 83%+ cobertura** de código
- **✅ Code Style** validado com Laravel Pint
- **✅ Static Analysis** nível 4 com PHPStan

## Requisitos

- PHP 8.2 ou superior
- Laravel 12.0 ou superior
- ext-json
- GuzzleHttp 7.0 ou superior

## Credenciais

Para usar este pacote, você precisa:

1. **Contrato com Tecnospeed**: CNPJ SH e Token SH
2. **Cadastro de Empresa**: CNPJ do usuário final
3. **Credenciais SCC**: Login e senha fornecidos após cadastro

## Segurança

Se você descobrir alguma vulnerabilidade de segurança, por favor envie um e-mail para luis@lpdeveloper.com.br ao invés de usar o issue tracker.

## Créditos

- [Ferreiramg](https://github.com/Ferreiramg)
- [Todos os Contribuidores](../../contributors)

## Licença

Este pacote é open source e está licenciado sob a [Licença MIT](LICENSE).

## Referências

- [Documentação Oficial - Solicitação de Consulta](https://atendimento.tecnospeed.com.br/hc/pt-br/articles/26521344252567-Solicita%C3%A7%C3%A3o-de-consulta)
- [Documentação Oficial - Consulta do Protocolo](https://atendimento.tecnospeed.com.br/hc/pt-br/articles/26524166896151-Consulta-do-protocolo)
- [API Tecnospeed Serasa](https://tecnospeed.com.br/consulta-credito/)
