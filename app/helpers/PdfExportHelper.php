<?php
// app/helpers/PdfExportHelper.php
require_once __DIR__ . '/../models/UserModel.php';

class PdfExportHelper
{
    /**
     * Crea un reporte de CVs en PDF (formato HTML imprimible)
     */
    public static function createCvReport($cvData, $filename, $roleFilter = null, $searchName = null)
    {
        $html = self::generateCvHtml($cvData, $roleFilter, $searchName);
        self::outputPdf($html, $filename, 'portrait');
    }

    /**
     * Crea un reporte de facturación en PDF
     */
    public static function createBillingReport($billingData, $filename, $year = null, $searchName = null)
    {
        $html = self::generateBillingHtml($billingData, $year, $searchName);
        self::outputPdf($html, $filename);
    }

    /**
     * Crea un reporte de portafolios en PDF
     */
    public static function createPortfolioReport($portfolios, $filename)
    {
        $html = self::generatePortfolioHtml($portfolios);
        self::outputPdf($html, $filename, 'landscape');
    }

    /**
     * Crea un reporte de docentes por dedicación en PDF
     */
    public static function createTeacherDedicationReport($teachersByDedication, $filename, $searchName = null, $selectedDedication = null)
    {
        $html = self::generateTeacherDedicationHtml($teachersByDedication, $searchName, $selectedDedication);
        self::outputPdf($html, $filename);
    }

    // =============== MÉTODOS PRIVADOS ===============

    private static function outputPdf($html, $filename, $orientation = 'landscape')
    {
        $download = isset($_GET['download']) && $_GET['download'] === '1';

        // Vista previa HTML por defecto para mantener filtros y navegación.
        if (!$download) {
            header('Content-Type: text/html; charset=UTF-8');
            echo $html;
            exit;
        }

        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoloadPath)) {
            header('Content-Type: text/html; charset=UTF-8');
            echo '<h2>Error de exportación PDF</h2>';
            echo '<p>No se encontró la librería de PDF. Ejecuta <strong>composer install</strong> en la raíz del proyecto.</p>';
            exit;
        }

        require_once $autoloadPath;

        // Eliminar scripts (no se anidan, regex es segura) y ocultar no-print via CSS
        $pdfHtml = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $pdfHtml = '<style>th,.header,.header p{color:#000 !important;}.no-print,.action-buttons,.print-button{display:none !important;height:0 !important;overflow:hidden !important;margin:0 !important;padding:0 !important;}</style>' . $pdfHtml;

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new \Dompdf\Dompdf($options);

        $dompdf->loadHtml($pdfHtml, 'UTF-8');
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();

        $safeFilename = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $filename);
        $dompdf->stream($safeFilename, ['Attachment' => true]);
        exit;
    }

    private static function getCurrentPdfDownloadUrl()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if ($requestUri === '') {
            return '#';
        }

        $parts = parse_url($requestUri);
        $path = $parts['path'] ?? '';
        $params = [];
        parse_str($parts['query'] ?? '', $params);

        $params['format'] = 'pdf';
        $params['download'] = '1';

        return htmlspecialchars($path . '?' . http_build_query($params), ENT_QUOTES, 'UTF-8');
    }

    private static function getPublicImageSource($relativePath)
    {
        $relativePath = '/' . ltrim((string) $relativePath, '/');
        $absolutePath = realpath(__DIR__ . '/../../public' . $relativePath);

        if ($absolutePath && is_file($absolutePath) && is_readable($absolutePath)) {
            $mimeType = function_exists('mime_content_type') ? mime_content_type($absolutePath) : 'image/png';
            $binary = file_get_contents($absolutePath);
            if ($binary !== false) {
                return 'data:' . $mimeType . ';base64,' . base64_encode($binary);
            }
        }

        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        return htmlspecialchars($basePath . $relativePath, ENT_QUOTES, 'UTF-8');
    }

    private static function generateCvHtml($cvData, $roleFilter = null, $searchName = null)
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $downloadPdfUrl = self::getCurrentPdfDownloadUrl();
        $generatedDate = date('Y-m-d H:i:s');
        $filterText = 'Profesores';
        if ($searchName) {
            if ($filterText) $filterText .= " | ";
            $filterText .= "Búsqueda: $searchName";
        }

        $logoSrc = self::getPublicImageSource('/img/logo_sgpro.jpg');
        $safeSearchName = htmlspecialchars((string)($searchName ?? ''), ENT_QUOTES, 'UTF-8');

        $css = file_exists(__DIR__ . '/../../public/css/reportes.css') ? file_get_contents(__DIR__ . '/../../public/css/reportes.css') : '';
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hoja de Vida Institucional 2025</title>
    <style>
$css
    </style>
</head>
<body>
    <div class="container">
        <div>
            <!-- Membrete y filtro juntos para evitar salto de página -->
            <div style="display: flex; flex-direction: column; gap: 0;">
                <div class="letterhead">
                    <img src="$logoSrc" alt="Logo institucional" class="letterhead-logo" style="max-height: 60px; max-width: 80px; width: auto; height: auto; display: block; object-fit: contain; margin: 0 18px 0 0;">
                    <div class="letterhead-text">
                        <h2>Sistema de Gestion Profesoral - SGPRO</h2>
                        <p>Reporte institucional de curriculos de profesores</p>
                    </div>
                </div>
                <!-- El bloque de filtros solo se muestra en pantalla, nunca en PDF -->
                <div class="no-print" style="display: flex; gap: 12px; margin-bottom: 0; align-items: center; flex-wrap: wrap; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 20px; border-radius: 12px; border: 2px solid #e2e8f0;">
                    <a class="print-button" href="$downloadPdfUrl" style="margin-bottom: 0; display: inline-flex; align-items: center; text-decoration: none;">
                        <span>Descargar PDF</span>
                    </a>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="search-name" style="font-weight: 600; color: #475569;">Nombre:</label>
                        <input type="text" id="search-name" value="$safeSearchName" placeholder="Buscar por nombre..." style="padding: 10px 14px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 220px; transition: all 0.2s;">
                    </div>
                    <button onclick="applySearch()" style="padding: 10px 18px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);">Buscar</button>
                    <button type="button" onclick="clearFilters(event)" style="padding: 10px 18px; background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);">Limpiar</button>
                </div>
                <div class="header">
                    <h1>Hoja de Vida Institucional 2026</h1>
                    <p>Generado: <strong>$generatedDate</strong><span class="filter-info"> | Filtros: <strong>$filterText</strong></span></p>
                </div>
            </div>
        </div>
<script>
function applySearch() {
    const name = document.getElementById('search-name').value;
    const url = new URL(window.location.href);
    if (name) {
        url.searchParams.set('name', name);
    } else {
        url.searchParams.delete('name');
    }
    window.location.href = url.toString();
}
function clearFilters(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    // Limpiar solo el campo de búsqueda y recargar sin parámetros
    var searchInput = document.getElementById('search-name');
    if (searchInput) searchInput.value = '';
    var url = new URL(window.location.href);
    url.search = '';
    //window.location.href = url.pathname;
    return false;
}
</script>
HTML;

        if (empty($cvData)) {
            $html .= <<<HTML
        <div class="no-data" style="text-align: center; padding: 50px; color: #64748b; font-size: 18px;">
            ℹ️ No hay usuarios con CV registrado para el filtro seleccionado
        </div>
HTML;
        }

        foreach ($cvData as $userId => $data) {
            // $safeName eliminado para no mostrar el nombre

            $safeName = htmlspecialchars((string)($data['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $safeEmail = htmlspecialchars((string)($data['email'] ?? ''), ENT_QUOTES, 'UTF-8');
            $safeCedula = htmlspecialchars((string)($data['cedula'] ?? ''), ENT_QUOTES, 'UTF-8');
            $safePhone = htmlspecialchars((string)($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
            $safeCell = htmlspecialchars((string)($data['cell_phone'] ?? ''), ENT_QUOTES, 'UTF-8');
            $safeNationality = htmlspecialchars((string)($data['nationality'] ?? ''), ENT_QUOTES, 'UTF-8');
            $safeCity = htmlspecialchars((string)($data['city'] ?? ''), ENT_QUOTES, 'UTF-8');
            $safeBirthDate = htmlspecialchars((string)($data['birth_date'] ?? ''), ENT_QUOTES, 'UTF-8');
            $safeAddress = htmlspecialchars((string)($data['address'] ?? ''), ENT_QUOTES, 'UTF-8');


            $photoPath = trim((string)($data['photo_path'] ?? ''));
            if ($photoPath !== '' && !str_starts_with($photoPath, '/')) {
                $photoPath = '/' . $photoPath;
            }

            // Imagen por defecto si la foto está vacía o rota
            $defaultPhoto = self::getPublicImageSource('/img/default-profile.png');
            $photoUrl = '';
            $photoSrc = $defaultPhoto;
            if ($photoPath !== '') {
                $photoUrl = htmlspecialchars($basePath . $photoPath, ENT_QUOTES, 'UTF-8');
                $absolutePhotoPath = realpath(__DIR__ . '/../../public' . $photoPath);
                if ($absolutePhotoPath && is_file($absolutePhotoPath) && is_readable($absolutePhotoPath)) {
                    $mimeType = function_exists('mime_content_type') ? mime_content_type($absolutePhotoPath) : 'image/jpeg';
                    $binary = file_get_contents($absolutePhotoPath);
                    if ($binary !== false) {
                        $photoSrc = 'data:' . $mimeType . ';base64,' . base64_encode($binary);
                    }
                }
            }

            $html .= <<<HTML
        <div class="user-section">
            <div class="user-content">
                <div style="display: flex; flex-direction: row; align-items: flex-start; gap: 32px; margin-bottom: 18px;">
                    <div class="personal-info-list" style="flex: 1;">
                        <div style="font-size: 1.5em; font-weight: bold; margin-bottom: 8px;">$safeName</div>
                        <div><strong>Email:</strong> $safeEmail</div>
                        <div><strong>Cédula/Pasaporte:</strong> $safeCedula</div>
                        <div><strong>Teléfono:</strong> $safePhone</div>
                        <div><strong>Celular:</strong> $safeCell</div>
                        <div><strong>Nacionalidad:</strong> $safeNationality</div>
                        <div><strong>Ciudad:</strong> $safeCity</div>
                        <div><strong>Fecha de Nacimiento:</strong> $safeBirthDate</div>
                        <div><strong>Dirección:</strong> $safeAddress</div>
                    </div>
                    <div class="profile-photo-cell" style="align-self: flex-start;">
                        <img src="$photoSrc" alt="Foto de perfil" class="profile-photo">
                    </div>
                </div>

            <div class="section-title" style="font-weight: bold;">1. Formación Académica</div>
HTML;

            $html .= '<table><tr><th>Institución</th><th>Título</th><th>Nivel Educativo</th><th>Registro SENESCYT</th></tr>';
            if (!empty($data['education'])) {
                foreach ($data['education'] as $edu) {
                    $institution = htmlspecialchars($edu['institution_name'] ?? '', ENT_QUOTES);
                    $degree = htmlspecialchars($edu['degree_title'] ?? '', ENT_QUOTES);
                    $level = htmlspecialchars($edu['education_level'] ?? '', ENT_QUOTES);
                    $senescyt = htmlspecialchars($edu['senescyt_register'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$institution</td><td>$degree</td><td>$level</td><td>$senescyt</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="4" class="no-data">Sin registro de educación</td></tr>';
            }
            $html .= '</table>';

            $html .= '<div class="section-title" style="font-weight: bold;">2. Experiencia Profesional</div>';
            $html .= '<table><tr><th>Empresa/Institución</th><th>Cargo</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Actividades</th></tr>';
            if (!empty($data['professional_experience'])) {
                foreach ($data['professional_experience'] as $exp) {
                    $company = htmlspecialchars($exp['company_name'] ?? '', ENT_QUOTES);
                    $position = htmlspecialchars($exp['position'] ?? '', ENT_QUOTES);
                    $startDate = htmlspecialchars($exp['start_date'] ?? '', ENT_QUOTES);
                    $endDate = htmlspecialchars($exp['end_date'] ?? '', ENT_QUOTES);
                    $activities = htmlspecialchars($exp['activities_description'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$company</td><td>$position</td><td>$startDate</td><td>$endDate</td><td>$activities</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="5" class="no-data">Sin registro de experiencia profesional</td></tr>';
            }
            $html .= '</table>';

            $html .= '<div class="section-title" style="font-weight: bold;">3. Capacitaciones y Cursos</div>';
            $html .= '<table><tr><th>Institución</th><th>Nombre del Evento</th><th>Fecha</th><th>Descripción</th></tr>';
            if (!empty($data['training'])) {
                foreach ($data['training'] as $training) {
                    $institution = htmlspecialchars($training['institution'] ?? '', ENT_QUOTES);
                    $eventName = htmlspecialchars($training['name'] ?? '', ENT_QUOTES);
                    $date = htmlspecialchars($training['date'] ?? '', ENT_QUOTES);
                    $description = htmlspecialchars($training['description'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$institution</td><td>$eventName</td><td>$date</td><td>$description</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="4" class="no-data">Sin registro de capacitaciones</td></tr>';
            }
            $html .= '</table>';

            // EXPERIENCIA DOCENTE
            $html .= '<div class="section-title" style="font-weight: bold;">4. Experiencia Docente</div>';
            $html .= '<table><tr><th>IES</th><th>Denominación</th><th>Asignaturas</th><th>Fecha Inicio</th><th>Fecha Fin</th></tr>';
            if (!empty($data['teaching_experience'])) {
                foreach ($data['teaching_experience'] as $teaching) {
                    $ies = htmlspecialchars($teaching['ies'] ?? '', ENT_QUOTES);
                    $denomination = htmlspecialchars($teaching['denomination'] ?? '', ENT_QUOTES);
                    $subjects = htmlspecialchars($teaching['subjects'] ?? '', ENT_QUOTES);
                    $startDate = htmlspecialchars($teaching['start_date'] ?? '', ENT_QUOTES);
                    $endDate = htmlspecialchars($teaching['end_date'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$ies</td><td>$denomination</td><td>$subjects</td><td>$startDate</td><td>$endDate</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="5" class="no-data">Sin registro de experiencia docente</td></tr>';
            }
            $html .= '</table>';

            // GESTIÓN ACADÉMICA
            $html .= '<div class="section-title" style="font-weight: bold;">5. Gestión Académica</div>';
            $html .= '<table><tr><th>IES</th><th>Cargo</th><th>Actividades</th><th>Fecha Inicio</th><th>Fecha Fin</th></tr>';
            if (!empty($data['academic_management'])) {
                foreach ($data['academic_management'] as $management) {
                    $ies = htmlspecialchars($management['ies_name'] ?? '', ENT_QUOTES);
                    $position = htmlspecialchars($management['position'] ?? '', ENT_QUOTES);
                    $activities = htmlspecialchars($management['activities_description'] ?? '', ENT_QUOTES);
                    $startDate = htmlspecialchars($management['start_date'] ?? '', ENT_QUOTES);
                    $endDate = htmlspecialchars($management['end_date'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$ies</td><td>$position</td><td>$activities</td><td>$startDate</td><td>$endDate</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="5" class="no-data">Sin registro de gestión académica</td></tr>';
            }
            $html .= '</table>';

            // PROYECTOS DE INVESTIGACIÓN
            $html .= '<div class="section-title" style="font-weight: bold;">6. Proyectos de Investigación</div>';
            $html .= '<table><tr><th>Denominación</th><th>Alcance</th><th>Responsabilidad</th><th>Año</th><th>Duración</th><th>Entidad</th></tr>';
            if (!empty($data['research_projects'])) {
                foreach ($data['research_projects'] as $project) {
                    $denomination = htmlspecialchars($project['denomination'] ?? '', ENT_QUOTES);
                    $scope = htmlspecialchars($project['scope'] ?? '', ENT_QUOTES);
                    $responsibility = htmlspecialchars($project['responsibility'] ?? '', ENT_QUOTES);
                    $year = htmlspecialchars($project['year'] ?? '', ENT_QUOTES);
                    $duration = htmlspecialchars($project['duration'] ?? '', ENT_QUOTES);
                    $entity = htmlspecialchars($project['entity_name'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$denomination</td><td>$scope</td><td>$responsibility</td><td>$year</td><td>$duration</td><td>$entity</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="6" class="no-data">Sin registro de proyectos de investigación</td></tr>';
            }
            $html .= '</table>';

            // PUBLICACIONES
            $html .= '<div class="section-title" style="font-weight: bold;">7. Publicaciones</div>';
            $html .= '<table><tr><th>Título</th><th>Tipo</th><th>Editorial/Revista</th><th>ISBN/ISSN</th><th>Autoría</th></tr>';
            if (!empty($data['publications'])) {
                foreach ($data['publications'] as $publication) {
                    $title = htmlspecialchars($publication['publication_title'] ?? '', ENT_QUOTES);
                    $type = htmlspecialchars($publication['production_type'] ?? '', ENT_QUOTES);
                    $publisher = htmlspecialchars($publication['publisher_magazine'] ?? '', ENT_QUOTES);
                    $isbn = htmlspecialchars($publication['isbn_issn'] ?? '', ENT_QUOTES);
                    $authorship = htmlspecialchars($publication['authorship'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$title</td><td>$type</td><td>$publisher</td><td>$isbn</td><td>$authorship</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="5" class="no-data">Sin registro de publicaciones</td></tr>';
            }
            $html .= '</table>';

            // PONENCIAS
            $html .= '<div class="section-title" style="font-weight: bold;">8. Ponencias</div>';
            $html .= '<table><tr><th>Institución</th><th>Año</th><th>Presentación</th></tr>';
            if (!empty($data['presentations'])) {
                foreach ($data['presentations'] as $presentation) {
                    $institution = htmlspecialchars($presentation['institution_name'] ?? '', ENT_QUOTES);
                    $year = htmlspecialchars($presentation['year'] ?? '', ENT_QUOTES);
                    $presentationTitle = htmlspecialchars($presentation['presentation'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$institution</td><td>$year</td><td>$presentationTitle</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="3" class="no-data">Sin registro de ponencias</td></tr>';
            }
            $html .= '</table>';

            // DIRECCIÓN DE TESIS
            $html .= '<div class="section-title" style="font-weight: bold;">9. Dirección de Tesis</div>';
            $html .= '<table><tr><th>Título de Tesis</th><th>Estudiante</th><th>Universidad</th><th>Programa Académico</th></tr>';
            if (!empty($data['thesis_direction'])) {
                foreach ($data['thesis_direction'] as $thesis) {
                    $title = htmlspecialchars($thesis['thesis_title'] ?? '', ENT_QUOTES);
                    $student = htmlspecialchars($thesis['student_name'] ?? '', ENT_QUOTES);
                    $university = htmlspecialchars($thesis['university_name'] ?? '', ENT_QUOTES);
                    $program = htmlspecialchars($thesis['academic_program'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$title</td><td>$student</td><td>$university</td><td>$program</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="4" class="no-data">Sin registro de dirección de tesis</td></tr>';
            }
            $html .= '</table>';

            // PROYECTOS DE VINCULACIÓN
            $html .= '<div class="section-title" style="font-weight: bold;">10. Proyectos de Vinculación</div>';
            $html .= '<table><tr><th>Nombre del Proyecto</th><th>Institución</th><th>Fecha Inicio</th><th>Fecha Fin</th></tr>';
            if (!empty($data['outreach_projects'])) {
                foreach ($data['outreach_projects'] as $outreach) {
                    $projectName = htmlspecialchars($outreach['project_name'] ?? '', ENT_QUOTES);
                    $institution = htmlspecialchars($outreach['institution_name'] ?? '', ENT_QUOTES);
                    $startDate = htmlspecialchars($outreach['start_date'] ?? '', ENT_QUOTES);
                    $endDate = htmlspecialchars($outreach['end_date'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$projectName</td><td>$institution</td><td>$startDate</td><td>$endDate</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="4" class="no-data">Sin registro de proyectos de vinculación</td></tr>';
            }
            $html .= '</table>';

            // REFERENCIAS LABORALES
            $html .= '<div class="section-title" style="font-weight: bold;">11. Referencias Laborales</div>';
            $html .= '<table><tr><th>Persona de Contacto</th><th>Relación/Cargo</th><th>Organización/Empresa</th><th>Número de Contacto</th></tr>';
            if (!empty($data['work_references'])) {
                foreach ($data['work_references'] as $reference) {
                    $person = htmlspecialchars($reference['contact_person'] ?? '', ENT_QUOTES);
                    $relation = htmlspecialchars($reference['relation_position'] ?? '', ENT_QUOTES);
                    $organization = htmlspecialchars($reference['organization_company'] ?? '', ENT_QUOTES);
                    $contact = htmlspecialchars($reference['contact_number'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$person</td><td>$relation</td><td>$organization</td><td>$contact</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="4" class="no-data">Sin registro de referencias laborales</td></tr>';
            }
            $html .= '</table>';

            // REFERENCIAS PERSONALES
            $html .= '<div class="section-title" style="font-weight: bold;">12. Referencias Personales</div>';
            $html .= '<table><tr><th>Persona de Contacto</th><th>Tipo de Relación</th><th>Número de Contacto</th></tr>';
            if (!empty($data['personal_references'])) {
                foreach ($data['personal_references'] as $reference) {
                    $person = htmlspecialchars($reference['contact_person'] ?? '', ENT_QUOTES);
                    $relationship = htmlspecialchars($reference['relationship_type'] ?? '', ENT_QUOTES);
                    $contact = htmlspecialchars($reference['contact_number'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$person</td><td>$relationship</td><td>$contact</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="3" class="no-data">Sin registro de referencias personales</td></tr>';
            }
            $html .= '</table>';

            // IDIOMAS
            $html .= '<div class="section-title" style="font-weight: bold;">13. Idiomas</div>';
            $html .= '<table><tr><th>Idioma</th><th>Nivel</th></tr>';
            if (!empty($data['languages'])) {
                foreach ($data['languages'] as $language) {
                    $languageName = htmlspecialchars($language['language'] ?? '', ENT_QUOTES);
                    $level = htmlspecialchars($language['level'] ?? '', ENT_QUOTES);
                    $html .= "<tr><td>$languageName</td><td>$level</td></tr>";
                }
            } else {
                $html .= '<tr><td colspan="2" class="no-data">Sin registro de idiomas</td></tr>';
            }
            $html .= '</table>';

            $html .= '</div>'; // Cierre de user-content
            $html .= '</div>'; // Cierre de user-section
        }

        $html .= <<<HTML
        <div class="action-buttons no-print">
            <a href="$basePath/reports" class="btn-action btn-secondary">
                <span>⬅</span> Volver a Reportes
            </a>
            <a href="$basePath/reports/billing-by-user?format=pdf" class="btn-action btn-primary">
                <span>💰</span> Reporte de Facturación
            </a>
            <a href="$basePath/reports/portfolios?format=pdf" class="btn-action btn-primary">
                <span>🎯</span> Reporte de Portafolios
            </a>
            <a href="$basePath/reports/teachers-by-dedication?format=pdf" class="btn-action btn-primary">
                <span>👨‍🏫</span> Docentes por Dedicación
            </a>
        </div>
    </div>
</body>
</html>
HTML;
        return $html;
    }

    private static function generateBillingHtml($billingData, $year = null, $searchName = null)
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $downloadPdfUrl = self::getCurrentPdfDownloadUrl();
        $generatedDate = date('Y-m-d H:i:s');
        $yearFilter = $year ? "(Año: $year)" : "(Todos los años)";
        $searchFilter = $searchName ? "(Búsqueda: $searchName)" : "";
        $filterText = $yearFilter;
        if ($searchFilter) {
            $filterText .= " " . $searchFilter;
        }
        $currentYear = date('Y');
        $logoSrc = self::getPublicImageSource('/img/logo_sgpro.jpg');
        // Construir las opciones del selector de años
        $yearOptions = '<option value="">Todos los años</option>';
        for ($y = $currentYear; $y >= 2020; $y--) {
            $selected = ($year && $y == $year) ? 'selected' : '';
            $yearOptions .= "<option value='$y' $selected>$y</option>";
        }
        $safeSearchName = htmlspecialchars((string)($searchName ?? ''), ENT_QUOTES, 'UTF-8');
        $css = file_exists(__DIR__ . '/../../public/css/reportes.css') ? file_get_contents(__DIR__ . '/../../public/css/reportes.css') : '';
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Facturación</title>
    <style>
$css
    </style>
</head>
<body>
    <div class="container">
      <div class="main-block">
        <div class="letterhead">
            <img src="$logoSrc" alt="Logo institucional" class="letterhead-logo" style="max-height: 60px; max-width: 80px; width: auto; height: auto; display: block; object-fit: contain; margin: 0 18px 0 0;">
            <div class="letterhead-text">
                <h2>Sistema de Gestion Profesoral - SGPRO</h2>
                <p>Reporte institucional de facturación</p>
            </div>
        </div>
        <!-- Bloque de filtros solo visible en pantalla, nunca en PDF -->
        <div class="no-print" style="display: flex; gap: 12px; margin-bottom: 0; align-items: center; flex-wrap: wrap; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 20px; border-radius: 12px; border: 2px solid #e2e8f0;">
            <a class="print-button" href="$downloadPdfUrl" style="margin-bottom: 0; display: inline-flex; align-items: center; text-decoration: none;">
                <span>Descargar PDF</span>
            </a>
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="search-name" style="font-weight: 600; color: #475569;">Nombre:</label>
                <input type="text" id="search-name" value="$safeSearchName" placeholder="Buscar por nombre..." style="padding: 10px 14px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 220px; transition: all 0.2s;">
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="year-select" style="font-weight: 600; color: #475569;">Año:</label>
                <select id="year-select" style="padding: 10px 14px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 140px; transition: all 0.2s;">
                    $yearOptions
                </select>
            </div>
            <button onclick="applyBillingFilters()" style="padding: 10px 18px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);">Buscar</button>
            <button type="button" onclick="clearBillingFilters(event)" style="padding: 10px 18px; background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);">Limpiar</button>
        </div>
        <script>
        function applyBillingFilters() {
            const name = document.getElementById('search-name').value;
            const year = document.getElementById('year-select').value;
            const url = new URL(window.location.href);
            if (name) {
                url.searchParams.set('name', name);
            } else {
                url.searchParams.delete('name');
            }
            if (year) {
                url.searchParams.set('year', year);
            } else {
                url.searchParams.delete('year');
            }
            window.location.href = url.toString();
        }
        function clearBillingFilters(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            document.getElementById('search-name').value = '';
            document.getElementById('year-select').value = '';
            var url = new URL(window.location.href);
            url.search = '';
            window.location.href = url.pathname;
            return false;
        }
        // Seleccionar el año actual si corresponde
        document.addEventListener('DOMContentLoaded', function() {
            var yearSelect = document.getElementById('year-select');
            if (yearSelect && '$year') {
                yearSelect.value = '$year';
            }
        });
        </script>
        <div class="header">
            <h1>REPORTE DE FACTURACIÓN POR USUARIO</h1>
            <p>Generado: <strong>$generatedDate</strong><span class="filter-info"> | <strong>$filterText</strong></span></p>
        </div>
        <table>

        <table>
            <thead>
                <tr>
                    <th>Nombre del Usuario</th>
                    <th>Email</th>
                    <th>Unidad</th>
                    <th>Período</th>
                    <th>Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
HTML;

        $totalGeneral = 0;
        
        if (empty($billingData)) {
            $html .= <<<HTML
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #999; font-style: italic;">
                        ℹ️ No hay facturas registradas en el sistema
                    </td>
                </tr>
HTML;
        } else {
            foreach ($billingData as $userId => $data) {
                $userSubtotal = 0;
                foreach ($data['invoices'] as $invoice) {
                    $period = ($invoice['period_month'] ?? 'N/A') . ' ' . ($invoice['period_year'] ?? '');
                    $amount = number_format($invoice['amount'] ?? 0, 2, '.', ',');
                    $html .= "<tr>";
                    $html .= "<td>{$data['name']}</td>";
                    $html .= "<td>{$data['email']}</td>";
                    $html .= "<td>" . ($invoice['unit_number'] ?? '') . "</td>";
                    $html .= "<td>$period</td>";
                    $html .= "<td>\$" . $amount . "</td>";
                    $html .= "<td>" . ($invoice['status'] ?? '') . "</td>";
                    $html .= "</tr>";
                    $userSubtotal += $invoice['amount'] ?? 0;
                    $totalGeneral += $invoice['amount'] ?? 0;
                }
                $subtotal = number_format($userSubtotal, 2, '.', ',');
                $html .= "<tr class='total-row'><td colspan='4'>Subtotal de " . htmlspecialchars($data['name']) . ":</td><td>\$$subtotal</td><td></td></tr>";
            }
        }

        $totalFormatted = number_format($totalGeneral, 2, '.', ',');
        $total_invoices = array_reduce($billingData, function ($carry, $item) {
            return $carry + count($item['invoices']);
        }, 0);

        $usuarioCount = count($billingData);

        $html .= "</tbody>\n";
        $html .= "</table>\n";

        // Agregar bloque de resumen y botones de acción
        $html .= '<div class="summary">'
            . '<h3> Resumen General</h3>'
            . '<p><strong>Total de Facturación:</strong> $' . $totalFormatted . '</p>'
            . '<p><strong>Total de Registros:</strong> ' . $total_invoices . '</p>'
            . '<p><strong>Cantidad de Usuarios:</strong> ' . $usuarioCount . '</p>'
            . '</div>';

        $html .= '<div class="action-buttons no-print">'
            . '<a href="' . $basePath . '/reports" class="btn-action btn-secondary">'
            . '<span>⬅</span> Volver a Reportes</a>'
            . '<a href="' . $basePath . '/reports/cv-by-user?format=pdf" class="btn-action btn-primary">'
            . '<span>📄</span> Reporte de Currículos</a>'
            . '<a href="' . $basePath . '/reports/portfolios?format=pdf" class="btn-action btn-primary">'
            . '<span>🎯</span> Reporte de Portafolios</a>'
            . '<a href="' . $basePath . '/reports/teachers-by-dedication?format=pdf" class="btn-action btn-primary">'
            . '<span>👨‍🏫</span> Docentes por Dedicación</a>'
            . '</div>';

        $html .= '</div></body></html>';
        return $html;
    }

    private static function generatePortfolioHtml($portfolios)
    {
        // Instanciar modelo de usuario para buscar emails y nombres de aprobadores
        $userModel = new UserModel();
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $downloadPdfUrl = self::getCurrentPdfDownloadUrl();
        $generatedDate = date('Y-m-d H:i:s');
        $logoSrc = self::getPublicImageSource('/img/logo_sgpro.jpg');
        $css = file_exists(__DIR__ . '/../../public/css/reportes.css') ? file_get_contents(__DIR__ . '/../../public/css/reportes.css') : '';
        // Estilos adicionales para PDF landscape del reporte de portafolios
        $css .= <<<'PDFCSS'

/* === Estilos específicos para exportación PDF de portafolios (landscape A4) === */
.portfolio-pdf-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse !important;
    font-size: 10px !important;
}
.portfolio-pdf-table th,
.portfolio-pdf-table td {
    word-break: break-word !important;
    overflow-wrap: break-word !important;
    padding: 6px 5px !important;
    text-align: center;
    vertical-align: middle;
    border: 1px solid #cbd5e1;
}
.portfolio-pdf-table th {
    background: #1e40af !important;
    color: #fff !important;
    font-size: 9px !important;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
/* Anchos proporcionales para 10 columnas en landscape */
.portfolio-pdf-table .col-profesor { width: 16%; }
.portfolio-pdf-table .col-email    { width: 18%; }
.portfolio-pdf-table .col-pao      { width: 10%; }
.portfolio-pdf-table .col-unit     { width: 9%; }
.portfolio-pdf-table .col-count    { width: 8%; }

@media print {
    body { margin: 0 !important; padding: 0 !important; }
    .container { padding: 8px !important; margin: 0 !important; max-width: 100% !important; }
    .header { padding: 8px !important; margin-bottom: 8px !important; }
    .header h1 { font-size: 16px !important; }
    .header p { font-size: 10px !important; }
    .summary { padding: 8px !important; margin-top: 8px !important; font-size: 10px !important; }
    .summary h3 { font-size: 13px !important; }
    .letterhead { padding: 6px 8px !important; margin-bottom: 4px !important; }
    .letterhead h2 { font-size: 14px !important; }
    .letterhead p { font-size: 10px !important; }
    .letterhead-logo { width: 50px !important; height: 50px !important; }
    .portfolio-pdf-table { font-size: 9px !important; }
    .portfolio-pdf-table th { font-size: 8px !important; padding: 4px 3px !important; }
    .portfolio-pdf-table td { font-size: 9px !important; padding: 4px 3px !important; }
}
PDFCSS;

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Portafolios</title>
    <style>
$css
    </style>
</head>
<body>
    <div class="container">
        <div class="letterhead" style="display: flex; align-items: center; gap: 14px; padding: 10px 12px; border: 1px solid #3b82f6; border-radius: 6px; background: #e0e7ef; margin-bottom: 2px;">
            <img src="$logoSrc" alt="Logo institucional" class="letterhead-logo" style="width: 68px; height: 68px; object-fit: cover; border-radius: 8px; background: #f1f5f9; border: 2px solid #3b82f6; padding: 2px; box-shadow: 0 2px 8px rgba(59,130,246,0.10);">
            <div class="letterhead-text" style="margin: 0;">
                <h2 style="margin: 0; font-size: 18px; color: #2563eb; text-transform: uppercase; letter-spacing: 0.2px;">SISTEMA DE GESTION PROFESORAL - SGPRO</h2>
                <p style="margin: 2px 0 0; color: #334155; font-size: 12px;">Reporte institucional de portafolios</p>
            </div>
        </div>
        <div style="display: flex; gap: 12px; margin-bottom: 30px; align-items: center; flex-wrap: wrap; background: #e0e7ef; padding: 20px; border-radius: 12px; border: 2px solid #3b82f6;" class="no-print">
            <a class="print-button" href="$downloadPdfUrl" style="margin-bottom: 0; display: inline-flex; align-items: center; text-decoration: none;">
                <span>⬇️ Descargar PDF</span>
            </a>
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="search-portfolio-name" style="font-weight: 600; color: #92400e;">Nombre:</label>
                <input type="text" id="search-portfolio-name" value="" placeholder="Buscar por nombre..." style="padding: 10px 14px; border: 2px solid #2563eb; border-radius: 8px; font-size: 14px; width: 220px; transition: all 0.2s;">
            </div>
            <button onclick="applyPortfolioSearch()" style="padding: 10px 18px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);">Buscar</button>
            <button onclick="clearPortfolioFilters()" style="padding: 10px 18px; background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);">Limpiar</button>
        </div>
        <div class="header">
            <h1>REPORTE DE PORTAFOLIOS</h1>
            <p>Generado: <strong>$generatedDate</strong></p>
        </div>


        <div style="overflow-x:auto; width:100%; max-width:100%;">
        <table class="portfolio-pdf-table" style="min-width:700px;">
            <thead>
                <tr>
                    <th class="col-profesor">Profesor</th>
                    <th class="col-email">Email</th>
                    <th class="col-pao">PAO</th>
                    <th class="col-unit">U1</th>
                    <th class="col-unit">U2</th>
                    <th class="col-unit">U3</th>
                    <th class="col-unit">U4</th>
                    <th class="col-count">Aprob.</th>
                    <th class="col-count">Reprob.</th>
                    <th class="col-count">Pend.</th>
                </tr>
            </thead>
            <tbody>
HTML;

        if (empty($portfolios)) {
            $html .= '<tr><td colspan="10" style="text-align:center; padding:40px; color:#64748b; font-size:18px;">ℹ️ No hay portafolios registrados para el filtro seleccionado</td></tr>';
        } else {
            $emailCache = [];
            foreach ($portfolios as $portfolio) {
                $profesor = htmlspecialchars($portfolio['professor_name'] ?? '', ENT_QUOTES, 'UTF-8');
                $professorId = $portfolio['professor_id'] ?? null;
                $pao = htmlspecialchars($portfolio['pao_name'] ?? '', ENT_QUOTES, 'UTF-8');
                // Buscar email solo una vez por profesor
                if ($professorId && !isset($emailCache[$professorId])) {
                    $user = $userModel->find($professorId);
                    $emailCache[$professorId] = $user && isset($user['email']) ? htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') : '';
                }
                $email = $professorId ? $emailCache[$professorId] : '';
                $units = $portfolio['units'] ?? [];
                // Indexar unidades por número para acceso rápido
                $unitsByNumber = [];
                foreach ($units as $unit) {
                    $num = isset($unit['unit_number']) ? (int)$unit['unit_number'] : null;
                    if ($num !== null) {
                        $unitsByNumber[$num] = $unit;
                    }
                }
                // Para cada unidad, determinar estado
                $unitStates = [];
                $aprobadas = 0;
                $reprobadas = 0;
                $pendientes = 0;
                for ($i = 1; $i <= 4; $i++) {
                    if (isset($unitsByNumber[$i])) {
                        $approved = !empty($unitsByNumber[$i]['unit_approved']);
                        $failed = isset($unitsByNumber[$i]['unit_failed']) && $unitsByNumber[$i]['unit_failed'];
                        if ($approved) {
                            $unitStates[$i] = '<span style="color:#15803d;font-weight:600;">Aprobado</span>';
                            $aprobadas++;
                        } elseif ($failed) {
                            $unitStates[$i] = '<span style="color:#dc2626;font-weight:600;">Reprobado</span>';
                            $reprobadas++;
                        } else {
                            $unitStates[$i] = '<span style="color:#d97706;font-weight:600;">Pendiente</span>';
                            $pendientes++;
                        }
                    } else {
                        $unitStates[$i] = '<span style="color:#94a3b8;">Sin registro</span>';
                        $pendientes++;
                    }
                }
                $html .= "<tr>"
                    . "<td style=\"text-align:left;\">$profesor</td>"
                    . "<td style=\"text-align:left;font-size:9px;\">$email</td>"
                    . "<td>$pao</td>"
                    . "<td>" . $unitStates[1] . "</td>"
                    . "<td>" . $unitStates[2] . "</td>"
                    . "<td>" . $unitStates[3] . "</td>"
                    . "<td>" . $unitStates[4] . "</td>"
                    . "<td style=\"font-weight:700;color:#15803d;\">$aprobadas</td>"
                    . "<td style=\"font-weight:700;color:#dc2626;\">$reprobadas</td>"
                    . "<td style=\"font-weight:700;color:#d97706;\">$pendientes</td>"
                    . "</tr>";
            }
        }

        $html .= <<<HTML
            </tbody>
        </table>
        </div>
        <script>
        // Filtro por nombre para reporte de portafolios
        function applyPortfolioSearch() {
            const name = document.getElementById('search-portfolio-name').value;
            const url = new URL(window.location.href);
            if (name) {
                url.searchParams.set('name', name);
            } else {
                url.searchParams.delete('name');
            }
            window.location.href = url.toString();
        }
        function clearPortfolioFilters() {
            const url = new URL(window.location.href);
            url.searchParams.delete('name');
            window.location.href = url.pathname;
        }
        // Si hay valor en el parámetro name, rellenar el input
        window.addEventListener('DOMContentLoaded', function() {
            const url = new URL(window.location.href);
            const name = url.searchParams.get('name') || '';
            document.getElementById('search-portfolio-name').value = name;
        });
        </script>

        <div class="summary">
            <h3> Resumen</h3>
HTML;

        // Resumen por unidad: contar todas las unidades aprobadas, reprobadas y pendientes
        $totalUnits = 0;
        $approvedUnits = 0;
        $failedUnits = 0;
        $pendingUnits = 0;
        foreach ($portfolios as $portfolio) {
            $units = $portfolio['units'] ?? [];
            $unitsByNumber = [];
            for ($i = 1; $i <= 4; $i++) {
                $unitsByNumber[$i] = null;
            }
            foreach ($units as $unit) {
                $num = isset($unit['unit_number']) ? (int)$unit['unit_number'] : null;
                if ($num !== null && $num >= 1 && $num <= 4) {
                    $unitsByNumber[$num] = $unit;
                }
            }
            for ($i = 1; $i <= 4; $i++) {
                $totalUnits++;
                $unit = $unitsByNumber[$i];
                if ($unit && !empty($unit['unit_approved'])) {
                    $approvedUnits++;
                } elseif ($unit && isset($unit['unit_failed']) && $unit['unit_failed']) {
                    $failedUnits++;
                } else {
                    $pendingUnits++;
                }
            }
        }
        $total = count($portfolios);
        $html .= "<p><strong>Total de Portafolios:</strong> $total</p>";
        $html .= "<p><strong>Total de Unidades:</strong> $totalUnits</p>";
        $html .= "<p><strong>✓ Unidades Aprobadas:</strong> $approvedUnits</p>";
        $html .= "<p><strong>✗ Unidades Reprobadas:</strong> $failedUnits</p>";
        $html .= "<p><strong> Unidades Pendientes:</strong> $pendingUnits</p>";
        $html .= <<<HTML
        </div>
        
        <div class="action-buttons no-print">
            <a href="$basePath/reports" class="btn-action btn-secondary">
                <span>⬅</span> Volver a Reportes
            </a>
            <a href="$basePath/reports/cv-by-user?format=pdf" class="btn-action btn-primary">
                <span>📄</span> Reporte de Currículos
            </a>
            <a href="$basePath/reports/billing-by-user?format=pdf" class="btn-action btn-primary">
                <span>💰</span> Reporte de Facturación
            </a>
            <a href="$basePath/reports/teachers-by-dedication?format=pdf" class="btn-action btn-primary">
                <span>👨‍🏫</span> Docentes por Dedicación
            </a>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    private static function generateTeacherDedicationHtml($teachersByDedication, $searchName = null, $selectedDedication = null)
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        $downloadPdfUrl = self::getCurrentPdfDownloadUrl();
        $generatedDate = date('Y-m-d H:i:s');
        $logoSrc = self::getPublicImageSource('/img/logo_sgpro.jpg');
        $safeSearchName = htmlspecialchars((string) ($searchName ?? ''), ENT_QUOTES, 'UTF-8');
        $selectedDedication = strtoupper(trim((string) ($selectedDedication ?? '')));
        $dedicationOptions = ['TIEMPO COMPLETO', 'TIEMPO PARCIAL', 'MEDIO TIEMPO', 'OTROS'];
        $filterText = [];
        if ($safeSearchName !== '') {
            $filterText[] = 'Nombre: ' . $safeSearchName;
        }
        if ($selectedDedication !== '') {
            $filterText[] = 'Dedicación: ' . htmlspecialchars($selectedDedication, ENT_QUOTES, 'UTF-8');
        }
        $filterSummary = empty($filterText)
            ? 'Todos los registros'
            : implode(' | ', $filterText);
        $dedicationSelectOptions = '<option value="">Todas las dedicaciones</option>';
        foreach ($dedicationOptions as $option) {
            $selected = $selectedDedication === $option ? ' selected' : '';
            $safeOption = htmlspecialchars($option, ENT_QUOTES, 'UTF-8');
            $dedicationSelectOptions .= "<option value=\"{$safeOption}\"{$selected}>{$safeOption}</option>";
        }
        $css = file_exists(__DIR__ . '/../../public/css/reportes.css') ? file_get_contents(__DIR__ . '/../../public/css/reportes.css') : '';
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Docentes por Dedicación</title>
    <style>
$css
    </style>
</head>
<body>
    <div class="container">
        <div class="letterhead" style="display: flex; align-items: center; gap: 14px; padding: 10px 12px; border: 1px solid #3b82f6; border-radius: 6px; background: #e0e7ef; margin-bottom: 2px;">
            <img src="$logoSrc" alt="Logo institucional" class="letterhead-logo" style="width: 68px; height: 68px; object-fit: cover; border-radius: 8px; background: #f1f5f9; border: 2px solid #3b82f6; padding: 2px; box-shadow: 0 2px 8px rgba(59,130,246,0.10);">
            <div class="letterhead-text" style="margin: 0;">
                <h2 style="margin: 0; font-size: 18px; color: #2563eb; text-transform: uppercase; letter-spacing: 0.2px;">SISTEMA DE GESTION PROFESORAL - SGPRO</h2>
                <p style="margin: 2px 0 0; color: #334155; font-size: 12px;">Reporte institucional de docentes por dedicación</p>
            </div>
        </div>
        <div style="display: flex; gap: 12px; margin-bottom: 30px; align-items: center; flex-wrap: wrap; background: #e0e7ef; padding: 20px; border-radius: 12px; border: 2px solid #3b82f6;" class="no-print">
            <a class="print-button" href="$downloadPdfUrl" style="margin-bottom: 0; display: inline-flex; align-items: center; text-decoration: none;">
                <span>Descargar PDF</span>
            </a>
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="teacher-name-filter" style="font-weight: 600; color: #92400e;">Nombre:</label>
                <input type="text" id="teacher-name-filter" value="$safeSearchName" placeholder="Escribe el nombre..." style="padding: 10px 14px; border: 2px solid #2563eb; border-radius: 8px; font-size: 14px; width: 220px; transition: all 0.2s;">
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="teacher-dedication-filter" style="font-weight: 600; color: #92400e;">Dedicación:</label>
                <select id="teacher-dedication-filter" style="padding: 10px 14px; border: 2px solid #2563eb; border-radius: 8px; font-size: 14px; width: 200px; transition: all 0.2s;">
                    $dedicationSelectOptions
                </select>
            </div>
            <button onclick="applyTeacherSearch()" style="padding: 10px 18px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);">Buscar</button>
            <button onclick="clearTeacherFilters()" style="padding: 10px 18px; background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);">Limpiar</button>
        </div>
        <script>
        function applyTeacherSearch() {
            const name = document.getElementById('teacher-name-filter').value;
            const dedication = document.getElementById('teacher-dedication-filter').value;
            const url = new URL(window.location.href);
            if (name) {
                url.searchParams.set('name', name);
            } else {
                url.searchParams.delete('name');
            }
            if (dedication) {
                url.searchParams.set('dedication', dedication);
            } else {
                url.searchParams.delete('dedication');
            }
            window.location.href = url.toString();
        }
        function clearTeacherFilters() {
            const url = new URL(window.location.href);
            url.searchParams.delete('name');
            url.searchParams.delete('dedication');
            window.location.href = url.pathname;
        }
        // Rellenar los filtros si hay valores en la URL
        window.addEventListener('DOMContentLoaded', function() {
            const url = new URL(window.location.href);
            const name = url.searchParams.get('name') || '';
            const dedication = url.searchParams.get('dedication') || '';
            document.getElementById('teacher-name-filter').value = name;
            document.getElementById('teacher-dedication-filter').value = dedication;
        });
        </script>
        <div class="header">
            <h1>REPORTE DE DOCENTES POR DEDICACION</h1>
            <p>Generado: <strong>$generatedDate</strong> | <strong>$filterSummary</strong></p>
        </div>
HTML;

        $grandTotal = 0;

        foreach ($teachersByDedication as $dedication => $teachers) {
            $html .= <<<HTML
        <div class="dedication-section">
            <div class="dedication-header">
                <h2>$dedication</h2>
                <p>Total de Docentes: 
HTML;
            $html .= count($teachers);
            $html .= <<<HTML
                </p>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre del Docente</th>
                            <th>Email</th>
                            <th>PAO</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;

            foreach ($teachers as $teacher) {
                $name = htmlspecialchars($teacher['name'], ENT_QUOTES);
                $email = htmlspecialchars($teacher['email'], ENT_QUOTES);
                $pao = isset($teacher['pao']) ? htmlspecialchars($teacher['pao'], ENT_QUOTES) : '';
                $html .= "<tr>";
                $html .= "<td>$name</td>";
                $html .= "<td>$email</td>";
                $html .= "<td>$pao</td>";
                $html .= "</tr>";
                $grandTotal++;
            }

            $html .= <<<HTML
                    </tbody>
                </table>
            </div>
        </div>
HTML;
        }

        $html .= <<<HTML
        <div class="summary">
            <h3>Resumen General</h3>
            <p><strong>Total de Docentes Registrados:</strong> $grandTotal</p>
HTML;

        foreach ($teachersByDedication as $dedication => $teachers) {
            $count = count($teachers);
            $html .= "<p><strong>$dedication:</strong> $count docentes</p>";
        }

        $html .= <<<HTML
        </div>
        
        <div class="action-buttons no-print">
            <a href="$basePath/reports" class="btn-action btn-secondary">
                <span>⬅</span> Volver a Reportes
            </a>
            <a href="$basePath/reports/cv-by-user?format=pdf" class="btn-action btn-primary">
                <span>📄</span> Reporte de Currículos
            </a>
            <a href="$basePath/reports/billing-by-user?format=pdf" class="btn-action btn-primary">
                <span>💰</span> Reporte de Facturación
            </a>
            <a href="$basePath/reports/portfolios?format=pdf" class="btn-action btn-primary">
                <span>🎯</span> Reporte de Portafolios
            </a>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }
}


