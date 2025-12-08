<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Informe de Formulario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            /* Dejamos un poco más de margen interno para que DomPDF no recorte bordes */
            margin: 15px 20px;
        }
        table {
            /* Un poco menos de 100% para evitar que se pegue al borde de la hoja */
            width: 98%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px 4px;
        }
        .no-border th,
        .no-border td {
            border: none;
        }
        .logo-row td {
            border: none;
            text-align: center;
            font-size: 9px;
        }
        .title-bar {
            background-color: #800000;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
        .instruction-bar {
            background-color: #f2f2f2;
            font-size: 8px;
        }
        .section-label {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .activities-label {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .control-label {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .small-text {
            font-size: 8px;
        }
        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .signature-row td {
            border-top: 1px solid #000;
            border-bottom: none;
            border-left: none;
            border-right: none;
            text-align: center;
            font-size: 9px;
            padding-top: 15px;
        }
        .signature-label {
            font-size: 8px;
        }
        .border-box {
            border: 1px solid #000;
            padding: 4px;
            font-size: 9px;
        }
        .checkbox {
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            display: inline-block;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- Encabezado con logos reales --}}
    <table class="no-border logo-row">
        <tr>
            <td>
                <img src="{{ public_path('assets/gob_logo.png') }}" alt="Gobierno de Sinaloa" height="40">
            </td>
            <td>
                <img src="{{ public_path('assets/dte_logo.png') }}" alt="DTE" height="40">
            </td>
            <td>
                <img src="{{ public_path('assets/sepyc_logo.png') }}" alt="SEPyC" height="40">
            </td>
        </tr>
    </table>

    {{-- Barra de título roja --}}
    <table>
        <tr>
            <td class="title-bar">
                PRIMER INFORME TRIMESTRAL DEL USO Y APROVECHAMIENTO DE LOS RECURSOS TECNOLÓGICOS
            </td>
        </tr>
        <tr>
            <td class="instruction-bar small-text">
                INSTRUCCIONES: 1) LLENAR, IMPRIMIR, FIRMAR AL CALCE Y DIGITALIZAR &nbsp;&nbsp; 2) SUBIR EN: http://tecnologiayeducativa.sepyc.gob.mx/
            </td>
        </tr>
    </table>

    {{-- Datos del centro de trabajo (encabezado principal) --}}
    <table>
        <tr>
            <th style="width: 35%">NOMBRE DEL CENTRO DE TRABAJO</th>
            <td style="width: 35%">{{ $profile->work_center_name ?? 'N/A' }}</td>
            <th style="width: 15%">CCT</th>
            <td style="width: 15%">{{ $profile->cct ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>NOMBRE DEL RTE QUE REPORTA</th>
            <td>{{ $profile->rte_name ?? ($formulario->usuario->name ?? 'N/A') }}</td>
            <th>ZONA</th>
            <td>{{ $profile->zone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>TURNO</th>
            <td>{{ $profile->shift ?? 'N/A' }}</td>
            <th>SECTOR</th>
            <td>{{ $profile->sector ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>PERIODO A REPORTAR</th>
            <td>{{ $profile->report_period ?? 'N/A' }}</td>
            <th>HORAS COMISIONADAS AL AULA DE MEDIOS</th>
            <td>{{ $profile->commissioned_hours ?? '0' }}</td>
        </tr>
    </table>

    {{-- DESGLOSE DE ACTIVIDADES / CONTROL --}}
    <table>
        <tr>
            <td class="activities-label" colspan="8">DESGLOSE DE ACTIVIDADES</td>
        </tr>
        <tr>
            <td class="control-label" colspan="8">CONTROL - Cantidad de alumnos atendidos en el aula de medios</td>
        </tr>
        {{-- Encabezado como en el formato oficial --}}
        <tr>
            <th rowspan="2" style="width: 26%">Contenidos y/o PDA, de cada campo formativo abordados.</th>
            <th colspan="6" style="width: 38%">Cantidad de alumnos por grado</th>
            <th rowspan="2" style="width: 36%">
                Participan en proyectos colaborativos / Especifique cuáles:<br/>
                <span class="small-text">
                    SI&nbsp;[{{ $formulario->participa_en_proyectos ? 'X' : ' ' }}]&nbsp;&nbsp;&nbsp;&nbsp;NO&nbsp;[{{ !$formulario->participa_en_proyectos ? 'X' : ' ' }}]
                </span>
            </th>
        </tr>
        <tr>
            <th>1º</th>
            <th>2º</th>
            <th>3º</th>
            <th>4º</th>
            <th>5º</th>
            <th>6º</th>
        </tr>

        {{-- Mostrar campos formativos --}}
        @foreach($formulario->camposFormativos as $campo)
            <tr>
                {{-- Contenidos / PDA --}}
                <td>{{ $campo->nombre  }}</td>
                {{-- Cantidad de alumnos por grado 1º a 6º --}}
                 @for($grado = 1; $grado <= 6; $grado++)
                    <td class="center">{{ $campo->alumnos_por_grado[$grado] ?? '0' }}</td>
                @endfor
                {{-- Participan / Especifique cuáles --}}
                <td>
                    @if($formulario->participa_en_proyectos && $formulario->proyectosColaborativos->isNotEmpty())
                        @foreach($formulario->proyectosColaborativos as $proyecto)
                            {{ $proyecto->nombre  }}
                            @if(!$loop->last), @endif
                        @endforeach
                    @else
                        &nbsp;
                    @endif
                </td>
            </tr>
        @endforeach
        
        {{-- Rellenar con filas vacías si hay menos de 12 campos --}}
        @for($i = count($formulario->camposFormativos); $i < 12; $i++)
            <tr>
                <td>&nbsp;</td>
                <td class="center">&nbsp;</td>
                <td class="center">&nbsp;</td>
                <td class="center">&nbsp;</td>
                <td class="center">&nbsp;</td>
                <td class="center">&nbsp;</td>
                <td class="center">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        @endfor
    </table>

    <br/>

    {{-- Segunda sección: cursos en línea ocupando todo el ancho --}}
    <table class="no-border" style="margin-top: 15px;">
        <tr>
            <td>
                <div class="border-box">
                    <table class="no-border small-text" style="width: 100%">
                        <tr>
                            <td colspan="4"><strong>¿Se inscribió en algún curso en línea?</strong></td>
                        </tr>
                        <tr>
                            <td style="width: 8%">SI</td>
                            <td style="width: 8%"><span class="checkbox">@if($formulario->se_inscribio_en_cursos) X @endif</span></td>
                            <td rowspan="3" class="small-text" style="width: 54%">
                                Anote el nombre del (los) curso(s):<br/>
                                @if(isset($formulario->cursosEnLinea) && $formulario->cursosEnLinea->count())
                                    @foreach($formulario->cursosEnLinea as $curso)
                                        - {{ $curso->nombre ?? 'Curso' }}<br/>
                                    @endforeach
                                @else
                                    <br/><br/><br/>
                                @endif
                            </td>
                            <td rowspan="3" style="width: 30%; vertical-align: top;">
                                {{-- Columna libre por si se requiere texto adicional más adelante --}}
                            </td>
                        </tr>
                        <tr>
                            <td>NO</td>
                            <td><span class="checkbox">@if(!$formulario->se_inscribio_en_cursos) X @endif</span></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 8px;">
                                ¿En cuántos? &nbsp;
                                <span class="border-box" style="display: inline-block; min-width: 25px; text-align: center;">
                                    {{ $formulario->cantidad_cursos ?? '' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <br/><br/><br/>

    {{-- Firmas --}}
    <table class="no-border" style="width: 100%; margin-top: 40px;">
        <tr class="signature-row">
            <td style="width: 33%"></td>
            <td style="width: 33%"></td>
            <td style="width: 33%"></td>
        </tr>
        <tr class="no-border">
            <td class="center signature-label">Nombre y firma del Director de la Escuela</td>
            <td class="center signature-label">Sello de la Escuela</td>
            <td class="center signature-label">Nombre y firma del RTE</td>
        </tr>
    </table>
</body>
</html>
