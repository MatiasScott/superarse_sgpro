<?php
// app/controllers/ReportController.php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ContractModel.php';
require_once __DIR__ . '/../models/InvoiceModel.php';
require_once __DIR__ . '/../models/PortfolioModel.php';
require_once __DIR__ . '/../models/RoleModel.php';
require_once __DIR__ . '/../models/CvEducationModel.php';
require_once __DIR__ . '/../models/CvProfessionalExperienceModel.php';
require_once __DIR__ . '/../models/CvTrainingModel.php';
require_once __DIR__ . '/../models/ProfessorCvModel.php';
require_once __DIR__ . '/../models/CvTeachingExperienceModel.php';
require_once __DIR__ . '/../models/CvAcademicManagementExperienceModel.php';
require_once __DIR__ . '/../models/CvResearchProjectsModel.php';
require_once __DIR__ . '/../models/CvPublicationsModel.php';
require_once __DIR__ . '/../models/CvPresentationsModel.php';
require_once __DIR__ . '/../models/CvThesisDirectionModel.php';
require_once __DIR__ . '/../models/CvWorkReferencesModel.php';
require_once __DIR__ . '/../models/CvPersonalReferencesModel.php';
require_once __DIR__ . '/../models/CvOutreachProjectsModel.php';
require_once __DIR__ . '/../models/CvLanguageProficiencyModel.php';
require_once __DIR__ . '/../helpers/ExcelExportHelper.php';
require_once __DIR__ . '/../helpers/PdfExportHelper.php';

class ReportController
{
    private $userModel;
    private $contractModel;
    private $invoiceModel;
    private $portfolioModel;
    private $roleModel;
    private $cvEducationModel;
    private $cvProfessionalExperienceModel;
    private $cvTrainingModel;
    private $professorCvModel;
    private $cvTeachingExperienceModel;
    private $cvAcademicManagementExperienceModel;
    private $cvResearchProjectsModel;
    private $cvPublicationsModel;
    private $cvPresentationsModel;
    private $cvThesisDirectionModel;
    private $cvWorkReferencesModel;
    private $cvPersonalReferencesModel;
    private $cvOutreachProjectsModel;
    private $cvLanguageProficiencyModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->contractModel = new ContractModel();
        $this->invoiceModel = new InvoiceModel();
        $this->portfolioModel = new PortfolioModel();
        $this->roleModel = new RoleModel();
        $this->cvEducationModel = new CvEducationModel();
        $this->cvProfessionalExperienceModel = new CvProfessionalExperienceModel();
        $this->cvTrainingModel = new CvTrainingModel();
        $this->professorCvModel = new ProfessorCvModel();
        $this->cvTeachingExperienceModel = new CvTeachingExperienceModel();
        $this->cvAcademicManagementExperienceModel = new CvAcademicManagementExperienceModel();
        $this->cvResearchProjectsModel = new CvResearchProjectsModel();
        $this->cvPublicationsModel = new CvPublicationsModel();
        $this->cvPresentationsModel = new CvPresentationsModel();
        $this->cvThesisDirectionModel = new CvThesisDirectionModel();
        $this->cvWorkReferencesModel = new CvWorkReferencesModel();
        $this->cvPersonalReferencesModel = new CvPersonalReferencesModel();
        $this->cvOutreachProjectsModel = new CvOutreachProjectsModel();
        $this->cvLanguageProficiencyModel = new CvLanguageProficiencyModel();
    }

    /**
     * Muestra el índice de reportes disponibles
     */
    public function index()
    {
        if (!$this->isAuthorizedForReports()) {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Obtener roles del usuario actual para el sidebar
        $roles = $this->roleModel->getRolesByUserId($_SESSION['user_id']);
        
        $pageTitle = 'Reportes';
        require_once __DIR__ . '/../views/reports/index.php';
    }

    /**
     * Genera reporte de todos los CVs separado por usuario
     */
    public function reportCvsByUser()
    {
        if (!$this->isAuthorized(['Super Administrador', 'Director de docencia', 'Talento humano'])) {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Este reporte consolida únicamente CV de profesores activos.
        $users = $this->userModel->getUsersWithRole('Profesor');

        // Filtrar por nombre si se especifica en la URL.
        // Se mantiene compatibilidad con "search" y "name".
        $rawSearch = $_GET['search'] ?? ($_GET['name'] ?? '');
        $searchName = trim((string) $rawSearch) !== '' ? trim((string) $rawSearch) : null;
        $roleFilter = 'Profesor';

        // Agrupar datos por usuario - MOSTRANDO SOLO UN CURRÍCULUM POR USUARIO
        $cvData = [];
        foreach ($users as $user) {
            $userId = $user['id'];
            
            // Obtener datos personales del CV
            $professorCv = $this->professorCvModel->findByProfessorId($userId);
            
            // Obtener educación más reciente
            $allEducation = $this->cvEducationModel->getAllByProfessorId($userId);
            $education = !empty($allEducation) ? [$allEducation[0]] : []; // Solo el primero
            
            // Obtener experiencia profesional más reciente
            $allExperience = $this->cvProfessionalExperienceModel->getAllByProfessorId($userId);
            $experience = !empty($allExperience) ? [$allExperience[0]] : []; // Solo la primera
            
            // Obtener capacitación más reciente
            $allTraining = $this->cvTrainingModel->getAllByProfessorId($userId);
            $training = !empty($allTraining) ? [$allTraining[0]] : []; // Solo la primera
            
            // Obtener experiencia docente más reciente
            $allTeachingExperience = $this->cvTeachingExperienceModel->getAllByProfessorId($userId);
            $teachingExperience = !empty($allTeachingExperience) ? [$allTeachingExperience[0]] : [];
            
            // Obtener experiencia en gestión académica más reciente
            $allAcademicManagement = $this->cvAcademicManagementExperienceModel->getAllByProfessorId($userId);
            $academicManagement = !empty($allAcademicManagement) ? [$allAcademicManagement[0]] : [];
            
            // Obtener proyectos de investigación más reciente
            $allResearchProjects = $this->cvResearchProjectsModel->getAllByProfessorId($userId);
            $researchProjects = !empty($allResearchProjects) ? [$allResearchProjects[0]] : [];
            
            // Obtener publicaciones más reciente
            $allPublications = $this->cvPublicationsModel->getAllByProfessorId($userId);
            $publications = !empty($allPublications) ? [$allPublications[0]] : [];
            
            // Obtener ponencias más reciente
            $allPresentations = $this->cvPresentationsModel->getAllByProfessorId($userId);
            $presentations = !empty($allPresentations) ? [$allPresentations[0]] : [];
            
            // Obtener tesis dirigidas más reciente
            $allThesisDirection = $this->cvThesisDirectionModel->getAllByProfessorId($userId);
            $thesisDirection = !empty($allThesisDirection) ? [$allThesisDirection[0]] : [];
            
            // Obtener proyectos de vinculación más reciente
            $allOutreachProjects = $this->cvOutreachProjectsModel->getAllByProfessorId($userId);
            $outreachProjects = !empty($allOutreachProjects) ? [$allOutreachProjects[0]] : [];
            
            // Obtener referencias laborales (todas)
            $workReferences = $this->cvWorkReferencesModel->getAllByProfessorId($userId);
            
            // Obtener referencias personales (todas)
            $personalReferences = $this->cvPersonalReferencesModel->getAllByProfessorId($userId);
            
            // Obtener idiomas (todos)
            $languages = $this->cvLanguageProficiencyModel->getAllByProfessorId($userId);

            $cvDisplayName = trim((string) (($professorCv['surnames'] ?? '') . ' ' . ($professorCv['first_name'] ?? '')));
            $displayName = $cvDisplayName !== '' ? $cvDisplayName : ($user['name'] ?? '');
            $displayEmail = $professorCv['email'] ?? $user['email'] ?? '';

            if ($searchName) {
                $matchesUserName = stripos((string) ($user['name'] ?? ''), $searchName) !== false;
                $matchesCvName = stripos($cvDisplayName, $searchName) !== false;
                if (!$matchesUserName && !$matchesCvName) {
                    continue;
                }
            }
            
            $cvData[$userId] = [
                'name' => $displayName,
                'email' => $displayEmail,
                'phone' => $professorCv['phone'] ?? $user['phone'] ?? '',
                'cell_phone' => $professorCv['cell_phone'] ?? '',
                'cedula' => $professorCv['cedula_passport'] ?? '',
                'address' => $professorCv['address'] ?? '',
                'city' => $professorCv['city'] ?? '',
                'nationality' => $professorCv['nationality'] ?? '',
                'birth_date' => $professorCv['birth_date'] ?? '',
                'photo_path' => $professorCv['photo_path'] ?? '',
                'education' => $education,
                'professional_experience' => $experience,
                'training' => $training,
                'teaching_experience' => $teachingExperience,
                'academic_management' => $academicManagement,
                'research_projects' => $researchProjects,
                'publications' => $publications,
                'presentations' => $presentations,
                'thesis_direction' => $thesisDirection,
                'outreach_projects' => $outreachProjects,
                'work_references' => $workReferences,
                'personal_references' => $personalReferences,
                'languages' => $languages,
                'has_education' => !empty($allEducation),
                'has_experience' => !empty($allExperience),
                'has_training' => !empty($allTraining)
            ];
        }

        // Determinar formato (PDF o Excel)
        $format = isset($_GET['format']) ? $_GET['format'] : 'excel';

        if ($format === 'pdf') {
            $this->exportCvsToPdf($cvData, $roleFilter, $searchName);
        } else {
            $this->exportCvsToExcel($cvData);
        }
    }

    /**
     * Genera reporte de facturación por usuario
     */
    public function reportBillingByUser()
    {
        if (!$this->isAuthorized(['Super Administrador', 'Talento humano', 'Director de docencia'])) {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Capturar los parámetros de año y nombre si se proporcionan
        $year = isset($_GET['year']) && $_GET['year'] !== '' ? $_GET['year'] : null;
        $searchName = isset($_GET['name']) && $_GET['name'] !== '' ? strtolower(trim($_GET['name'])) : null;

        // Obtener todas las facturas con detalles, filtradas por año si se especifica
        $invoices = $this->invoiceModel->getInvoicesWithDetails(null, [], $year);

        // Agrupar por usuario
        $billingData = [];
        foreach ($invoices as $invoice) {
            $professorId = $invoice['professor_id'];
            if (!isset($billingData[$professorId])) {
                $billingData[$professorId] = [
                    'name' => $invoice['professor_name'] ?? 'Desconocido',
                    'email' => '',
                    'invoices' => [],
                    'total' => 0
                ];
                
                // Obtener email del usuario
                $user = $this->userModel->find($professorId);
                if ($user) {
                    $billingData[$professorId]['email'] = $user['email'] ?? '';
                }
            }
            $billingData[$professorId]['invoices'][] = $invoice;
            $billingData[$professorId]['total'] += $invoice['amount'] ?? 0;
        }

        // Filtrar por nombre si se especifica
        if ($searchName) {
            $billingData = array_filter($billingData, function ($userData) use ($searchName) {
                return stripos($userData['name'], $searchName) !== false;
            });
        }

        $format = isset($_GET['format']) ? $_GET['format'] : 'excel';

        if ($format === 'pdf') {
            $this->exportBillingToPdf($billingData, $year, $searchName);
        } else {
            $this->exportBillingToExcel($billingData);
        }
    }

    /**
     * Genera reporte de portafolios
     */
    public function reportPortfolios()
    {
        if (!$this->isAuthorized(['Super Administrador', 'Director de docencia', 'Coordinador académico'])) {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }


        // Obtener portafolios agrupados con unidades (estructura esperada por el PDF)
        // Se asume que el usuario actual es un administrador, por lo que puede ver todos
        $userId = $_SESSION['user_id'] ?? null;
        $userRoles = $this->roleModel->getRolesByUserId($userId);
        $portfolios = $this->portfolioModel->getPortfoliosWithDetails($userId, $userRoles);

        $format = isset($_GET['format']) ? $_GET['format'] : 'excel';

        if ($format === 'pdf') {
            $this->exportPortfoliosToPdf($portfolios);
        } else {
            $this->exportPortfoliosToExcel($portfolios);
        }
    }

    /**
     * Genera reporte de docentes por dedicación
     */
    public function reportTeachersByDedication()
    {
        if (!$this->isAuthorized(['Super Administrador', 'Talento humano', 'Director de docencia'])) {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $searchName = isset($_GET['name']) && $_GET['name'] !== '' ? trim($_GET['name']) : null;
        $selectedDedication = isset($_GET['dedicacion']) && $_GET['dedicacion'] !== ''
            ? $this->normalizeTeacherDedication($_GET['dedicacion'])
            : null;


        // Incluir docentes y cualquier usuario activo con dedicación explícita.

        $teachers = $this->getUsersForDedicationReport();
        $teachersByDedication = [
            'TIEMPO COMPLETO' => [],
            'TIEMPO PARCIAL' => [],
            'MEDIO TIEMPO' => [],
            'OTROS' => []
        ];

        // Necesitamos acceso a PortfolioModel y PaoModel
        require_once __DIR__ . '/../models/PaoModel.php';
        $paoModel = new \PaoModel();

        foreach ($teachers as $teacher) {
            $dedication = $this->normalizeTeacherDedication($teacher['dedicacion'] ?? null);
            $contract = null;

            if ($dedication === 'OTROS') {
                $contract = $this->contractModel->getActiveContractByProfessor($teacher['id']);
                if ($contract) {
                    $dedication = $this->normalizeTeacherDedication($this->extractDedicationFromContract($contract));
                }
            }

            if ($searchName !== null && stripos($teacher['name'] ?? '', $searchName) === false) {
                continue;
            }

            if ($selectedDedication !== null && $dedication !== $selectedDedication) {
                continue;
            }

            if (!isset($teachersByDedication[$dedication])) {
                $teachersByDedication[$dedication] = [];
            }

            // Buscar todos los portafolios del profesor para obtener los PAO
                $portfolios = $this->portfolioModel->getPortfoliosByProfessor($teacher['id']);
                $paoTitles = [];
                if (!empty($portfolios)) {
                    foreach ($portfolios as $portfolio) {
                        if (!empty($portfolio['pao_id'])) {
                            $pao = $paoModel->getById($portfolio['pao_id']);
                            if ($pao && !empty($pao['title'])) {
                                $paoTitles[] = $pao['title'];
                            }
                        }
                    }
                }
                $paoTitles = array_unique($paoTitles);
                $paoTitle = implode(', ', $paoTitles);

                $teachersByDedication[$dedication][] = [
                    'id' => $teacher['id'],
                    'name' => $teacher['name'],
                    'email' => $teacher['email'],
                    'dedication' => $dedication,
                    'contract_id' => $contract['id'] ?? null,
                    'pao' => $paoTitle,
                ];
        }

        if ($searchName !== null || $selectedDedication !== null) {
            $teachersByDedication = array_filter(
                $teachersByDedication,
                fn ($group) => !empty($group)
            );
        }

        $format = isset($_GET['format']) ? $_GET['format'] : 'excel';

        if ($format === 'pdf') {
            $this->exportTeachersByDedicationToPdf($teachersByDedication, $searchName, $selectedDedication);
        } else {
            $this->exportTeachersByDedicationToExcel($teachersByDedication);
        }
    }

    // =============== MÉTODOS DE EXPORTACIÓN A EXCEL ===============

    private function exportCvsToExcel($cvData)
    {
        $filename = 'Reporte_CVs_' . date('Y-m-d_His') . '.xlsx';
        ExcelExportHelper::createCvReport($cvData, $filename);
    }

    private function exportBillingToExcel($billingData)
    {
        $filename = 'Reporte_Facturacion_' . date('Y-m-d_His') . '.xlsx';
        ExcelExportHelper::createBillingReport($billingData, $filename);
    }

    private function exportPortfoliosToExcel($portfolios)
    {
        $filename = 'Reporte_Portafolios_' . date('Y-m-d_His') . '.xlsx';
        ExcelExportHelper::createPortfolioReport($portfolios, $filename);
    }

    private function exportTeachersByDedicationToExcel($teachersByDedication)
    {
        $filename = 'Reporte_Docentes_Dedicacion_' . date('Y-m-d_His') . '.xlsx';
        ExcelExportHelper::createTeacherDedicationReport($teachersByDedication, $filename);
    }

    // =============== MÉTODOS DE EXPORTACIÓN A PDF ===============

    private function exportCvsToPdf($cvData, $roleFilter = null, $searchName = null)
    {
        $filename = 'Reporte_CVs_' . date('Y-m-d_His') . '.pdf';
        PdfExportHelper::createCvReport($cvData, $filename, $roleFilter, $searchName);
    }

    private function exportBillingToPdf($billingData, $year = null, $searchName = null)
    {
        $filename = 'Reporte_Facturación_' . date('Y-m-d_His') . '.pdf';
        PdfExportHelper::createBillingReport($billingData, $filename, $year, $searchName);
    }

    private function exportPortfoliosToPdf($portfolios)
    {
        $filename = 'Reporte_Portafolios_' . date('Y-m-d_His') . '.pdf';
        PdfExportHelper::createPortfolioReport($portfolios, $filename);
    }

    private function exportTeachersByDedicationToPdf($teachersByDedication, $searchName = null, $selectedDedication = null)
    {
        $filename = 'Reporte_Docentes_Dedicación_' . date('Y-m-d_His') . '.pdf';
        PdfExportHelper::createTeacherDedicationReport($teachersByDedication, $filename, $searchName, $selectedDedication);
    }

    // =============== MÉTODOS AUXILIARES ===============

    /**
     * Extrae dedicación del contrato
     */
    private function extractDedicationFromContract($contract)
    {
        // Si existe un campo 'dedication' en el contrato
        if (!empty($contract['dedication'])) {
            return $contract['dedication'];
        }

        // Si no, intenta extraerlo del nombre del archivo
        if (!empty($contract['document_path'])) {
            if (stripos($contract['document_path'], 'TP') !== false) {
                return 'TIEMPO PARCIAL';
            } elseif (stripos($contract['document_path'], 'TC') !== false) {
                return 'TIEMPO COMPLETO';
            }
        }

        return 'OTROS';
    }

    /**
     * Normaliza la dedicación del docente para reportes.
     */
    private function normalizeTeacherDedication($dedication)
    {
        $value = strtoupper(trim((string) $dedication));

        if ($value === '') {
            return 'OTROS';
        }

        return match ($value) {
            'TIEMPO COMPLETO', 'TIEMPOCOMPLETEO', 'TIEMPO_COMPLETO' => 'TIEMPO COMPLETO',
            'TIEMPO PARCIAL', 'TIEMPO_PARCIAL' => 'TIEMPO PARCIAL',
            'MEDIO TIEMPO', 'MEDIO_TIEMPO' => 'MEDIO TIEMPO',
            'OCASIONAL', 'SIN DEDICACION ASIGNADA', 'SIN DEDICACIÓN ASIGNADA', 'SIN ESPECIFICAR' => 'OTROS',
            default => 'OTROS',
        };
    }

    /**
     * Obtiene usuarios para el reporte de dedicación.
     * Incluye profesores activos y usuarios activos con dedicación distinta de OTROS.
     */
    private function getUsersForDedicationReport()
    {
        $users = [];

        foreach ($this->userModel->getUsersWithRole('Profesor') as $teacher) {
            $users[$teacher['id']] = $teacher;
        }

        foreach ($this->userModel->getAll() as $user) {
            if ((int) ($user['active'] ?? 0) !== 1) {
                continue;
            }

            // Incluir todos los usuarios activos, sin importar su dedicación
            if (!isset($users[$user['id']])) {
                $users[$user['id']] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'dedicacion' => $user['dedicacion'],
                ];
            }
        }

        uasort($users, fn($left, $right) => strcmp($left['name'] ?? '', $right['name'] ?? ''));

        return array_values($users);
    }

    /**
     * Verifica si el usuario actual está autorizado para ver reportes
     */
    private function isAuthorizedForReports()
    {
        return $this->isAuthorized(['Super Administrador', 'Talento humano', 'Director de docencia', 'Coordinador académico']);
    }

    /**
     * Verifica si el usuario actual está autorizado
     */
    private function isAuthorized($roles)
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $userRoles = $this->getUserRoles($_SESSION['user_id']);
        foreach ($userRoles as $role) {
            if (in_array($role['role_name'], $roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtiene los roles del usuario actual
     */
    private function getUserRoles($userId)
    {
        $db = $this->userModel->getConnection();
        $stmt = $db->prepare("
            SELECT ur.* FROM user_roles ur
            JOIN user_roles_pivot urp ON ur.id = urp.role_id
            WHERE urp.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
