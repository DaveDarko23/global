<?php
require 'fpdf/fpdf.php';

// Clase para generar el PDF
class PDF extends FPDF
{
    // Función para crear el encabezado del PDF
    function Header()
    {
        $this->SetFont('Arial', 'B', 25);
        $this->Ln(20);
        $this->Cell(80);
        $this->Cell(30, 20, 'Comercio Global',0, 1, 'C');
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 20, 'Detalles de la compra', 0, 1, 'C');
        $this->Ln(20);
    }

    function Content($summary,$total, $estado, $domicilio)
    {
        $this->SetFillColor(100, 89, 201);
        $this->SetFont('Helvetica', 'B', 15);

        $this->Cell(120, 10, "Producto", 1, 0,'C',1);
        $this->Cell(40, 10, "Cantidad", 1, 0,'C',1);
        $this->Cell(30, 10, "Precio", 1, 1,'C',1);

        $this->SetFont('Arial', '', 10);
        $cantidadTotal = 0;
        foreach ($summary as $user) {
            list($id, $name, $Cantidad, $precio) = $user;
            $this->Ln(0.6);
            $this->Cell(120, 10, $name, 'B', 0,'C');
            $this->Cell(40, 10, $Cantidad, 'B', 0,'C');
            $this->Cell(30, 10, '$' . $precio, 'B', 1,'C');
            $cantidadTotal += $Cantidad;
        }

        
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(120, 10, 'Total:', 0, 0,'R');
        $this->SetFont('Arial', '', 10);
        $this->Cell(40, 10, $cantidadTotal, 1, 0,'C');
        $this->Cell(30, 10, '$' . $total, 1, 1,'C');

        $this->Ln(20);

        $this->Cell(13, 7, "Estado: ", 0, 0);
        $this->Cell(60, 7, $estado, 0, 1);
        $this->Cell(17, 7, "Domicilio: ", 0, 0);
        $this->Cell(120, 7, $domicilio, 0, 1);

        $this->Ln(10);
        $this->Cell(80, 10, "Fecha de Compra: ".date("d-m-Y h:i:s"), 0, 1);
    }

    function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 15, 'Gracias por su compra y su confianza', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

function generateContentPdf($summary,$total, $estado, $domicilio, $id)
{
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetMargins(10,10,10);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->Image("./logo.png",0,0,70);
    $pdf->Content($summary,$total, $estado, $domicilio);
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
  
