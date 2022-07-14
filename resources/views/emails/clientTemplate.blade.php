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
        <div>
            <ul>
                <li>- DNI (copia de frente y dorso)</li> <br>
                <li>- Constancia de CBU de la/las cuentas bancarias desde donde enviará o recibirá dinero (debe coincidir con la/las indicadas en el formulario).</li> <br>
                <li>- Documentación que justifique los fondos (Por ejemplo: 3 últimos Recibos de Sueldo ó 3 últimos pagos de monotributo o autónomos y sus respectivas DDJJ de IVA ó Facturas emitidas ó DDJJ de Ganancias + Ticket de Presentación ó DDJJ Bienes Personales + Ticket de Presentación ó Certificación Contable de Ingresos o Fondos con su respectiva Oblea del Consejo Profesional de Ciencias Económicas).</li> <br>
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