<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se Recibió un documento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .separator {
            margin-top: 10px;
            border-bottom: 1px solid #ddd;
        }
        h3 {
            color: #08730c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Se recibió un documento:</h3>
        <p><strong>CUO:</strong> {{ $vcuo }}</p>
        <p><strong>Entidad Remitente:</strong> {{ $vrucentrem }} | {{ $vnomentemi }}</p>
        <p><strong>Unidad Orgánica de la Entidad Remitente:</strong> {{ $vuniorgrem }} | {{ $data->vnomentemi }}</p>
        <p><strong>Documento:</strong> {{ $ccodtipdocValue }}-{{ $vnumdoc }}</p>
        <p><strong>Fecha del Documento:</strong> {{ $dfecdoc }}</p>
        <p><strong>Asunto:</strong> {{ $vasu }}</p>
    </div>
</body>
</html>
