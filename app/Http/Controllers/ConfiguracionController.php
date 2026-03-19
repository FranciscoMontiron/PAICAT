<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionVariable;
use App\Models\ConfiguracionHistorial;
use App\Services\ConfiguracionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $variables = ConfiguracionVariable::orderBy('grupo')->orderBy('orden')->get();
        $grupos = $variables->groupBy('grupo');

        $gruposLabels = [
            'general' => 'Parámetros Generales',
            'evaluaciones' => 'Evaluaciones',
            'inscripciones' => 'Inscripciones',
            'materias' => 'Materias',
            'condiciones' => 'Condiciones Particulares',
        ];

        $gruposIconos = [
            'general' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'evaluaciones' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'inscripciones' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'materias' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'condiciones' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        ];

        // Cargar últimos cambios por variable
        $ultimosCambios = ConfiguracionHistorial::select('configuracion_variable_id')
            ->selectRaw('MAX(id) as last_id')
            ->groupBy('configuracion_variable_id')
            ->pluck('last_id');

        $historialReciente = ConfiguracionHistorial::whereIn('id', $ultimosCambios)
            ->with('usuario')
            ->get()
            ->keyBy('configuracion_variable_id');

        $faltantes = ConfiguracionService::requeridasFaltantes();

        return view('configuracion.index', compact(
            'grupos', 'gruposLabels', 'gruposIconos',
            'historialReciente', 'faltantes'
        ));
    }

    public function update(Request $request, ConfiguracionVariable $variable)
    {
        if (!$variable->editable) {
            return back()->with('error', 'Esta variable no es editable.');
        }

        if ($variable->tipo === 'json') {
            $opciones = [];
            $normalizar = (bool) $variable->tabla_sincronizacion;
            foreach ($request->input('opciones', []) as $opcion) {
                $clave = trim($opcion['clave'] ?? '');
                $valor = trim($opcion['valor'] ?? '');
                if ($valor === '') continue;

                // Si no viene clave (opción nueva), generarla desde la etiqueta
                if ($clave === '') {
                    $clave = mb_strtolower(trim($valor));
                    $clave = str_replace(' ', '_', $clave);
                    // Eliminar acentos y caracteres especiales
                    $clave = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $clave);
                    $clave = preg_replace('/[^a-z0-9_]/', '', $clave);
                }

                if ($normalizar) {
                    $clave = mb_strtoupper(str_replace(' ', '_', $clave));
                    $valor = mb_strtoupper($valor);
                }

                $opciones[$clave] = $valor;
            }

            if (empty($opciones) && $variable->requerida) {
                return back()->with('error', 'Esta variable es requerida. Debe tener al menos una opción.')->with('grupo', $variable->grupo);
            }

            ConfiguracionService::set(
                $variable->clave,
                $opciones,
                auth()->id(),
                $request->input('motivo')
            );
        } else {
            $rules = ['valor' => 'required'];
            if ($variable->tipo === 'numero') {
                $rules['valor'] = 'required|numeric';
            }
            $request->validate($rules);

            ConfiguracionService::set(
                $variable->clave,
                $request->input('valor'),
                auth()->id(),
                $request->input('motivo')
            );
        }

        return back()->with('success', "Variable \"{$variable->nombre}\" actualizada correctamente.")->with('grupo', $variable->grupo);
    }

    public function sincronizar(ConfiguracionVariable $variable)
    {
        if (!$variable->tabla_sincronizacion) {
            return back()->with('error', 'Esta variable no tiene tabla de sincronización configurada.')->with('grupo', $variable->grupo);
        }

        try {
            $registros = DB::table($variable->tabla_sincronizacion)
                ->orderBy('nombre')
                ->pluck('nombre')
                ->filter()
                ->unique();

            if ($registros->isEmpty()) {
                return back()->with('warning', "La tabla {$variable->tabla_sincronizacion} no tiene registros.")->with('grupo', $variable->grupo);
            }

            // Construir key-value: normalizar claves y etiquetas a mayúsculas
            $opciones = [];
            foreach ($registros as $nombre) {
                $clave = mb_strtoupper(trim($nombre));
                $clave = str_replace(' ', '_', $clave);
                $opciones[$clave] = mb_strtoupper(trim($nombre));
            }

            // Normalizar opciones existentes a mayúsculas antes de mergear
            $actuales = json_decode($variable->valor, true) ?? [];
            $actualesNormalizados = [];
            foreach ($actuales as $k => $v) {
                $kNorm = mb_strtoupper(str_replace(' ', '_', trim($k)));
                $actualesNormalizados[$kNorm] = mb_strtoupper(trim($v));
            }

            // Merge: las nuevas de sysacad sobreescriben
            $merged = array_merge($actualesNormalizados, $opciones);

            ConfiguracionService::set(
                $variable->clave,
                $merged,
                auth()->id(),
                'Sincronización automática desde ' . $variable->tabla_sincronizacion,
                'sincronizacion'
            );

            return back()->with('success', "Variable \"{$variable->nombre}\" sincronizada. Se encontraron " . count($opciones) . " valores en {$variable->tabla_sincronizacion}.")->with('grupo', $variable->grupo);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al sincronizar: ' . $e->getMessage())->with('grupo', $variable->grupo);
        }
    }

    public function historial(ConfiguracionVariable $variable)
    {
        $historial = $variable->historial()
            ->with('usuario')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($h) use ($variable) {
                return [
                    'id' => $h->id,
                    'fecha' => $h->created_at->format('d/m/Y H:i'),
                    'usuario' => $h->usuario ? $h->usuario->nombre_completo : 'Sistema',
                    'tipo_cambio' => $h->tipo_cambio,
                    'motivo' => $h->motivo,
                    'valor_anterior' => $variable->tipo === 'json'
                        ? json_decode($h->valor_anterior, true)
                        : $h->valor_anterior,
                    'valor_nuevo' => $variable->tipo === 'json'
                        ? json_decode($h->valor_nuevo, true)
                        : $h->valor_nuevo,
                ];
            });

        return response()->json($historial);
    }
}
