<?php

namespace Database\Seeders;

use App\Models\ConfiguracionVariable;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $variables = [
            // ═══════════════════════════════════════════
            // GENERAL
            // ═══════════════════════════════════════════
            [
                'grupo' => 'general',
                'clave' => 'nota_aprobacion',
                'valor' => '6',
                'tipo' => 'numero',
                'nombre' => 'Nota mínima de aprobación',
                'descripcion' => 'Nota mínima necesaria para aprobar evaluaciones y materias.',
                'origen' => 'config',
                'requerida' => true,
                'orden' => 1,
            ],
            [
                'grupo' => 'general',
                'clave' => 'nota_minima_regular',
                'valor' => '4',
                'tipo' => 'numero',
                'nombre' => 'Nota mínima para regularidad',
                'descripcion' => 'Nota mínima para alcanzar condición de regular (el alumno debe rendir examen final). Por debajo de esta nota queda desaprobado.',
                'origen' => 'config',
                'requerida' => true,
                'orden' => 2,
            ],
            [
                'grupo' => 'general',
                'clave' => 'asistencia_minima',
                'valor' => '75',
                'tipo' => 'numero',
                'nombre' => 'Asistencia mínima (%)',
                'descripcion' => 'Porcentaje mínimo de asistencia requerido para mantener la regularidad.',
                'origen' => 'config',
                'requerida' => true,
                'orden' => 2,
            ],
            [
                'grupo' => 'general',
                'clave' => 'max_solicitudes_cambio',
                'valor' => '3',
                'tipo' => 'numero',
                'nombre' => 'Máx. solicitudes de cambio',
                'descripcion' => 'Cantidad máxima de solicitudes de cambio de turno que un alumno puede realizar.',
                'origen' => 'config',
                'orden' => 3,
            ],
            [
                'grupo' => 'general',
                'clave' => 'dias_inactividad',
                'valor' => '30',
                'tipo' => 'numero',
                'nombre' => 'Días de inactividad',
                'descripcion' => 'Días sin actividad para considerar un alumno inactivo.',
                'origen' => 'config',
                'orden' => 4,
            ],
            [
                'grupo' => 'general',
                'clave' => 'nombre_institucion',
                'valor' => 'FACULTAD REGIONAL LA PLATA',
                'tipo' => 'texto',
                'nombre' => 'Nombre de la institución',
                'descripcion' => 'Nombre de la facultad/institución que aparece en exports y documentos oficiales.',
                'origen' => 'config',
                'requerida' => true,
                'orden' => 5,
            ],
            [
                'grupo' => 'general',
                'clave' => 'sigla_institucion',
                'valor' => 'FRLP',
                'tipo' => 'texto',
                'nombre' => 'Sigla de la institución',
                'descripcion' => 'Abreviatura de la facultad (ej: FRLP, FRA, FRBA) para uso en títulos cortos.',
                'origen' => 'config',
                'requerida' => true,
                'orden' => 6,
            ],

            // ═══════════════════════════════════════════
            // EVALUACIONES
            // ═══════════════════════════════════════════
            [
                'grupo' => 'evaluaciones',
                'clave' => 'tipos_evaluacion',
                'valor' => json_encode([
                    'parcial' => 'Parcial',
                    'recuperatorio' => 'Recuperatorio',
                    'examen_final' => 'Examen Final',
                    'trabajo_practico' => 'Trabajo Práctico',
                    'integrador' => 'Integrador',
                ], JSON_UNESCAPED_UNICODE),
                'tipo' => 'json',
                'nombre' => 'Tipos de evaluación',
                'descripcion' => 'Tipos de evaluaciones disponibles en el sistema (clave interna => etiqueta visible).',
                'origen' => 'config',
                'requerida' => true,
                'orden' => 1,
            ],
            [
                'grupo' => 'evaluaciones',
                'clave' => 'instancias_evaluacion',
                'valor' => json_encode([
                    '1' => 'Primera instancia (1er Parcial)',
                    '2' => 'Segunda instancia (2do Parcial)',
                    '3' => 'Tercera instancia (3er Parcial)',
                ], JSON_UNESCAPED_UNICODE),
                'tipo' => 'json',
                'nombre' => 'Instancias de evaluación',
                'descripcion' => 'Instancias de evaluación disponibles (número => descripción).',
                'origen' => 'config',
                'requerida' => true,
                'orden' => 2,
            ],

            // ═══════════════════════════════════════════
            // MATERIAS
            // ═══════════════════════════════════════════
            [
                'grupo' => 'materias',
                'clave' => 'tipos_materia',
                'valor' => json_encode([
                    'obligatoria' => 'Obligatoria',
                    'optativa' => 'Optativa',
                    'nivelacion' => 'Nivelación',
                ], JSON_UNESCAPED_UNICODE),
                'tipo' => 'json',
                'nombre' => 'Tipos de materia',
                'descripcion' => 'Tipos de materias disponibles.',
                'origen' => 'config',
                'orden' => 1,
            ],

            // ═══════════════════════════════════════════
            // CONDICIONES PARTICULARES
            // ═══════════════════════════════════════════
            [
                'grupo' => 'condiciones',
                'clave' => 'tipos_condicion',
                'valor' => json_encode([
                    'discapacidad' => 'Discapacidad',
                    'enfermedad_cronica' => 'Enfermedad Crónica',
                    'situacion_laboral' => 'Situación Laboral',
                    'situacion_familiar' => 'Situación Familiar',
                    'condicionalidad_academica' => 'Condicionalidad Académica',
                    'otra' => 'Otra',
                ], JSON_UNESCAPED_UNICODE),
                'tipo' => 'json',
                'nombre' => 'Tipos de condición particular',
                'descripcion' => 'Tipos de condiciones particulares de alumnos.',
                'origen' => 'config',
                'orden' => 1,
            ],
        ];

        foreach ($variables as $data) {
            ConfiguracionVariable::updateOrCreate(
                ['clave' => $data['clave']],
                $data
            );
        }

        // Eliminar variables que ya no deben existir (estados lógicos del sistema)
        $clavesAEliminar = [
            'estados_comision',
            'estados_inscripcion',
            'estados_ingreso',
            'estados_cursada',
            'estados_asistencia',
            'tipos_solicitud',
            'estados_solicitud',
            'estados_trayectoria',
            'estados_usuario',
            'tipos_ingreso',
            'turnos',
            'modalidades',
            'municipios_default',
            'estados_documentacion',
        ];

        ConfiguracionVariable::whereIn('clave', $clavesAEliminar)->delete();
    }
}
