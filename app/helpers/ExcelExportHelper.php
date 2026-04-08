<?php
// app/helpers/ExcelExportHelper.php

class ExcelExportHelper
{
    private static function cleanOutputBuffer()
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    private static function xmlSafe($value)
    {
        $text = (string)($value ?? '');
        $text = preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $text);
        return htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function columnName($index)
    {
        $name = '';
        $n = $index;
        while ($n > 0) {
            $mod = ($n - 1) % 26;
            $name = chr(65 + $mod) . $name;
            $n = intdiv($n - 1, 26);
        }
        return $name;
    }

    private static function buildSheetXml($rows)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml .= '<sheetData>';

        $rowNumber = 1;
        foreach ($rows as $row) {
            $xml .= '<row r="' . $rowNumber . '">';
            $colNumber = 1;
            foreach ($row as $cellValue) {
                $cellRef = self::columnName($colNumber) . $rowNumber;
                $xml .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . self::xmlSafe($cellValue) . '</t></is></c>';
                $colNumber++;
            }
            $xml .= '</row>';
            $rowNumber++;
        }

        $xml .= '</sheetData>';
        $xml .= '</worksheet>';
        return $xml;
    }

    private static function streamXlsx($rows, $sheetName, $filename)
    {
        self::cleanOutputBuffer();

        $safeFilename = preg_replace('/\.csv$/i', '.xlsx', $filename);
        $tmpFile = tempnam(sys_get_temp_dir(), 'sgpro_xlsx_');

        $zip = new ZipArchive();
        if ($zip->open($tmpFile, ZipArchive::OVERWRITE) !== true) {
            http_response_code(500);
            echo 'No se pudo generar el archivo Excel.';
            exit;
        }

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::xmlSafe($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border/></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';

        $now = gmdate('Y-m-d\TH:i:s\Z');
        $core = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
            . 'xmlns:dc="http://purl.org/dc/elements/1.1/" '
            . 'xmlns:dcterms="http://purl.org/dc/terms/" '
            . 'xmlns:dcmitype="http://purl.org/dc/dcmitype/" '
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>SGPRO</dc:creator>'
            . '<cp:lastModifiedBy>SGPRO</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>'
            . '</cp:coreProperties>';

        $app = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
            . 'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>SGPRO</Application>'
            . '</Properties>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/worksheets/sheet1.xml', self::buildSheetXml($rows));
        $zip->addFromString('xl/styles.xml', $styles);
        $zip->addFromString('docProps/core.xml', $core);
        $zip->addFromString('docProps/app.xml', $app);
        $zip->close();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $safeFilename . '"');
        header('Content-Length: ' . filesize($tmpFile));
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($tmpFile);
        @unlink($tmpFile);
        exit;
    }

    public static function createCvReport($cvData, $filename)
    {
        $rows = [];
        $rows[] = ['REPORTE DE CURRICULOS VITAE'];
        $rows[] = ['Generado: ' . date('Y-m-d H:i:s')];
        $rows[] = [];

        foreach ($cvData as $data) {
            $rows[] = ['DATOS PERSONALES'];
            $rows[] = ['Nombre', $data['name'] ?? ''];
            $rows[] = ['Email', $data['email'] ?? ''];
            $rows[] = ['Cedula/Pasaporte', $data['cedula'] ?? ''];
            $rows[] = ['Telefono', $data['phone'] ?? ''];
            $rows[] = ['Celular', $data['cell_phone'] ?? ''];
            $rows[] = ['Nacionalidad', $data['nationality'] ?? ''];
            $rows[] = ['Ciudad', $data['city'] ?? ''];
            $rows[] = ['Fecha de Nacimiento', $data['birth_date'] ?? ''];
            $rows[] = ['Direccion', $data['address'] ?? ''];
            $rows[] = [];

            $rows[] = ['FORMACION ACADEMICA'];
            $rows[] = ['Institucion', 'Titulo', 'Nivel Educativo', 'Registro SENESCYT'];
            if (!empty($data['education'])) {
                foreach ($data['education'] as $edu) {
                    $rows[] = [
                        $edu['institution_name'] ?? '',
                        $edu['degree_title'] ?? '',
                        $edu['education_level'] ?? '',
                        $edu['senescyt_register'] ?? ''
                    ];
                }
            } else {
                $rows[] = ['Sin registro', '', '', ''];
            }
            $rows[] = [];

            $rows[] = ['EXPERIENCIA PROFESIONAL'];
            $rows[] = ['Empresa/Institucion', 'Cargo', 'Fecha Inicio', 'Fecha Fin', 'Actividades'];
            if (!empty($data['professional_experience'])) {
                foreach ($data['professional_experience'] as $exp) {
                    $rows[] = [
                        $exp['company_name'] ?? '',
                        $exp['position'] ?? '',
                        $exp['start_date'] ?? '',
                        $exp['end_date'] ?? '',
                        $exp['activities_description'] ?? ''
                    ];
                }
            } else {
                $rows[] = ['Sin registro', '', '', '', ''];
            }
            $rows[] = [];
        }

        self::streamXlsx($rows, 'Reporte CVs', $filename);
    }

    public static function createBillingReport($billingData, $filename)
    {
        $rows = [];
        $rows[] = ['REPORTE DE FACTURACION POR USUARIO'];
        $rows[] = ['Generado: ' . date('Y-m-d H:i:s')];
        $rows[] = [];
        $rows[] = ['Nombre del Usuario', 'Email', 'Unidad', 'Periodo', 'Monto', 'Estado', 'Total Usuario'];

        if (empty($billingData)) {
            $rows[] = [];
            $rows[] = ['No hay facturas registradas en el sistema'];
        } else {
            $currentUser = null;
            $userTotal = 0;

            foreach ($billingData as $userId => $data) {
                foreach ($data['invoices'] as $invoice) {
                    if ($currentUser !== $userId) {
                        if ($currentUser !== null) {
                            $rows[] = ['', '', '', 'Total Usuario:', number_format($userTotal, 2), '', ''];
                            $rows[] = [];
                        }
                        $currentUser = $userId;
                        $userTotal = 0;
                    }

                    $period = ($invoice['period_month'] ?? 'N/A') . ' ' . ($invoice['period_year'] ?? '');
                    $amount = (float)($invoice['amount'] ?? 0);

                    $rows[] = [
                        $data['name'] ?? '',
                        $data['email'] ?? '',
                        $invoice['unit_number'] ?? '',
                        $period,
                        number_format($amount, 2),
                        $invoice['status'] ?? '',
                        ''
                    ];

                    $userTotal += $amount;
                }
            }

            if ($currentUser !== null) {
                $rows[] = ['', '', '', 'Total Usuario:', number_format($userTotal, 2), '', ''];
            }
        }

        $totalGeneral = array_reduce($billingData, function ($carry, $item) {
            return $carry + (float)($item['total'] ?? 0);
        }, 0);

        $rows[] = [];
        $rows[] = ['RESUMEN GENERAL'];
        $rows[] = ['Total General de Facturacion', number_format($totalGeneral, 2)];

        self::streamXlsx($rows, 'Facturacion', $filename);
    }

    public static function createPortfolioReport($portfolios, $filename)
    {
        $rows = [];
        $rows[] = ['REPORTE DE PORTAFOLIOS'];
        $rows[] = ['Generado: ' . date('Y-m-d H:i:s')];
        $rows[] = [];
        $rows[] = ['Profesor', 'Email', 'PAO', 'Tipo de Portafolio', 'Unidades', 'Total Unidades', 'Aprobadas', 'Pendientes', 'Sin Registro'];

        foreach ($portfolios as $portfolio) {
            $units = array_map(function ($unit) {
                if (!empty($unit['is_missing'])) {
                    return 'Unidad ' . ($unit['unit_number'] ?? '') . ' (Sin registro)';
                }

                return 'Unidad ' . ($unit['unit_number'] ?? '') . ' (' . (!empty($unit['unit_approved']) ? 'Aprobada' : 'Pendiente') . ')';
            }, $portfolio['units'] ?? []);

            $rows[] = [
                $portfolio['professor_name'] ?? '',
                $portfolio['professor_email'] ?? '',
                $portfolio['pao_name'] ?? '',
                $portfolio['portfolio_type'] ?? '',
                implode(' | ', $units),
                $portfolio['total_units'] ?? count($portfolio['units'] ?? []),
                $portfolio['approved_units'] ?? 0,
                $portfolio['pending_units'] ?? 0,
                $portfolio['missing_units'] ?? 0,
            ];
        }

        $rows[] = [];
        $rows[] = ['Total de Usuarios/PAO:', count($portfolios)];

        self::streamXlsx($rows, 'Portafolios', $filename);
    }

    public static function createTeacherDedicationReport($teachersByDedication, $filename)
    {
        $rows = [];
        $rows[] = ['REPORTE DE DOCENTES POR DEDICACION'];
        $rows[] = ['Generado: ' . date('Y-m-d H:i:s')];
        $rows[] = [];

        $grandTotal = 0;
        foreach ($teachersByDedication as $dedication => $teachers) {
            $rows[] = ['DEDICACION: ' . $dedication];
            $rows[] = ['Total: ' . count($teachers)];
            $rows[] = ['ID', 'Nombre del Docente', 'Email'];
            foreach ($teachers as $teacher) {
                $rows[] = [
                    $teacher['id'] ?? '',
                    $teacher['name'] ?? '',
                    $teacher['email'] ?? ''
                ];
                $grandTotal++;
            }
            $rows[] = [];
        }

        $rows[] = ['RESUMEN GENERAL'];
        $rows[] = ['Total de Docentes:', $grandTotal];

        self::streamXlsx($rows, 'Docentes', $filename);
    }

    public static function createPermissionHistoryReport($rows, $filename)
    {
        self::streamXlsx($rows, 'Historial Permisos', $filename);
    }
}
