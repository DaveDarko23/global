<?php
require 'fpdf/fpdf.php';

// Clase para generar el PDF
class PDF extends FPDF
{
    // Función para crear el encabezado del PDF
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'Detalles de la compra', 0, 0, 'C');
        $this->Ln(20);
    }

    function Content($summary,$total, $estado, $domicilio)
    {
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



        $this->Cell(120, 10, 'Total:', 1, 0);
        $this->Cell(40, 10, $cantidadTotal, 1, 0);
        $this->Cell(30, 10, '$' . $total, 1, 1);

        $this->Ln(20);

        $this->Cell(80, 10, "Estado", 1, 0);
        $this->Cell(100, 10, $estado, 1, 1);

        $this->Ln(1);
        $this->Cell(60, 10, "Domicilio", 1, 0);
        $this->Cell(120, 10, $domicilio, 1, 1);


        $this->Ln(20);

        $this->Cell(80, 10, date("d-m-Y h:i:s"), 1, 0);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

function generateContentPdf($summary,$total, $estado, $domicilio, $id)
{
    $pdf = new PDF();
    $pdf->AddPage();
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
  
