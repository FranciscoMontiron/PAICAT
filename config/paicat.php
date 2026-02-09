<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nota de aprobación
    |--------------------------------------------------------------------------
    |
    | Nota mínima para aprobar en el curso de ingreso UTN.
    | Por defecto es 6, puede configurarse en .env con NOTA_APROBACION=6
    |
    */
    'nota_aprobacion' => env('NOTA_APROBACION', 6),

    /*
    |--------------------------------------------------------------------------
    | Porcentaje mínimo de asistencia
    |--------------------------------------------------------------------------
    |
    | Porcentaje mínimo de asistencia requerido para no quedar libre.
    | Por defecto es 75%
    |
    */
    'asistencia_minima' => env('ASISTENCIA_MINIMA', 75),

    /*
    |--------------------------------------------------------------------------
    | Instancias de evaluación
    |--------------------------------------------------------------------------
    |
    | Número de instancias de parciales permitidas (1, 2, 3...)
    |
    */
    'instancias_evaluacion' => [
        1 => 'Primera instancia (1er Parcial)',
        2 => 'Segunda instancia (2do Parcial)',
        3 => 'Tercera instancia (3er Parcial)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de evaluación
    |--------------------------------------------------------------------------
    |
    | Tipos de evaluaciones disponibles
    |
    */
    'tipos_evaluacion' => [
        'parcial' => 'Parcial',
        'recuperatorio' => 'Recuperatorio',
        'examen_final' => 'Examen Final',
        'trabajo_practico' => 'Trabajo Práctico',
        'integrador' => 'Integrador',
    ],

    /*
    |--------------------------------------------------------------------------
    | Municipios por defecto
    |--------------------------------------------------------------------------
    |
    | Lista de municipios/sedes del curso de ingreso
    |
    */
    'municipios_default' => [
        'La Plata',
        'Chascomús',
        'Brandsen',
        'Magdalena',
    ],

    /*
    |--------------------------------------------------------------------------
    | Límite de solicitudes de cambio
    |--------------------------------------------------------------------------
    |
    | Cantidad máxima de solicitudes de cambio (comisión, modalidad, turno, carrera)
    | que un alumno puede realizar por año. 0 = sin límite.
    |
    */
    'max_solicitudes_cambio' => env('MAX_SOLICITUDES_CAMBIO', 3),

    /*
    |--------------------------------------------------------------------------
    | Días de inactividad
    |--------------------------------------------------------------------------
    |
    | Cantidad de días sin actividad (asistencia, notas) tras los cuales
    | un alumno cursando se considera inactivo.
    |
    */
    'dias_inactividad' => env('DIAS_INACTIVIDAD', 30),

    /*
    |--------------------------------------------------------------------------
    | Porcentaje mínimo de asistencia (alias)
    |--------------------------------------------------------------------------
    */
    'porcentaje_asistencia_minimo' => env('ASISTENCIA_MINIMA', 75),
];
