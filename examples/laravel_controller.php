<?php

// Exemplo de uso em uma aplicação Laravel

namespace App\Http\Controllers;

use Ferreiramg\TecnospeedSerasa\Exceptions\TecnospeedSerasaException;
use Ferreiramg\TecnospeedSerasa\Facades\TecnospeedSerasa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SerasaController extends Controller
{
    /**
     * Solicitar consulta Serasa
     */
    public function solicitarConsulta(Request $request): JsonResponse
    {
        $request->validate([
            'documento' => 'required|string|min:11|max:18',
            'codConsulta' => 'integer|in:1,600,601,602,603',
            'uf' => 'string|size:2',
            'retorno' => 'string|in:HTML,JSON',
        ]);

        $documento = preg_replace('/[^0-9]/', '', $request->documento);
        $codConsulta = $request->input('codConsulta', 602);
        $uf = $request->input('uf');
        $retorno = $request->input('retorno', 'HTML');

        try {
            // Validar documento
            if (! TecnospeedSerasa::validarDocumento($documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento inválido',
                ], 400);
            }

            // Realizar solicitação de consulta
            $response = TecnospeedSerasa::consultarPorDocumento($documento, $codConsulta, $uf, $retorno);

            if ($response->isSuccess()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'protocolo' => $response->getProtocolo(),
                        'status' => $response->getStatus(),
                        'documento' => $response->getDocumento(),
                        'codConsulta' => $response->getCodConsulta(),
                    ],
                    'message' => 'Consulta solicitada com sucesso. Use o protocolo para verificar o resultado.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response->getErrorMessage(),
                    'errors' => $response->getErrors(),
                ], 422);
            }

        } catch (TecnospeedSerasaException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na consulta Serasa: '.$e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
            ], 500);
        }
    }

    /**
     * Consultar resultado por protocolo
     */
    public function consultarProtocolo(Request $request, string $protocolo): JsonResponse
    {
        try {
            $response = TecnospeedSerasa::consultarProtocolo($protocolo);

            if ($response->isCompleted()) {
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'data' => [
                        'protocolo' => $protocolo,
                        'status' => $response->getStatus(),
                        'resultado' => $response->getResultado(),
                        'html' => $response->getHtml(),
                    ],
                ]);
            } elseif ($response->isProcessing()) {
                return response()->json([
                    'success' => true,
                    'status' => 'processing',
                    'message' => 'Consulta ainda em processamento. Tente novamente em alguns minutos.',
                    'data' => [
                        'protocolo' => $protocolo,
                        'status' => $response->getStatus(),
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response->getErrorMessage(),
                    'errors' => $response->getErrors(),
                ], 422);
            }

        } catch (TecnospeedSerasaException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na consulta do protocolo: '.$e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
            ], 500);
        }
    }

    /**
     * Validar documento
     */
    public function validarDocumento(Request $request): JsonResponse
    {
        $request->validate([
            'documento' => 'required|string',
        ]);

        $documento = preg_replace('/[^0-9]/', '', $request->documento);
        $isValid = TecnospeedSerasa::validarDocumento($documento);

        return response()->json([
            'success' => true,
            'valid' => $isValid,
            'documento' => $documento,
            'tipo' => strlen($documento) === 11 ? 'CPF' : (strlen($documento) === 14 ? 'CNPJ' : 'INDEFINIDO'),
        ]);
    }

    /**
     * Listar tipos de consulta disponíveis
     */
    public function tiposConsulta(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'tipos_consulta' => TecnospeedSerasa::getTiposConsulta(),
            'tipos_retorno' => TecnospeedSerasa::getTiposRetorno(),
        ]);
    }

    /**
     * Exemplo de fluxo completo com polling
     */
    public function consultaCompleta(Request $request): JsonResponse
    {
        $request->validate([
            'documento' => 'required|string|min:11|max:18',
            'codConsulta' => 'integer|in:1,600,601,602,603',
            'uf' => 'string|size:2',
            'max_tentativas' => 'integer|min:1|max:10',
        ]);

        $documento = preg_replace('/[^0-9]/', '', $request->documento);
        $codConsulta = $request->input('codConsulta', 602);
        $uf = $request->input('uf');
        $maxTentativas = $request->input('max_tentativas', 5);

        try {
            // 1. Solicitar consulta
            $response = TecnospeedSerasa::consultarPorDocumento($documento, $codConsulta, $uf, 'HTML');

            if (! $response->isSuccess()) {
                return response()->json([
                    'success' => false,
                    'message' => $response->getErrorMessage(),
                ], 422);
            }

            $protocolo = $response->getProtocolo();

            // 2. Polling para verificar resultado
            $tentativas = 0;
            do {
                sleep(3); // Aguarda 3 segundos entre tentativas
                $tentativas++;

                $resultado = TecnospeedSerasa::consultarProtocolo($protocolo);

                if ($resultado->isCompleted()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Consulta finalizada com sucesso',
                        'data' => [
                            'protocolo' => $protocolo,
                            'tentativas' => $tentativas,
                            'html' => $resultado->getHtml(),
                            'status' => $resultado->getStatus(),
                        ],
                    ]);
                }

            } while ($tentativas < $maxTentativas && $resultado->isProcessing());

            // Se chegou aqui, não finalizou no tempo limite
            return response()->json([
                'success' => false,
                'message' => 'Consulta não finalizou no tempo limite. Use o protocolo para verificar posteriormente.',
                'data' => [
                    'protocolo' => $protocolo,
                    'tentativas' => $tentativas,
                    'status' => 'timeout',
                ],
            ], 202);

        } catch (TecnospeedSerasaException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na consulta: '.$e->getMessage(),
            ], 500);
        }
    }
}
