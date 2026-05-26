@extends('layouts.app')
@section('title', 'Desarrollador - Guia de Estilos')
@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-utn-dark to-utn-dark-light p-6">
            <h1 class="text-2xl font-bold text-white">Guia de Estilos & Accesibilidad</h1>
            <p class="text-white/70 mt-1">Manual de Identidad Visual UTN FRLP - Referencia WCAG 2.1</p>
        </div>
        <div class="h-1 bg-utn-blue"></div>
    </div>

    {{-- ============================================ --}}
    {{-- SECCION 1: TIPOGRAFIA INSTITUCIONAL --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-utn-dark mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
            </svg>
            Tipografia Institucional
        </h2>
        <p class="text-sm text-gray-600 mb-6">Segun Manual de Identidad Visual UTN La Plata - Pagina de tipografia</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Arial Black --}}
            <div class="border border-gray-200 rounded-lg p-5">
                <h3 class="text-lg font-bold text-utn-dark mb-2" style="font-family: 'Arial Black', 'Arial Bold', sans-serif; font-weight: 900;">Arial Black</h3>
                <p class="text-sm text-gray-500 mb-3">Logotipo vertical, titulos principales, headings</p>
                <div style="font-family: 'Arial Black', 'Arial Bold', sans-serif; font-weight: 900;">
                    <p class="text-2xl text-utn-dark tracking-wide">ABCDEFGHIJKLMNOPQRSTUVWXYZ</p>
                    <p class="text-xl text-gray-700">abcdefghijklmnopqrstuvwxyz</p>
                    <p class="text-xl text-gray-700">0123456789</p>
                </div>
                <div class="mt-3 bg-gray-50 rounded p-2">
                    <code class="text-xs text-gray-600">font-family: 'Arial Black', sans-serif; font-weight: 900;</code>
                </div>
            </div>

            {{-- Arial --}}
            <div class="border border-gray-200 rounded-lg p-5">
                <h3 class="text-lg font-bold text-utn-dark mb-2" style="font-family: Arial, Helvetica, sans-serif;">Arial</h3>
                <p class="text-sm text-gray-500 mb-3">Logotipo horizontal, cuerpo de texto, UI general</p>
                <div style="font-family: Arial, Helvetica, sans-serif;">
                    <p class="text-2xl text-utn-dark tracking-wide">ABCDEFGHIJKLMNOPQRSTUVWXYZ</p>
                    <p class="text-xl text-gray-700">abcdefghijklmnopqrstuvwxyz</p>
                    <p class="text-xl text-gray-700">0123456789</p>
                </div>
                <div class="mt-3 bg-gray-50 rounded p-2">
                    <code class="text-xs text-gray-600">font-family: Arial, Helvetica, sans-serif; (default del proyecto)</code>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECCION 2: PALETA PRINCIPAL UTN --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-utn-dark mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
            </svg>
            Colores Institucionales - Paleta Principal
        </h2>
        <p class="text-sm text-gray-600 mb-6">Colores oficiales UTN La Plata: <strong>Azul (Pantone BLUE C)</strong> y <strong>Negro (Pantone BLACK C)</strong>. Ref: Manual de Identidad Visual, pag. 05.</p>

        {{-- Colores Azules --}}
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Familia Azul UTN</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @php
            $azules = [
                ['name' => 'utn-blue', 'hex' => '#008CCC', 'desc' => 'Azul oficial UTN', 'uso' => 'Acentos, barras, iconos', 'ratio' => '3.73', 'wcag' => 'UI/Large', 'class_bg' => 'bg-utn-blue', 'text_white' => true],
                ['name' => 'utn-blue-dark', 'hex' => '#006699', 'desc' => 'Azul oscuro', 'uso' => 'Texto, links, focus', 'ratio' => '6.25', 'wcag' => 'AA', 'class_bg' => 'bg-utn-blue-dark', 'text_white' => true],
                ['name' => 'utn-blue-darker', 'hex' => '#004D73', 'desc' => 'Azul mas oscuro', 'uso' => 'Botones, paginacion', 'ratio' => '9.09', 'wcag' => 'AAA', 'class_bg' => 'bg-utn-blue-darker', 'text_white' => true],
                ['name' => 'utn-blue-light', 'hex' => '#33A3D6', 'desc' => 'Azul claro', 'uso' => 'Solo sobre fondos oscuros', 'ratio' => '2.86', 'wcag' => 'No en blanco', 'class_bg' => 'bg-utn-blue-light', 'text_white' => true],
            ];
            @endphp
            @foreach($azules as $color)
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="{{ $color['class_bg'] }} h-20 flex items-center justify-center">
                    <span class="{{ $color['text_white'] ? 'text-white' : 'text-black' }} font-bold text-sm">Aa</span>
                </div>
                <div class="p-3">
                    <p class="font-mono text-xs font-bold text-gray-800">{{ $color['hex'] }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $color['desc'] }}</p>
                    <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded mt-1 inline-block">{{ $color['name'] }}</code>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs font-bold {{ $color['wcag'] === 'AAA' ? 'text-green-700' : ($color['wcag'] === 'AA' ? 'text-utn-blue-dark' : 'text-yellow-600') }}">
                            {{ $color['ratio'] }}:1
                        </span>
                        <span class="text-xs px-1.5 py-0.5 rounded font-medium
                            {{ $color['wcag'] === 'AAA' ? 'bg-green-100 text-green-800' : ($color['wcag'] === 'AA' ? 'bg-blue-50 text-utn-blue-dark' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $color['wcag'] }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $color['uso'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Colores Oscuros --}}
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Familia Negro UTN</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
            $oscuros = [
                ['name' => 'utn-dark', 'hex' => '#1A1A1A', 'desc' => 'Negro suave', 'uso' => 'Header, footer, nav', 'ratio' => '17.40', 'wcag' => 'AAA', 'class_bg' => 'bg-utn-dark'],
                ['name' => 'utn-dark-light', 'hex' => '#2D2D2D', 'desc' => 'Negro claro', 'uso' => 'Sub-nav, gradientes', 'ratio' => '13.77', 'wcag' => 'AAA', 'class_bg' => 'bg-utn-dark-light'],
                ['name' => 'utn-dark-lighter', 'hex' => '#404040', 'desc' => 'Gris oscuro', 'uso' => 'Subtexto, bordes', 'ratio' => '10.37', 'wcag' => 'AAA', 'class_bg' => 'bg-utn-dark-lighter'],
                ['name' => 'black', 'hex' => '#000000', 'desc' => 'Negro puro (Pantone BLACK C)', 'uso' => 'Referencia institucional', 'ratio' => '21.00', 'wcag' => 'AAA', 'class_bg' => 'bg-black'],
            ];
            @endphp
            @foreach($oscuros as $color)
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="{{ $color['class_bg'] }} h-20 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">Aa</span>
                </div>
                <div class="p-3">
                    <p class="font-mono text-xs font-bold text-gray-800">{{ $color['hex'] }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $color['desc'] }}</p>
                    <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded mt-1 inline-block">{{ $color['name'] }}</code>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs font-bold text-green-700">{{ $color['ratio'] }}:1</span>
                        <span class="text-xs px-1.5 py-0.5 rounded font-medium bg-green-100 text-green-800">{{ $color['wcag'] }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $color['uso'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECCION 3: TABLA DE CONTRASTE WCAG --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-utn-dark mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Verificacion de Contraste WCAG 2.1
        </h2>
        <p class="text-sm text-gray-600 mb-4">Ratios calculados sobre fondo blanco (#FFFFFF). Herramienta: <a href="https://accessibleweb.com/color-contrast-checker/" class="text-utn-blue-dark underline font-medium" target="_blank">accessibleweb.com</a></p>

        {{-- Leyenda WCAG --}}
        <div class="flex flex-wrap gap-4 mb-4 p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-green-600"></span>
                <span class="text-xs text-gray-700"><strong>AAA</strong> (7:1+) Texto normal excelente</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-utn-blue-dark"></span>
                <span class="text-xs text-gray-700"><strong>AA</strong> (4.5:1+) Texto normal minimo</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                <span class="text-xs text-gray-700"><strong>AA Large</strong> (3:1+) Solo texto grande/UI</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-xs text-gray-700"><strong>Falla</strong> (&lt;3:1) No usar para texto</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Combinacion</th>
                        <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Muestra</th>
                        <th class="text-center py-2 px-3 text-xs font-bold text-gray-500 uppercase">Ratio</th>
                        <th class="text-center py-2 px-3 text-xs font-bold text-gray-500 uppercase">AA Normal</th>
                        <th class="text-center py-2 px-3 text-xs font-bold text-gray-500 uppercase">AAA Normal</th>
                        <th class="text-center py-2 px-3 text-xs font-bold text-gray-500 uppercase">AA Large</th>
                        <th class="text-center py-2 px-3 text-xs font-bold text-gray-500 uppercase">UI (3:1)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                    $contrastes = [
                        ['combo' => 'utn-dark sobre blanco', 'fg' => 'text-utn-dark', 'bg' => 'bg-white', 'ratio' => 17.40, 'sample_text' => 'Texto ejemplo', 'sample_style' => 'text-utn-dark'],
                        ['combo' => 'utn-dark-light sobre blanco', 'fg' => 'text-utn-dark-light', 'bg' => 'bg-white', 'ratio' => 13.77, 'sample_text' => 'Texto ejemplo', 'sample_style' => 'text-utn-dark-light'],
                        ['combo' => 'utn-dark-lighter sobre blanco', 'fg' => 'text-utn-dark-lighter', 'bg' => 'bg-white', 'ratio' => 10.37, 'sample_text' => 'Texto ejemplo', 'sample_style' => 'text-utn-dark-lighter'],
                        ['combo' => 'utn-blue-darker sobre blanco', 'fg' => 'text-utn-blue-darker', 'bg' => 'bg-white', 'ratio' => 9.09, 'sample_text' => 'Texto ejemplo', 'sample_style' => 'text-utn-blue-darker'],
                        ['combo' => 'utn-blue-dark sobre blanco', 'fg' => 'text-utn-blue-dark', 'bg' => 'bg-white', 'ratio' => 6.25, 'sample_text' => 'Texto ejemplo', 'sample_style' => 'text-utn-blue-dark'],
                        ['combo' => 'Blanco sobre utn-blue-darker', 'fg' => 'text-white', 'bg' => 'bg-utn-blue-darker', 'ratio' => 9.09, 'sample_text' => 'Boton', 'sample_style' => 'text-white bg-utn-blue-darker px-2 py-0.5 rounded'],
                        ['combo' => 'Blanco sobre utn-dark', 'fg' => 'text-white', 'bg' => 'bg-utn-dark', 'ratio' => 17.40, 'sample_text' => 'Header', 'sample_style' => 'text-white bg-utn-dark px-2 py-0.5 rounded'],
                        ['combo' => 'utn-blue sobre blanco', 'fg' => 'text-utn-blue', 'bg' => 'bg-white', 'ratio' => 3.73, 'sample_text' => 'Acento', 'sample_style' => 'text-utn-blue'],
                        ['combo' => 'Blanco sobre green-700', 'fg' => 'text-white', 'bg' => 'bg-green-700', 'ratio' => 5.02, 'sample_text' => 'Guardar', 'sample_style' => 'text-white bg-green-700 px-2 py-0.5 rounded'],
                        ['combo' => 'Blanco sobre red-600', 'fg' => 'text-white', 'bg' => 'bg-red-600', 'ratio' => 4.83, 'sample_text' => 'Eliminar', 'sample_style' => 'text-white bg-red-600 px-2 py-0.5 rounded'],
                        ['combo' => 'gray-700 sobre blanco', 'fg' => 'text-gray-700', 'bg' => 'bg-white', 'ratio' => 8.59, 'sample_text' => 'Texto body', 'sample_style' => 'text-gray-700'],
                        ['combo' => 'gray-500 sobre blanco', 'fg' => 'text-gray-500', 'bg' => 'bg-white', 'ratio' => 4.83, 'sample_text' => 'Subtexto', 'sample_style' => 'text-gray-500'],
                    ];
                    @endphp
                    @foreach($contrastes as $c)
                    @php
                        $aa = $c['ratio'] >= 4.5;
                        $aaa = $c['ratio'] >= 7;
                        $large = $c['ratio'] >= 3;
                        $ui = $c['ratio'] >= 3;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-xs text-gray-700 font-medium">{{ $c['combo'] }}</td>
                        <td class="py-2 px-3">
                            <span class="text-sm font-bold {{ $c['sample_style'] }}">{{ $c['sample_text'] }}</span>
                        </td>
                        <td class="py-2 px-3 text-center font-mono text-xs font-bold {{ $aaa ? 'text-green-700' : ($aa ? 'text-utn-blue-dark' : ($large ? 'text-yellow-600' : 'text-red-600')) }}">{{ number_format($c['ratio'], 2) }}:1</td>
                        <td class="py-2 px-3 text-center">
                            @if($aa)<span class="text-green-600 font-bold text-xs">PASS</span>@else<span class="text-red-500 font-bold text-xs">FAIL</span>@endif
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($aaa)<span class="text-green-600 font-bold text-xs">PASS</span>@else<span class="text-gray-400 text-xs">-</span>@endif
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($large)<span class="text-green-600 font-bold text-xs">PASS</span>@else<span class="text-red-500 font-bold text-xs">FAIL</span>@endif
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($ui)<span class="text-green-600 font-bold text-xs">PASS</span>@else<span class="text-red-500 font-bold text-xs">FAIL</span>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECCION 4: PALETA CROMATICA DE CARRERAS --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-utn-dark mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Paleta Cromatica de las Carreras
        </h2>
        <p class="text-sm text-gray-600 mb-6">Ref: Manual de Identidad Visual UTN La Plata, pags. 07-08. Ratios sobre fondo blanco.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @php
            $carreras = [
                ['carrera' => 'Ing. Civil', 'hex' => '#408B4C', 'rgb' => '64, 139, 76', 'ratio' => 3.78, 'pantone' => '-'],
                ['carrera' => 'Ing. Electrica', 'hex' => '#BF3538', 'rgb' => '191, 53, 56', 'ratio' => 5.28, 'pantone' => '-'],
                ['carrera' => 'Ing. Industrial', 'hex' => '#EE8D35', 'rgb' => '238, 141, 53', 'ratio' => 2.30, 'pantone' => '-'],
                ['carrera' => 'Ing. Sistemas', 'hex' => '#1C6094', 'rgb' => '28, 96, 148', 'ratio' => 6.03, 'pantone' => '-'],
                ['carrera' => 'Ing. Mecanica', 'hex' => '#0B8C94', 'rgb' => '11, 140, 148', 'ratio' => 3.46, 'pantone' => '-'],
                ['carrera' => 'Ing. Quimica', 'hex' => '#FFC82A', 'rgb' => '255, 200, 42', 'ratio' => 1.63, 'pantone' => '-'],
                ['carrera' => 'Ciencias Basicas', 'hex' => '#BD9366', 'rgb' => '189, 147, 102', 'ratio' => 2.54, 'pantone' => '-'],
            ];
            @endphp
            @foreach($carreras as $c)
            @php
                $aa = $c['ratio'] >= 4.5;
                $large = $c['ratio'] >= 3;
            @endphp
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="h-16 flex items-center justify-center" style="background-color: {{ $c['hex'] }}">
                    <span class="text-white font-bold text-sm drop-shadow">{{ $c['carrera'] }}</span>
                </div>
                <div class="p-3">
                    <p class="font-mono text-xs font-bold text-gray-800">{{ $c['hex'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">RGB: {{ $c['rgb'] }}</p>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="font-mono text-xs font-bold {{ $aa ? 'text-green-700' : ($large ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($c['ratio'], 2) }}:1
                        </span>
                        <div class="flex gap-1">
                            @if($aa)
                                <span class="text-xs px-1.5 py-0.5 rounded bg-green-100 text-green-800 font-medium">AA</span>
                            @elseif($large)
                                <span class="text-xs px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-800 font-medium">Large</span>
                            @else
                                <span class="text-xs px-1.5 py-0.5 rounded bg-red-100 text-red-800 font-medium">Solo fondo</span>
                            @endif
                        </div>
                    </div>
                    {{-- Muestra de uso --}}
                    <div class="mt-2 flex gap-1">
                        <span class="text-xs font-bold" style="color: {{ $c['hex'] }}">Texto</span>
                        <span class="text-xs text-white px-1.5 rounded" style="background-color: {{ $c['hex'] }}">Badge</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-xs text-yellow-800">
                <strong>Nota de accesibilidad:</strong> Varias carreras tienen colores que no pasan WCAG AA para texto normal sobre blanco.
                Cuando se usen como texto, aplicar solo sobre fondos oscuros o como badges con texto blanco.
                Para texto sobre blanco usar variantes mas oscuras o el color institucional <code class="bg-yellow-100 px-1 rounded">utn-blue-dark</code>.
            </p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECCION 5: COMPONENTES DE EJEMPLO --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-utn-dark mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-utn-blue-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
            </svg>
            Componentes UI
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Botones --}}
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Botones</h3>
                <div class="space-y-3">
                    <div class="flex flex-wrap gap-2">
                        <button class="bg-utn-blue-darker hover:bg-utn-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition">Primario (9.09:1)</button>
                        <button class="bg-utn-dark hover:bg-utn-dark-light text-white px-4 py-2 rounded-lg text-sm font-medium transition">Oscuro (17.40:1)</button>
                        <button class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Guardar (5.02:1)</button>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Eliminar (4.83:1)</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button class="border border-utn-blue-dark text-utn-blue-dark hover:bg-utn-blue/10 px-4 py-2 rounded-lg text-sm font-medium transition">Outline (6.25:1)</button>
                        <button class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition">Secundario</button>
                    </div>
                </div>
            </div>

            {{-- Badges --}}
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Badges & Estados</h3>
                <div class="flex flex-wrap gap-2">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-utn-blue/10 text-utn-blue-dark">Activo</span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Aprobado</span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Pendiente</span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Rechazado</span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700">Inactivo</span>
                </div>

                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 mt-4">Alertas</h3>
                <div class="space-y-2">
                    <div class="bg-blue-50 border-l-4 border-utn-blue-dark p-2 rounded-r text-xs text-utn-blue-dark">Info: Texto informativo</div>
                    <div class="bg-green-50 border-l-4 border-green-600 p-2 rounded-r text-xs text-green-800">Exito: Operacion completada</div>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-2 rounded-r text-xs text-yellow-800">Advertencia: Revisar datos</div>
                    <div class="bg-red-50 border-l-4 border-red-500 p-2 rounded-r text-xs text-red-800">Error: Algo salio mal</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nota final --}}
    <div class="bg-blue-50 border-l-4 border-utn-blue-dark p-4 rounded-r-lg">
        <div class="flex">
            <svg class="h-5 w-5 text-utn-blue-dark flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3 text-sm text-utn-blue-dark">
                <strong>Nota:</strong> Esta pagina solo esta disponible en modo desarrollo (APP_DEBUG=true).
                Todos los ratios de contraste cumplen WCAG 2.1 AA como minimo para su uso previsto.
                Ref: <a href="https://accessibleweb.com/color-contrast-checker/" class="underline font-semibold" target="_blank">Color Contrast Checker</a>
            </div>
        </div>
    </div>
</div>
@endsection
