<?php
// Implementación simplificada de TCPDF para generar PDFs
class TCPDF {
    protected $orientation;
    protected $unit;
    protected $format;
    protected $unicode;
    protected $encoding;
    protected $diskcache;
    
    protected $pages = [];
    protected $currentPage = 0;
    protected $x = 0;
    protected $y = 0;
    protected $margins = ['left' => 15, 'top' => 27, 'right' => 15];
    protected $headerMargin = 5;
    protected $footerMargin = 10;
    protected $autoPageBreak = true;
    protected $pageBreakTrigger = 25;
    protected $font = ['family' => 'helvetica', 'style' => '', 'size' => 10];
    protected $textColor = [0, 0, 0];
    protected $fillColor = [255, 255, 255];
    protected $creator = '';
    protected $author = '';
    protected $title = '';
    protected $subject = '';
    
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        $this->orientation = $orientation;
        $this->unit = $unit;
        $this->format = $format;
        $this->unicode = $unicode;
        $this->encoding = $encoding;
        $this->diskcache = $diskcache;
        
        $this->AddPage();
    }
    
    public function SetCreator($creator) {
        $this->creator = $creator;
    }
    
    public function SetAuthor($author) {
        $this->author = $author;
    }
    
    public function SetTitle($title) {
        $this->title = $title;
    }
    
    public function SetSubject($subject) {
        $this->subject = $subject;
    }
    
    public function SetMargins($left, $top, $right = null) {
        $this->margins['left'] = $left;
        $this->margins['top'] = $top;
        $this->margins['right'] = $right ?? $left;
    }
    
    public function SetHeaderMargin($margin) {
        $this->headerMargin = $margin;
    }
    
    public function SetFooterMargin($margin) {
        $this->footerMargin = $margin;
    }
    
    public function SetAutoPageBreak($auto, $margin = 0) {
        $this->autoPageBreak = $auto;
        $this->pageBreakTrigger = $margin;
    }
    
    public function setImageScale($scale) {
        // Placeholder para compatibilidad
    }
    
    public function SetFont($family, $style = '', $size = 0) {
        $this->font = [
            'family' => $family,
            'style' => $style,
            'size' => $size ?: $this->font['size']
        ];
    }
    
    public function SetTextColor($r, $g = null, $b = null) {
        if ($g === null && $b === null) {
            $this->textColor = [$r, $r, $r];
        } else {
            $this->textColor = [$r, $g, $b];
        }
    }
    
    public function SetFillColor($r, $g = null, $b = null) {
        if ($g === null && $b === null) {
            $this->fillColor = [$r, $r, $r];
        } else {
            $this->fillColor = [$r, $g, $b];
        }
    }
    
    public function AddPage($orientation = '', $format = '') {
        $this->currentPage++;
        $this->pages[$this->currentPage] = [];
        $this->x = $this->margins['left'];
        $this->y = $this->margins['top'];
        
        $this->Header();
    }
    
    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M') {
        // Simplificado: solo agregar el texto al contenido
        if (!isset($this->pages[$this->currentPage])) {
            $this->pages[$this->currentPage] = [];
        }
        
        $this->pages[$this->currentPage][] = [
            'type' => 'cell',
            'x' => $this->x,
            'y' => $this->y,
            'w' => $w,
            'h' => $h,
            'txt' => $txt,
            'border' => $border,
            'align' => $align,
            'fill' => $fill,
            'font' => $this->font,
            'textColor' => $this->textColor,
            'fillColor' => $this->fillColor
        ];
        
        if ($ln == 1) {
            $this->Ln($h);
        } else {
            $this->x += $w;
        }
        
        // Verificar salto de página
        if ($this->autoPageBreak && $this->y > (297 - $this->pageBreakTrigger)) {
            $this->AddPage();
        }
    }
    
    public function Ln($h = null) {
        $this->x = $this->margins['left'];
        $this->y += $h ?: $this->font['size'] * 0.35;
    }
    
    public function Image($file, $x = '', $y = '', $w = 0, $h = 0, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false) {
        // Placeholder para imágenes
        if (!isset($this->pages[$this->currentPage])) {
            $this->pages[$this->currentPage] = [];
        }
        
        $this->pages[$this->currentPage][] = [
            'type' => 'image',
            'file' => $file,
            'x' => $x ?: $this->x,
            'y' => $y ?: $this->y,
            'w' => $w,
            'h' => $h
        ];
    }
    
    public function GetY() {
        return $this->y;
    }
    
    public function SetY($y) {
        $this->y = $y;
    }
    
    public function getAliasNumPage() {
        return $this->currentPage;
    }
    
    public function getAliasNbPages() {
        return count($this->pages);
    }
    
    public function Header() {
        // Método para ser sobrescrito
    }
    
    public function Footer() {
        // Método para ser sobrescrito
    }
    
    public function Output($name = 'doc.pdf', $dest = 'I') {
        // Generar PDF usando HTML y CSS para conversión
        $html = $this->generateHTML();
        
        if ($dest === 'D') {
            // Forzar descarga
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            // Usar wkhtmltopdf si está disponible, sino usar HTML
            if ($this->isWkhtmltopdfAvailable()) {
                $this->generatePDFWithWkhtmltopdf($html, $name);
            } else {
                $this->generateHTMLToPDF($html);
            }
        }
    }
    
    private function isWkhtmltopdfAvailable() {
        // Verificar si wkhtmltopdf está disponible
        $output = [];
        $return_var = 0;
        exec('which wkhtmltopdf 2>/dev/null', $output, $return_var);
        return $return_var === 0;
    }
    
    private function generatePDFWithWkhtmltopdf($html, $filename) {
        // Crear archivo temporal
        $tempHtml = tempnam(sys_get_temp_dir(), 'pdf_') . '.html';
        file_put_contents($tempHtml, $html);
        
        $tempPdf = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
        
        // Ejecutar wkhtmltopdf
        $command = "wkhtmltopdf --page-size A4 --margin-top 0.75in --margin-right 0.75in --margin-bottom 0.75in --margin-left 0.75in '$tempHtml' '$tempPdf' 2>/dev/null";
        exec($command);
        
        if (file_exists($tempPdf)) {
            readfile($tempPdf);
            unlink($tempPdf);
        } else {
            $this->generateHTMLToPDF($html);
        }
        
        unlink($tempHtml);
    }
    
    private function generateHTMLToPDF($html) {
        // Fallback: generar HTML con CSS para impresión
        echo $html;
        echo '<script>
            window.onload = function() {
                window.print();
                setTimeout(function() {
                    window.close();
                }, 1000);
            };
        </script>';
    }
    
    private function generateHTML() {
        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($this->title) . '</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            color: #3498db;
            margin: 10px 0;
            font-size: 16px;
        }
        .header .date {
            color: #7f8c8d;
            font-size: 10px;
        }
        .info-section {
            margin: 20px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 14px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .info-table td {
            padding: 5px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .info-table td:first-child {
            background: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        .stats-section {
            margin: 20px 0;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .stats-table th {
            background: #3498db;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }
        .stats-table td {
            background: #f8f9fa;
            color: #27ae60;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .data-section {
            margin: 20px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9px;
        }
        .data-table th {
            background: #3498db;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #2980b9;
        }
        .data-table td {
            padding: 4px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .data-table .total-row {
            background: #e8f5e8 !important;
            font-weight: bold;
            color: #27ae60;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #7f8c8d;
            border-top: 1px solid #ecf0f1;
            padding: 10px;
            background: white;
        }
        .no-print {
            margin: 20px 0;
            text-align: center;
        }
        .btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0 5px;
            font-size: 12px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
        }
    </style>
</head>
<body>';
        
        // Header
        $html .= '<div class="header">
            <h1>SISTEMA DE CONTROL DE VENTAS AguaZero C.A.</h1>
            <h2>' . htmlspecialchars($this->title) . '</h2>
            <div class="date">Generado el: ' . date('d/m/Y H:i:s') . '</div>
        </div>';
        
        // Botones de acción (solo visible en pantalla)
        $html .= '<div class="no-print">
            <button onclick="window.print()" class="btn">🖨️ Imprimir / Guardar PDF</button>
            <a href="javascript:history.back()" class="btn btn-secondary">⬅️ Volver</a>
        </div>';
        
        // Procesar contenido de las páginas
        foreach ($this->pages as $pageNum => $pageContent) {
            if ($pageNum > 1) {
                $html .= '<div class="page-break"></div>';
            }
            
            $html .= $this->renderPageContent($pageContent);
        }
        
        // Footer
        $html .= '<div class="footer">
            <p>Sistema de Control de Ventas AguaZero C.A. © ' . date('Y') . ' - Página 1</p>
        </div>';
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    private function renderPageContent($content) {
        $html = '';
        $infoData = [];
        $statsData = [];
        $tableData = [];
        $tableHeaders = [];
        
        // Procesar contenido y agrupar por tipo
        foreach ($content as $item) {
            if ($item['type'] === 'cell') {
                // Determinar si es parte de una tabla o información
                if (!empty($tableHeaders) || $this->isTableHeader($item)) {
                    if (empty($tableHeaders) && $this->isTableHeader($item)) {
                        $tableHeaders[] = $item['txt'];
                    } elseif (!empty($tableHeaders)) {
                        if ($this->isTableHeader($item)) {
                            $tableHeaders[] = $item['txt'];
                        } else {
                            $tableData[] = $item['txt'];
                        }
                    }
                } else {
                    // Es información general
                    $infoData[] = $item['txt'];
                }
            }
        }
        
        // Renderizar información
        if (!empty($infoData)) {
            $html .= '<div class="info-section">
                <h3>INFORMACIÓN DEL REPORTE</h3>
                <table class="info-table">';
            
            for ($i = 0; $i < count($infoData); $i += 2) {
                if (isset($infoData[$i + 1])) {
                    $html .= '<tr>
                        <td>' . htmlspecialchars($infoData[$i]) . '</td>
                        <td>' . htmlspecialchars($infoData[$i + 1]) . '</td>
                    </tr>';
                }
            }
            
            $html .= '</table></div>';
        }
        
        // Renderizar tabla de datos
        if (!empty($tableHeaders) && !empty($tableData)) {
            $html .= '<div class="data-section">
                <h3>DATOS DETALLADOS</h3>
                <table class="data-table">
                    <thead><tr>';
            
            foreach ($tableHeaders as $header) {
                $html .= '<th>' . htmlspecialchars($header) . '</th>';
            }
            
            $html .= '</tr></thead><tbody>';
            
            $colCount = count($tableHeaders);
            for ($i = 0; $i < count($tableData); $i += $colCount) {
                $html .= '<tr>';
                for ($j = 0; $j < $colCount; $j++) {
                    $cellValue = isset($tableData[$i + $j]) ? $tableData[$i + $j] : '';
                    $html .= '<td>' . htmlspecialchars($cellValue) . '</td>';
                }
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table></div>';
        }
        
        return $html;
    }
    
    private function isTableHeader($item) {
        // Detectar si es un header de tabla basado en el estilo
        return isset($item['fillColor']) && 
               $item['fillColor'][0] == 52 && 
               $item['fillColor'][1] == 152 && 
               $item['fillColor'][2] == 219;
    }
}
?>