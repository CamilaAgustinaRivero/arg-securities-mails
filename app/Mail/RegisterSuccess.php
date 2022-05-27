<?php

namespace App\Mail;

use FisicaPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use stdClass;

class RegisterSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "Se ha registrado un nuevo cliente";
    public $contenido;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contenido)
    {
        $this->contenido = $contenido;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfParserException
     * @throws PdfReaderException
     */
    public function build(): RegisterSuccess
    {
        if($this->contenido['isPersonaJuridica']){
            $pdf = $this->storeJuridica($this->contenido);
            $pdfdoc = $pdf->Output("registro-juridica.pdf", "S");
            return $this->view('emails.register', [
                'contenido' => $this->contenido,
            ])->attachData($pdfdoc, 'registro-juridica.pdf');
        } else {
            $pdf = $this->store($this->contenido);
            $pdfdoc = $pdf->Output("registro.pdf", "S");
            return $this->view('emails.register', [
                'contenido' => $this->contenido,
            ])->attachData($pdfdoc, 'registro.pdf');
        }
    }

    private function storeJuridica($info): Fpdi {
        $pdf = new Fpdi();
        $pdf->setSourceFile(base_path() . '/public/PDFs/PJ-Plantilla.pdf');
        $pdf->SetFont('Arial', '', '8');

        # Página 1
        $pdf->AddPage();
        $pdf_1 = $pdf->importPage(1);
        $pdf->useTemplate($pdf_1);
        # REGISTRO DE COMITENTES #
        # Denominación
        $pdf->SetXY(10, 33);
        $pdf->Write(30, $info["titular"]["datosPrincipalesIdeal"]["denominacion"]);
        # Domicilio
        $pdf->SetXY(10, 43.5);
        $pdf->Write(30, $info["titular"]["domicilioUrbano"][0]["calle"].' '.$info["titular"]["domicilioUrbano"][0]["numero"]);
        # Celular
        $pdf->SetXY(65, 52.5);
        $pdf->Write(30, $info["titular"]["mediocomunicacion"][1]["medio"]);
        # E-mail
        $pdf->SetXY(119, 52.5);
        $pdf->Write(30, $info["titular"]["mediocomunicacion"][0]["medio"]);
        # CP
        $pdf->SetXY(172, 52.5);
        $pdf->Write(30, $info["titular"]["domicilioUrbano"][0]["codigoPostal"]);
        # Fecha de constitución
        $pdf->SetXY(10, 62);
        $pdf->Write(30, $info["titular"]["datosOrganizacion"]["fechaConstitucion"]);
        # Acta de constitución
        $pdf->SetXY(49, 62);
        $pdf->Write(30, $info["titular"]["datosOrganizacion"]["actaConstitucion"]);
        # Tipo inscripción
        $pdf->SetXY(86.5, 62);
        $pdf->Write(30, $info["titular"]["registro"][0]["tipo"].' '. $info["titular"]["registro"][0]["numero"]);
        # Lugar
        $pdf->SetXY(134, 62);
        $pdf->Write(30, $info["titular"]["registro"][0]["lugar"]);
        # Folio
        $pdf->SetXY(10, 71.5);
        $pdf->Write(30, $info["titular"]["registro"][0]["folio"]);
        # Libro
        $pdf->SetXY(36, 71.5);
        $pdf->Write(30, $info["titular"]["registro"][0]["libro"]);
        # Tomo
        $pdf->SetXY(64, 71.5);
        $pdf->Write(30, $info["titular"]["registro"][0]["tomo"]);
        # CUIT
        $pdf->SetXY(149, 71.5);
        $pdf->Write(30, $info["titular"]["datosPrincipalesIdeal"]["id"]);
        # ACTIVIDAD DE LA ORGANIZACIÓN #
        # Actividad principal
        $pdf->SetXY(10, 98);
        $pdf->Write(30, $info["titular"]["actividadOrganizacion"][0]["actividad"]);
        # PATRIMONIO Y BALANCE #
        # Activos
        $pdf->SetXY(12, 117);
        $pdf->Write(30, $info["titular"]["patrimonioYBlanace"]["activos"]);
        # Pasivos
        $pdf->SetXY(101.5, 117);
        $pdf->Write(30, $info["titular"]["patrimonioYBlanace"]["pasivos"]);
        # Patrimonio
        $pdf->SetXY(12, 128);
        $pdf->Write(30, $info["titular"]["patrimonioYBlanace"]["activos"]);
        # Destinado a inversiones
        $pdf->SetXY(101.5, 128);
        $pdf->Write(30, $info["titular"]["patrimonioYBlanace"]["pasivos"]);
        # Egresos
        $pdf->SetXY(102, 138.5);
        $pdf->Write(30, $info["titular"]["patrimonioYBlanace"]["egresos"]);
        
        # Página 2
        $pdf->AddPage();
        $pdf_2 = $pdf->importPage(2);
        $pdf->useTemplate($pdf_2);
        # Apellido y nombre
        // if personaRelacionada count > 0
        if(count($info["personaRelacionada"]) > 0){
            $pdf->SetXY(10, 35);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["apellido"].' '.$info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["nombre"]);
            # Tipo de relación
            $pdf->SetXY(10, 44);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["tipo"]);
            # Tipo de documento
            $pdf->SetXY(72, 44);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["tipoID"]);
            # Numero
            $pdf->SetXY(110.5, 44);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["id"]);
            # Sexo
            $pdf->SetXY(167, 44);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["datosPersonales"]["sexo"]);
        }
        # <----------------> #
        $domicilio = $this->obtener_domicilio($info["personaRelacionada"][0]["persona"]["domicilioUrbano"]);
        # Domicilio legal
        $pdf->SetXY(10, 63);
        $pdf->Write(30, $domicilio->legal);
        # CP Legal
        $pdf->SetXY(167, 63);
        $pdf->Write(30, $domicilio->cp_legal);
        # Domicilio Real
        $pdf->SetXY(10, 73.5);
        $pdf->Write(30, $domicilio->real);
        # CP Real
        $pdf->SetXY(167, 73.5);
        $pdf->Write(30, $domicilio->cp_real);
        # Domicio por Correspondencia
        $pdf->SetXY(10, 83.5);
        $pdf->Write(30, $domicilio->correspondencia);
        # CP por Correspondencia
        $pdf->SetXY(167, 83.5);
        $pdf->Write(30, $domicilio->cp_correspondencia);
        # <----------------> #
        
        for ($i=3; $i <= 14; $i++) { 
            $pdf->AddPage();
            $pdf_2 = $pdf->importPage($i);
            $pdf->useTemplate($pdf_2);
        }

        return $pdf;
    }

    private function obtener_domicilio($domicilios): stdClass
    {
        $legal = '';
        $cp_legal = '';
        $real = '';
        $cp_real = '';
        $correspondencia = '';
        $cp_correspondencia = ' ';
        foreach($domicilios as $domicilio) {
            if ($domicilio["uso"] === "Legal") {
                $legal = $domicilio["calle"].' '.$domicilio["numero"];
                $cp_legal = $domicilio["codigoPostal"];
            } elseif ($domicilio["uso"] === "Real") {
                $real = $domicilio["calle"].' '.$domicilio["numero"];
                $cp_real = $domicilio["codigoPostal"];
            } elseif ($domicilio["uso"] === "Correspondencia") {
                $correspondencia = $domicilio["calle"].' '.$domicilio["numero"];
                $cp_correspondencia = $domicilio["codigoPostal"];
            }
        }
        # Crea un el objeto 'domicilio' sin clase
        $domicilio = new stdClass();
        $domicilio->legal = $legal;
        $domicilio->cp_legal = $cp_legal;
        $domicilio->real = $real;
        $domicilio->cp_real = $cp_real;
        $domicilio->correspondencia = $correspondencia;
        $domicilio->cp_correspondencia = $cp_correspondencia;
        return $domicilio;
    }

    /**
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     */
    public function store($info): Fpdi
    {
        $pdf = new Fpdi();
        $pdf->setSourceFile(base_path() . '/public/PDFs/PF-Plantilla.pdf');
        $pdf->SetFont('Arial', '', '8');

        # Página 1
        $pdf->AddPage();
        $pdf_1 = $pdf->importPage(1);
        $pdf->useTemplate($pdf_1);
        # Denominación
        $pdf->SetXY(18, 46);
        $pdf->Write(30, $info["titular"]["cuentaBancaria"][0]["denominacion"]);
        # Domicilio
        $pdf->SetXY(18, 57.5);
        $pdf->Write(30, $info["titular"]["domicilioUrbano"][0]["calle"].' '.$info["titular"]["domicilioUrbano"][0]["numero"]);
        # Celular
        $pdf->SetXY(82, 69);
        $pdf->Write(30, $info["titular"]["mediocomunicacion"][1]["medio"]);
        # Apellido y nombre - PRIMER TITULAR
        $pdf->SetXY(18, 77);
        $pdf->Write(50, $info["titular"]["datosPrincipalesFisicas"]["apellido"].' '.$info["titular"]["datosPrincipalesFisicas"]["nombre"]);
        # Tipo de documento
        $pdf->SetXY(75, 98.5);
        $pdf->Write(30, $info["titular"]["datosPrincipalesFisicas"]["tipoID"]);
        # Número
        $pdf->SetXY(110, 98.5);
        $pdf->Write(30, $info["titular"]["datosPrincipalesFisicas"]["id"]);
        # Número
        $pdf->SetXY(162, 98.5);
        $pdf->Write(30, $info["titular"]["datosPersonales"]["sexo"]);
        # <----------------> #
        $domicilio = $this->obtener_domicilio($info["titular"]["domicilioUrbano"]);
        # Domicilio legal
        $pdf->SetXY(18, 110.5);
        $pdf->Write(30, $domicilio->legal);
        # CP Legal
        $pdf->SetXY(162, 110.5);
        $pdf->Write(30, $domicilio->cp_legal);
        # Domicilio Real
        $pdf->SetXY(18, 123);
        $pdf->Write(30, $domicilio->real);
        # CP Real
        $pdf->SetXY(162, 123);
        $pdf->Write(30, $domicilio->cp_real);
        # Domicio por Correspondencia
        $pdf->SetXY(18, 135);
        $pdf->Write(30, $domicilio->correspondencia);
        # CP por Correspondencia
        $pdf->SetXY(162, 135);
        $pdf->Write(30, $domicilio->cp_correspondencia);
        # <----------------> #
        # Celular
        $pdf->SetXY(75, 147.5);
        $pdf->Write(30, $info["titular"]["mediocomunicacion"][1]["medio"]);
        # E-mail
        $pdf->SetXY(136, 147.5);
        $pdf->Write(30, $info["titular"]["mediocomunicacion"][0]["medio"]);
        # Estado civil
        $pdf->SetXY(18, 160);
        $pdf->Write(30, $info["titular"]["datosPersonales"]["estadoCivil"]);
        # Cónyuge
        $pdf->SetXY(75, 160);
        $info["titular"]["datosConyuge"] ? $pdf->Write(30, $info["titular"]["datosConyuge"][0]["apellido"].' '.$info["titular"]["datosConyuge"][0]["nombre"]) : ' ';
        # Documento cónyuge
        $pdf->SetXY(18, 172);
        $info["titular"]["datosConyuge"] ? $pdf->Write(30, $info["titular"]["datosConyuge"][0]["tipoID"]) : ' ';
        # CUIT O CUIL cónyuge
        $pdf->SetXY(75, 172);
        $info["titular"]["datosConyuge"] ? $pdf->Write(30, $info["titular"]["datosConyuge"][0]["id"]) : ' ';
        # Fecha de nacimiento
        $pdf->SetXY(18, 185.5);
        $pdf->Write(30, $info["titular"]["datosPersonales"]["fechaNacimiento"]);
        # Nacionalidad
        $pdf->SetXY(75, 185.5);
        $pdf->Write(30, $info["titular"]["datosPersonales"]["nacionalidad"]);
        # Residencia
        $pdf->SetXY(136, 185.5);
        $pdf->Write(30, $info["titular"]["datosPersonales"]["paisResidencia"]);
        # Actividad
        $pdf->SetXY(18, 198);
        $pdf->Write(30, $info["titular"]["datosPersonales"]["actividad"]);
        # CUIT O CUIL
        $pdf->SetXY(136, 198);
        $pdf->Write(30, $info["titular"]["datosFiscales"]["cuit"]);
        # Impuesto IVA
        $pdf->SetXY(18, 211);
        $pdf->Write(30, $info["titular"]["datosFiscales"]["tipoResponsableIVA"]);
        # Impuesto ganancias
        $pdf->SetXY(75, 211);
        $pdf->Write(30, $info["titular"]["datosFiscales"]["tipoResponsableGanancias"]);

        # Página 2
        $pdf->AddPage();
        $pdf_2 = $pdf->importPage(2);
        $pdf->useTemplate($pdf_2);
        if ($info["personaRelacionada"] && $info["personaRelacionada"][0]) {
            # Apellido y nombre - SEGUNDO TITULAR
            $pdf->SetXY(18, 29);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["apellido"].' '.$info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["nombre"]);
            # Tipo de relación
            $pdf->SetXY(18, 40.5);
            $pdf->Write(50, $info["personaRelacionada"][0]["tipo"]);
            # Tipo de documento
            $pdf->SetXY(75, 40.5);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["tipoID"]);
            # Número
            $pdf->SetXY(110, 40.5);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["id"]);
            # <----------------> #
            $domicilio = $this->obtener_domicilio($info["personaRelacionada"][0]["persona"]["domicilioUrbano"]);
            # Domicilio legal
            $pdf->SetXY(18, 62.5);
            $pdf->Write(30, $domicilio->legal);
            # CP Legal
            $pdf->SetXY(162, 62.5);
            $pdf->Write(30, $domicilio->cp_legal);
            # Domicilio Real
            $pdf->SetXY(18, 74.5);
            $pdf->Write(30, $domicilio->real);
            # CP Real
            $pdf->SetXY(162, 74.5);
            $pdf->Write(30, $domicilio->cp_real);
            # Domicio por Correspondencia
            $pdf->SetXY(18, 87);
            $pdf->Write(30, $domicilio->correspondencia);
            # CP por Correspondencia
            $pdf->SetXY(162, 87);
            $pdf->Write(30, $domicilio->cp_correspondencia);
            # <----------------> #
            # Celular
            $pdf->SetXY(75, 99);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["mediocomunicacion"][1]["medio"]);
            # E-mail
            $pdf->SetXY(136, 99);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["mediocomunicacion"][0]["medio"]);
            # Estado civil
            $pdf->SetXY(18, 111.5);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosPersonales"]["estadoCivil"]);
            # Cónyuge
            $pdf->SetXY(75, 111.5);
            $info["personaRelacionada"][0]["persona"]["datosConyuge"] ? $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosConyuge"][0]["apellido"].' '.$info["personaRelacionada"][0]["persona"]["datosConyuge"][0]["nombre"]) : ' ';
            # Documento cónyuge
            $pdf->SetXY(18, 124);
            $info["personaRelacionada"][0]["persona"]["datosConyuge"] ? $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosConyuge"][0]["tipoID"]) : ' ';
            # CUIT O CUIL cónyuge
            $pdf->SetXY(75, 124);
            $info["personaRelacionada"][0]["persona"]["datosConyuge"] ? $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosConyuge"][0]["id"]) : ' ';
            # Fecha de nacimiento
            $pdf->SetXY(18, 137.5);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosPersonales"]["fechaNacimiento"]);
            # Nacionalidad
            $pdf->SetXY(75, 137.5);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosPersonales"]["nacionalidad"]);
            # Residencia
            $pdf->SetXY(136, 137.5);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosPersonales"]["paisResidencia"]);
            # Actividad
            $pdf->SetXY(18, 150);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosPersonales"]["actividad"]);
            # CUIT O CUIL
            $pdf->SetXY(136, 150);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosFiscales"]["cuit"]);
            # Impuesto IVA
            $pdf->SetXY(18, 163);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosFiscales"]["tipoResponsableIVA"]);
            # Impuesto ganancias
            $pdf->SetXY(75, 163);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosFiscales"]["tipoResponsableGanancias"]);
        }

        # Página 3
        $pdf->AddPage();
        $pdf_3 = $pdf->importPage(3);
        $pdf->useTemplate($pdf_3);
        $pdf->SetFont('Arial', '', '7');
        # El que suscribe
        $pdf->SetXY(45, 42);
        $pdf->Write(50, $info["titular"]["datosPrincipalesFisicas"]["apellido"].' '.$info["titular"]["datosPrincipalesFisicas"]["nombre"]);
        # Si / No
        if ($info["titular"]["declaracionesPF"]["expuestaPoliticamente"]) {
            $pdf->SetXY(73, 45);
            $pdf->Write(50, "x");
            # Observaciones
            $pdf->SetXY(36, 51);
            $info["titular"]["declaracionesPF"]["observacionesFATCA"] ? $pdf->Write(50, $info["titular"]["declaracionesPF"]["observacionesFATCA"]) : ' ';
            # Documento
            $pdf->SetXY(44, 76);
            $pdf->Write(30, $info["titular"]["datosPrincipalesFisicas"]["tipoID"]);
            # Número
            $pdf->SetXY(70, 76);
            $pdf->Write(30, $info["titular"]["datosPrincipalesFisicas"]["id"]);
            # Pais
            $pdf->SetXY(116, 75.5);
            $pdf->Write(30, $info["titular"]["datosPersonales"]["paisResidencia"]);
            # CUIT O CUIL
            $pdf->SetXY(45, 79);
            $pdf->Write(30, $info["titular"]["datosFiscales"]["cuit"]);
        } else {
            $pdf->SetXY(79, 45);
            $pdf->Write(50, "x");
        }
        # <----------------> #
        # El que suscribe
        if ($info["personaRelacionada"] && $info["personaRelacionada"][0] && $info["personaRelacionada"][0]["persona"]["declaracionesPF"]["expuestaPoliticamente"]) {
            $pdf->SetXY(45, 167);
            $pdf->Write(50, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["apellido"].' '.$info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["nombre"]);
        # Si / No
            $pdf->SetXY(73, 170);
            $pdf->Write(50, "x");
            # Observaciones
            $pdf->SetXY(36, 176);
            $info["personaRelacionada"][0]["persona"]["declaracionesPF"]["observacionesFATCA"] ? $pdf->Write(50, $info["personaRelacionada"]["declaracionesPF"]["observacionesFATCA"]) : ' ';
            # Documento
            $pdf->SetXY(44, 201);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["tipoID"]);
            # Número
            $pdf->SetXY(70, 201);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosPrincipalesFisicas"]["id"]);
            # Pais
            $pdf->SetXY(116, 200.5);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosPersonales"]["paisResidencia"]);
            # CUIT O CUIL
            $pdf->SetXY(45, 204);
            $pdf->Write(30, $info["personaRelacionada"][0]["persona"]["datosFiscales"]["cuit"]);
        } else {
            $pdf->SetXY(79, 170);
            $pdf->Write(50, "x");
        }

        # Página 4
        $pdf->AddPage();
        $pdf_2 = $pdf->importPage(4);
        $pdf->useTemplate($pdf_2);
        $pdf->SetFont('Arial', '', '7');
        # <----------------> #
        # Nombre del sujeto obligado
        $pdf->SetXY(108, 65);
        $pdf->Write(50, $info["titular"]["datosPrincipalesFisicas"]["apellido"].' '.$info["titular"]["datosPrincipalesFisicas"]["nombre"]);
        # Es sujeto obligado?
        if ($info["titular"]["declaracionesPF"]["personaEEUU"]) {
            # Check SI ES SUJETO OBLIGADO
            $pdf->SetXY(20.1, 84.5);
            $pdf->Write(30, 'x');
            # Apellido y nombre
            $pdf->SetXY(42, 125);
            $pdf->Write(30, $info["titular"]["datosPrincipalesFisicas"]["apellido"].' '.$info["titular"]["datosPrincipalesFisicas"]["nombre"]);
            # CUIL
            $pdf->SetXY(44, 129);
            $pdf->Write(30, $info["titular"]["datosFiscales"]["cuit"]);
            # Documento
            $pdf->SetXY(33, 132);
            $pdf->Write(30, $info["titular"]["datosPrincipalesFisicas"]["tipoID"]);
            # Número
            $pdf->SetXY(55, 132);
            $pdf->Write(30, $info["titular"]["datosPrincipalesFisicas"]["id"]);
            # Numero de inscripción
            $pdf->SetXY(68.7, 137.5);
            $pdf->Write(30, $info["titular"]["declaracionesPF"]["numeroInscripcion"]);
        } else {
            # Check NO ES SUJETO OBLIGADO
            $pdf->SetXY(26, 84.5);
            $pdf->Write(30, 'x');
        }

        # Página 5
        $pdf->AddPage();
        $pdf_2 = $pdf->importPage(5);
        $pdf->useTemplate($pdf_2);
        $pdf->SetFont('Arial', '', '7');
        # <----------------> #
        # Nombre del sujeto obligado
        if($info["personaRelacionada"] && $info["personaRelacionada"][0] && $info["personaRelacionada"][0]["persona"]){
            $pdf->SetXY(108, 60.0);
            $pdf->Write(50, $info["personaRelacionada"][0]['persona']["datosPrincipalesFisicas"]["apellido"].' '.$info["personaRelacionada"][0]['persona']["datosPrincipalesFisicas"]["nombre"]);
            # Es sujeto obligado?
            if($info["personaRelacionada"][0]['persona']["declaracionesPF"]["personaEEUU"]) {
                # Check SI ES SUJETO OBLIGADO
                $pdf->SetXY(20.5, 82);
                $pdf->Write(30, 'x');
                # Apellido y nombre
                $pdf->SetXY(42, 123);
                $pdf->Write(30, $info["personaRelacionada"][0]['persona']["datosPrincipalesFisicas"]["apellido"].' '.$info["personaRelacionada"][0]['persona']["datosPrincipalesFisicas"]["nombre"]);
                # CUIL
                $pdf->SetXY(44, 126.5);
                $pdf->Write(30, $info["personaRelacionada"][0]['persona']["datosFiscales"]["cuit"]);
                # Documento
                $pdf->SetXY(33, 129.5);
                $pdf->Write(30, $info["personaRelacionada"][0]['persona']["datosPrincipalesFisicas"]["tipoID"]);
                # Número
                $pdf->SetXY(55, 129.5);
                $pdf->Write(30, $info["personaRelacionada"][0]['persona']["datosPrincipalesFisicas"]["id"]);
                # Numero de inscripción
                $pdf->SetXY(68.7, 135.5);
                $pdf->Write(30, $info["personaRelacionada"][0]['persona']["declaracionesPF"]["numeroInscripcion"]);
            } else {
                # Check NO ES SUJETO OBLIGADO
                $pdf->SetXY(26, 82);
                $pdf->Write(30, 'x');
            }
        }

        # Página 6
        $pdf->AddPage();
        $pdf_6 = $pdf->importPage(6);
        $pdf->useTemplate($pdf_6);
        $pdf->SetFont('Arial', '', '8');
        # Apellido y nombre
        $pdf->SetXY(18, 38);
        $pdf->Write(50, $info["titular"]["datosPrincipalesFisicas"]["apellido"].' '.$info["titular"]["datosPrincipalesFisicas"]["nombre"]);
        # Tipo y actividad principal
        $pdf->SetXY(18, 46);
        $pdf->Write(50, $info["titular"]["actividadPersona"][0]["actividad"]);
        # Rubro
        $pdf->SetXY(18, 54);
        $pdf->Write(50, $info["titular"]["actividadPersona"][0]["rubro"]);
        # Procedencia de ingresos
        $pdf->SetXY(18, 70);
        $pdf->Write(50, $info["titular"]["infoPatrimonial"][0]["procedenciaFondos"][0]);
        # Ingreso anual
        $pdf->SetXY(18, 78);
        $pdf->Write(50, $info["titular"]["infoPatrimonial"][0]["ingresos"]);
        # Pratrimonio neto
        $pdf->SetXY(110, 78);
        $pdf->Write(50, $info["titular"]["infoPatrimonial"][0]["patrimonio"]);

        # Páginas 7 a 13
        for ($i=7; $i <= 13 ; $i++) {
            $pdf->AddPage();
            $pdf_2 = $pdf->importPage($i);
            $pdf->useTemplate($pdf_2);
        }

        # Página 14
        $pdf->AddPage();
        $pdf_2 = $pdf->importPage(14);
        $pdf->useTemplate($pdf_2);

        # Denominacion
        $pdf->SetXY(103, 82);
        $pdf->Write(30, $info["titular"]["cuentaBancaria"][0]["denominacion"]);
        # Tipo de cuenta
        $pdf->SetXY(103, 85.5);
        $pdf->Write(30, $info["titular"]["cuentaBancaria"][0]["tipo"]);
        # Numero de cuenta
        $pdf->SetXY(103, 89);
        $pdf->Write(30, $info["titular"]["cuentaBancaria"][0]["numero"]);
        # Titular
        $pdf->SetXY(103, 82.5);
        $pdf->Write(50, $info["titular"]["datosPrincipalesFisicas"]["apellido"].' '.$info["titular"]["datosPrincipalesFisicas"]["nombre"]);
        # CUIT
        $pdf->SetXY(103, 96);
        $pdf->Write(30, $info["titular"]["datosFiscales"]["cuit"]);
        # CBU
        $pdf->SetXY(103, 100);
        $pdf->Write(30, $info["titular"]["cuentaBancaria"][0]["cbu"]);
        # Alias
        $pdf->SetXY(103, 104);
        $pdf->Write(30, $info["titular"]["cuentaBancaria"][0]["alias"]);

        if(isset($info["titular"]["cuentaBancaria"][1])){
            # Denominacion
            $pdf->SetXY(103, 112);
            $pdf->Write(30, $info["titular"]["cuentaBancaria"][1]["denominacion"]);
            # Tipo de cuenta
            $pdf->SetXY(103, 116);
            $pdf->Write(30, $info["titular"]["cuentaBancaria"][1]["tipo"]);
            # Numero de cuenta
            $pdf->SetXY(103, 120);
            $pdf->Write(30, $info["titular"]["cuentaBancaria"][1]["numero"]);
            # Titular
            $pdf->SetXY(103, 114);
            $pdf->Write(50, $info["titular"]["datosPrincipalesFisicas"]["apellido"].' '.$info["titular"]["datosPrincipalesFisicas"]["nombre"]);
            # CUIT
            $pdf->SetXY(103, 127.5);
            $pdf->Write(30, $info["titular"]["datosFiscales"]["cuit"]);
            # CBU
            $pdf->SetXY(103, 131);
            $pdf->Write(30, $info["titular"]["cuentaBancaria"][1]["cbu"]);
            # Alias
            $pdf->SetXY(103, 134.5);
            $pdf->Write(30, $info["titular"]["cuentaBancaria"][1]["alias"]);
        }

        # Pagina 15
        $pdf->AddPage();
        $pdf_2 = $pdf->importPage(15);
        $pdf->useTemplate($pdf_2);

        # Impresión PDF
//        $pdf->Output('../public/PDFs/PF-Editado.pdf', 'I');
        return $pdf;
    }
}
