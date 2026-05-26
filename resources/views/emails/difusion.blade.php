<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            background: linear-gradient(to right, #003f87, #004ba8);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 4px 4px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 0 0 4px 4px;
        }
        .content h2 {
            color: #003f87;
            font-size: 18px;
            margin-top: 0;
        }
        .content p {
            margin: 10px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .sent-by {
            margin-top: 10px;
            font-style: italic;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $difusion->asunto }}</h1>
        </div>
        <div class="content">
            <h2>Comunicado Institucional</h2>
            <p>{{ $difusion->mensaje }}</p>
            
            @if($difusion->modalidad)
            <p style="background-color: #f0f0f0; padding: 10px; border-radius: 4px; margin-top: 20px;">
                @if(str_contains($difusion->modalidad, 'Comisión:'))
                    <strong>{{ $difusion->modalidad }}</strong>
                @else
                    <strong>Modalidad:</strong> {{ $difusion->modalidad }}
                @endif
            </p>
            @endif
            
            <div class="footer">
                <p>Este mensaje ha sido enviado por el Sistema PAICAT - UTN Facultad Regional La Plata</p>
                <div class="sent-by">
                    Enviado por: {{ $usuario->nombre_completo ?? 'Administrador' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
