# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto segue [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [Unreleased]

## [1.0.0] - 2025-07-16

### Adicionado
- Implementação inicial do pacote Tecnospeed Serasa
- Autenticação OAuth2 com a API Tecnospeed
- Consulta de situação creditícia por CPF/CNPJ
- Validação de CPF e CNPJ
- Cache automático de tokens de autenticação
- Facade Laravel para facilitar o uso
- Service Provider para integração com Laravel
- DTOs para requisições e respostas
- Tratamento de exceções personalizado
- Testes unitários básicos
- Documentação completa
- Exemplos de uso
- Suporte a Laravel 12 e PHP 8.3

### Recursos
- Configuração flexível via arquivo de configuração
- Retry automático em caso de falha
- Timeout configurável para requisições
- Suporte a cache com TTL configurável
- Validação robusta de documentos
- Resposta estruturada com métodos auxiliares
