<?php

namespace Ferreiramg\TecnospeedSerasa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ferreiramg\TecnospeedSerasa\DTOs\ConsultationResponse solicitarConsulta(\Ferreiramg\TecnospeedSerasa\DTOs\ConsultationRequest $request)
 * @method static \Ferreiramg\TecnospeedSerasa\DTOs\ConsultationResponse consultarPorDocumento(string $documento, int $codConsulta = 602, ?string $uf = null, string $retorno = 'HTML')
 * @method static \Ferreiramg\TecnospeedSerasa\DTOs\ConsultationResponse consultarProtocolo(string $protocolo)
 * @method static bool validarDocumento(string $documento)
 * @method static array getTiposConsulta()
 * @method static array getTiposRetorno()
 */
class TecnospeedSerasa extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'tecnospeed-serasa';
    }
}
