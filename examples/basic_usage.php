<?php

require_once __DIR__.'/vendor/autoload.php';

use Ferreiramg\TecnospeedSerasa\DTOs\ConsultationRequest;
use Ferreiramg\TecnospeedSerasa\Exceptions\TecnospeedSerasaException;
use Ferreiramg\TecnospeedSerasa\Services\TecnospeedSerasaService;

// Configuração
$config = [
    'base_url' => [
        'homologacao' => 'https://api.consultanegativacao.com.br/v2/homologacao',
        'producao' => 'https://api.consultanegativacao.com.br/v2',
    ],
    'environment' => 'homologacao',
    'credentials' => [
        'cnpjsh' => 'seu_cnpj_sh',
        'tokensh' => 'seu_token_sh',
        'cnpjUsuario' => 'cnpj_usuario',
        'login' => 'seu_login',
        'password' => 'sua_password',
    ],
    'timeout' => 30,
    'retries' => 3,
    'cache' => [
        'enabled' => true,
        'ttl' => 300,
    ],
    'endpoints' => [
        'consulta_assincrona' => '/consultas/assincrona',
        'consulta_protocolo' => '/consultas',
    ],
    'tipos_consulta' => [
        1 => 'Crednet PF ou PJ TOP',
        600 => 'Relatório Básico PF',
        601 => 'Relatório Básico PJ',
        602 => 'Relatório Avançado PF',
        603 => 'Relatório Avançado PJ',
    ],
];

// Instanciando o serviço
$serasaService = new TecnospeedSerasaService($config);

try {
    // Validar documento antes de consultar
    $documento = '11144477735'; // CPF ou CNPJ (apenas números)

    if (! $serasaService->validarDocumento($documento)) {
        echo "Documento inválido: {$documento}\n";
        exit(1);
    }

    echo "Documento válido: {$documento}\n";

    // Método 1: Usando consultarPorDocumento (método de conveniência)
    echo "Solicitando consulta...\n";
    $response = $serasaService->consultarPorDocumento($documento, 602, 'PR', 'HTML');

    if ($response->isSuccess()) {
        echo "Consulta solicitada com sucesso!\n";
        echo 'Protocolo: '.$response->getProtocolo()."\n";
        echo 'Status: '.$response->getStatus()."\n";
        echo 'Documento: '.$response->getDocumento()."\n";
        echo 'Código Consulta: '.$response->getCodConsulta()."\n";

        // Aguardar processamento e consultar resultado
        $protocolo = $response->getProtocolo();

        echo "\nConsultando resultado do protocolo: {$protocolo}\n";

        // Em um cenário real, você pode implementar polling ou webhook
        sleep(5); // Aguarda 5 segundos

        $resultado = $serasaService->consultarProtocolo($protocolo);

        if ($resultado->isCompleted()) {
            echo "Consulta finalizada!\n";
            if ($resultado->getHtml()) {
                echo "Resultado HTML disponível\n";
                // Salvar ou exibir o HTML
                file_put_contents('resultado_consulta.html', $resultado->getHtml());
                echo "Resultado salvo em: resultado_consulta.html\n";
            }
        } elseif ($resultado->isProcessing()) {
            echo "Consulta ainda em processamento. Tente novamente em alguns minutos.\n";
        } else {
            echo 'Erro na consulta: '.$resultado->getErrorMessage()."\n";
        }

    } else {
        echo 'Erro na solicitação: '.$response->getErrorMessage()."\n";

        if ($response->isUnauthorized()) {
            echo "Problema de autenticação - verifique suas credenciais\n";
        } elseif ($response->isUnprocessableEntity()) {
            echo "Problema nos dados enviados:\n";
            foreach ($response->getErrors() as $error) {
                echo '  - '.($error['message'] ?? 'Erro desconhecido')."\n";
            }
        }
    }

    // Método 2: Usando objeto ConsultationRequest (mais flexível)
    echo "\n--- Exemplo com ConsultationRequest ---\n";

    $request = new ConsultationRequest($documento, 601, 'PR', 'HTML');
    $response2 = $serasaService->solicitarConsulta($request);

    if ($response2->isSuccess()) {
        echo "Segunda consulta solicitada com sucesso!\n";
        echo 'Protocolo: '.$response2->getProtocolo()."\n";
    }

    // Mostrar tipos de consulta disponíveis
    echo "\n--- Tipos de consulta disponíveis ---\n";
    foreach ($serasaService->getTiposConsulta() as $codigo => $descricao) {
        echo "{$codigo}: {$descricao}\n";
    }

} catch (TecnospeedSerasaException $e) {
    echo 'Erro: '.$e->getMessage()."\n";
    if ($e->getPrevious()) {
        echo 'Erro anterior: '.$e->getPrevious()->getMessage()."\n";
    }
} catch (Exception $e) {
    echo 'Erro inesperado: '.$e->getMessage()."\n";
}
