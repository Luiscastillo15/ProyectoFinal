<?php
require_once 'lib/TCPDF/tcpdf.php';

class PDFGenerator extends TCPDF {
    private $reportTitle;
    private $data;
    private $headers;
    
    public function __construct($title = 'Reporte', $orientation = 'P') {
        // Define constants if not already defined
        if (!defined('PDF_UNIT')) {
            define('PDF_UNIT', 'mm');
        }
        if (!defined('PDF_PAGE_FORMAT')) {
            define('PDF_PAGE_FORMAT', 'A4');
        }
        
        parent::__construct($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $this->reportTitle = $title;
        $this->data = [];
        $this->headers = [];
        
        // Configuración del documento
        $this->SetCreator('Sistema AguaZero C.A.');
        $this->SetAuthor('Sistema de Control de Ventas');
        $this->SetTitle($title);
        $this->SetSubject('Reporte generado automáticamente');
        
        // Configurar márgenes
        $this->SetMargins(15, 27, 15);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);
        
        // Auto page breaks
        $this->SetAutoPageBreak(TRUE, 25);
        
        // Configurar fuente
        $this->setImageScale(1.25);
        
        // Configurar fuente por defecto
        $this->SetFont('helvetica', '', 10);
    }
    
    public function setHeaders($headers) {
        $this->headers = $headers;
    }
    
    public function setData($data) {
        $this->data = $data;
    }
    
    // Header del PDF
    public function Header() {
        // Logo (si existe)
        $logo_path = 'assets/imagenes/Imagen de WhatsApp 2025-06-20 a las 23.47.39_e7804b75.jpg';
        if (file_exists($logo_path)) {
            $this->Image($logo_path, 15, 10, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Título
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 15, 'SISTEMA DE CONTROL DE VENTAS AguaZero C.A.', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(8);
        
        // Subtítulo del reporte
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(52, 152, 219);
        $this->Cell(0, 10, $this->reportTitle, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(8);
        
        // Fecha de generación
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(127, 140, 141);
        $this->Cell(0, 5, 'Generado el: ' . date('d/m/Y H:i:s'), 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10);
    }
    
    // Footer del PDF
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(149, 165, 166);
        $this->Cell(0, 10, 'Sistema de Control de Ventas AguaZero C.A. © ' . date('Y') . ' - Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    
    public function generate($filename = null) {
        if (!$filename) {
            $filename = 'reporte_' . date('Y-m-d_H-i-s') . '.pdf';
        }
        
        // Agregar página
        $this->AddPage();
        
        // Agregar información adicional si existe
        if (isset($this->data['info'])) {
            $this->addInfoSection();
        }
        
        // Agregar estadísticas si existen
        if (isset($this->data['stats'])) {
            $this->addStatsSection();
        }
        
        // Agregar tabla de datos
        if (!empty($this->headers) && !empty($this->data['rows'])) {
            $this->addDataTable();
        }
        
        // Generar el PDF
        $this->Output($filename, 'D');
    }
    
    private function addInfoSection() {
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 8, 'INFORMACIÓN DEL REPORTE', 0, 1, 'L');
        $this->Ln(2);
        
        // Crear tabla de información
        $this->SetFont('helvetica', '', 10);
        $this->SetFillColor(248, 249, 250);
        
        foreach ($this->data['info'] as $key => $value) {
            $this->SetTextColor(44, 62, 80);
            $this->Cell(60, 8, $key . ':', 1, 0, 'L', true);
            $this->SetTextColor(52, 152, 219);
            $this->Cell(0, 8, $value, 1, 1, 'L', false);
        }
        
        $this->Ln(5);
    }
    
    private function addStatsSection() {
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 8, 'ESTADÍSTICAS', 0, 1, 'L');
        $this->Ln(2);
        
        // Calcular ancho de columnas
        $colWidth = (180) / count($this->data['stats']);
        
        $this->SetFont('helvetica', 'B', 10);
        $this->SetFillColor(52, 152, 219);
        $this->SetTextColor(255, 255, 255);
        
        // Headers de estadísticas
        foreach ($this->data['stats'] as $stat) {
            $this->Cell($colWidth, 8, $stat['label'], 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Valores de estadísticas
        $this->SetFont('helvetica', 'B', 12);
        $this->SetFillColor(248, 249, 250);
        $this->SetTextColor(39, 174, 96);
        
        foreach ($this->data['stats'] as $stat) {
            $this->Cell($colWidth, 10, $stat['number'], 1, 0, 'C', true);
        }
        $this->Ln();
        $this->Ln(5);
    }
    
    private function addDataTable() {
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 8, 'DATOS DETALLADOS', 0, 1, 'L');
        $this->Ln(2);
        
        // Calcular ancho de columnas
        $colWidth = 180 / count($this->headers);
        
        // Headers de la tabla
        $this->SetFont('helvetica', 'B', 9);
        $this->SetFillColor(52, 152, 219);
        $this->SetTextColor(255, 255, 255);
        
        foreach ($this->headers as $header) {
            $this->Cell($colWidth, 8, $header, 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Datos de la tabla
        $this->SetFont('helvetica', '', 8);
        $fill = false;
        
        foreach ($this->data['rows'] as $row) {
            $isTotal = isset($row['_is_total']) && $row['_is_total'];
            
            if ($isTotal) {
                $this->SetFont('helvetica', 'B', 9);
                $this->SetFillColor(232, 245, 232);
                $this->SetTextColor(39, 174, 96);
                $fill = true;
            } else {
                $this->SetFont('helvetica', '', 8);
                $this->SetFillColor(248, 249, 250);
                $this->SetTextColor(44, 62, 80);
                $fill = !$fill;
            }
            
            // Verificar si necesitamos una nueva página
            if ($this->GetY() > 250) {
                $this->AddPage();
                
                // Repetir headers
                $this->SetFont('helvetica', 'B', 9);
                $this->SetFillColor(52, 152, 219);
                $this->SetTextColor(255, 255, 255);
                
                foreach ($this->headers as $header) {
                    $this->Cell($colWidth, 8, $header, 1, 0, 'C', true);
                }
                $this->Ln();
                
                // Restaurar configuración para datos
                if ($isTotal) {
                    $this->SetFont('helvetica', 'B', 9);
                    $this->SetFillColor(232, 245, 232);
                    $this->SetTextColor(39, 174, 96);
                } else {
                    $this->SetFont('helvetica', '', 8);
                    $this->SetFillColor(248, 249, 250);
                    $this->SetTextColor(44, 62, 80);
                }
            }
            
            foreach ($this->headers as $key => $header) {
                $cellValue = '';
                if (is_numeric($key)) {
                    $cellValue = isset($row[$key]) ? $row[$key] : '';
                } else {
                    $cellValue = isset($row[$key]) ? $row[$key] : '';
                }
                
                // Limpiar caracteres especiales
                $cellValue = html_entity_decode($cellValue, ENT_QUOTES, 'UTF-8');
                $cellValue = strip_tags($cellValue);
                
                $this->Cell($colWidth, 6, $cellValue, 1, 0, 'C', $fill);
            }
            $this->Ln();
        }
    }
}
?>