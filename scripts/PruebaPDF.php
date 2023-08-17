<?php
require 'fpdf/fpdf.php';

// Clase para generar el PDF
class PDF extends FPDF
{
    // Función para crear el encabezado del PDF
    function Header()
    {
        // Logo de la empresa
        // $this->Image('logo.png', 10, 6, 30);

        // Fuente y tamaño del título
        $this->SetFont('Arial', 'B', 15);

        // Título del documento
        $this->Cell(80);

        // El título de la página
        $this->Cell(30, 10, 'Detalles de la compra', 0, 0, 'C');

        // Salto de línea
        $this->Ln(20);
    }

    // Función para crear el contenido del PDF
    function Content($summary,$total, $estado, $domicilio)
    {
        // Configuración de la fuente y tamaño
        $this->SetFont('Arial', '', 10);

        $this->Cell(120, 10, "Producto", 1, 0);
        $this->Cell(40, 10, "Cantidad", 1, 0);
        $this->Cell(30, 10, "Precio", 1, 1);

        $cantidadTotal = 0;
        foreach ($summary as $user) {
            list($id, $name, $Cantidad, $precio) = $user;
            $this->Cell(120, 10, $name, 1, 0);
            $this->Cell(40, 10, $Cantidad, 1, 0);
            $this->Cell(30, 10, '$' . $precio, 1, 1);
            $cantidadTotal += $Cantidad;
        }



        // Total de la compra
        $this->Cell(120, 10, 'Total:', 1, 0);
        $this->Cell(40, 10, $cantidadTotal, 1, 0);
        $this->Cell(30, 10, '$' . $total, 1, 1);

        $this->Ln(20);

        $this->Cell(80, 10, "Estado", 1, 0);
        $this->Cell(100, 10, $estado, 1, 0);

        $this->Cell(60, 10, "Domicilio", 1, 0);
        $this->Cell(120, 10, $domicilio, 1, 0);
    }

    // Función para crear el pie de página del PDF
    function Footer()
    {
        // Posicionamiento a 1.5 cm del final
        $this->SetY(-15);

        // Configuración de la fuente y tamaño
        $this->SetFont('Arial', 'I', 10);

        // Número de página
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

function generateContentPdf($summary,$total, $estado, $domicilio, $id)
{
    // Crear una nueva instancia de la clase PDF
    $pdf = new PDF();
    // Añadir una página
    $pdf->AddPage();
    // Generar el contenido del PDF
    $pdf->Content($summary,$total, $estado, $domicilio);
    // Generar el PDF
    $pdf->Output('F', "../pdf/mail-".$id.".pdf");
}


function generatePdf(int $purchaseId)
{
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Hello World!');
    $pdf->Output('F', "../pdf/mail-".$purchaseId.".pdf");
  }
  
