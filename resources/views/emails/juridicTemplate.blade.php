<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo registro de persona física</title>
</head>
<style>
    * {
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-family: Calibri, 'Trebuchet MS', sans-serif;
        font-size: 14px;
    }
    body {
        margin: 32px;
    }
    a {
        text-decoration: none;
        color: #000;
    }
    ul {
        list-style: none;
        list-style-type: none;
    }
    li {
        margin-left: 10px;
        list-style: none;
        list-style-type: none;
    }
    .footer {
        margin-top: 32px;
    }
    .footer-text {
        margin-top: 12px;
    }

</style>
<body>
    <div>
        {{-- Se acaba de registrar {{$contenido['name']}} con CUIT: {{$contenido['cuit']}}. --}}
        <p>
            Estimado cliente,
        </p>
        <br>
        <p>
            Hemos recibido su solicitud de apertura de cuenta comitente. A la brevedad se estará contactando con Usted un representante del sector Apertura de Cuentas para continuar con el proceso.    
        </p>
        <br>
        <p>
            Por favor, le solicitamos tenga a bien responder este correo electrónico con la siguiente documentación:
        </p>
        <br>
        <p>
            <strong>De la empresa:</strong>
        </p>
        <div>
            <ul>
                <li>✔ Estatuto y todas sus modificaciones </li>
                <li>✔ Último balance con copia de la Oblea del Consejo Profesional de Cs. Económicas</li>
                <li>✔ Acta firmada con aprobación del último balance.</li>
                <li>✔ Última Acta firmada con designación de Autoridades con copia de la Rúbrica del Libro de Actas.</li>
                <li>✔ Registro de accionistas junto con la copia de la Rúbrica del Libro. De la documentación fehaciente solicitada en el presente ítem deben surgir los beneficiarios finales de la Persona Jurídica</li>
                <li>✔ Constancia de CBU</li>
                <li>✔ En caso que corresponda: Constancia de inscripción como Sujeto Obligado ante la Unidad de Información Financiera </li>
            </ul>
        </div>
        <br>
        <p>
            <strong>De los Apoderados y/o Autorizados:</strong>
        </p>
        <div>
            <ul>
                <li>✔ Copia del DNI</li>
                <li>✔ En caso que corresponda: Copia de los poderes</li>
            </ul>
        </div>
        <br>
        <p>
            Cualquier consulta quedamos a disposición.
        </p>
        <p>
            Saludos,
        </p>

        <div class="footer">
            @if(isset($message))
                <div class="footer-logo">
                    {{-- <img src="/logo.jpeg" alt="logo"> --}}
                    <img src="{{$message->embed(public_path().'/logo.jpeg')}}" alt="logo">
                </div>
            @endif
            <div class="footer-text">
                <strong>ARG Securities Advisors S.A.</strong> <br>
                Agente de Negociación. Reg CNV N°719 <br>
                Juan Carlos Cruz 120, Núcleo 4, Piso 2, Oficina 215 <br>
                (1638) Vicente López, Buenos Aires, Argentina <br>
                <a href="www.argsecurities.com">www.argsecurities.com</a> <br>
            </div>
        </div>

        
    </div>
</body>
</html>