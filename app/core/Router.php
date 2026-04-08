<?php
// app/core/Router.php

class Router
{
    private $routes = [];

    public function __construct()
    {
        // Rutas de autenticación
        $this->addRoute('/', 'Auth@showLogin');
        $this->addRoute('/login', 'Auth@login');
        $this->addRoute('/register', 'Auth@showRegister');
        $this->addRoute('/register/store', 'Auth@register');
        $this->addRoute('/logout', 'Auth@logout');

        // Rutas del Dashboard
        $this->addRoute('/dashboard', 'Dashboard@index');
        $this->addRoute('/dashboard/mark-notification-read/{id}', 'Dashboard@markNotificationAsRead');
        $this->addRoute('/dashboard/mark-all-notifications-read', 'Dashboard@markAllNotificationsAsRead');

        // Rutas de Notificaciones
        $this->addRoute('/notifications', 'Notification@index');
        $this->addRoute('/notifications/mark-read/{id}', 'Notification@markAsRead');
        $this->addRoute('/notifications/mark-all-read', 'Notification@markAllAsRead');
        $this->addRoute('/notifications/unread-count', 'Notification@getUnreadCount');
        $this->addRoute('/notifications/recent', 'Notification@getRecent');
        $this->addRoute('/notifications/{id}', 'Notification@show');

        // Rutas de gestión de PAO
        $this->addRoute('/pao', 'Pao@index');
        $this->addRoute('/pao/create', 'Pao@create');
        $this->addRoute('/pao/store', 'Pao@store');
        $this->addRoute('/pao/edit/{id}', 'Pao@edit');
        $this->addRoute('/pao/update/{id}', 'Pao@update');
        $this->addRoute('/pao/delete/{id}', 'Pao@delete');

        // Rutas de gestión de la Hoja de Vida del Profesor
        $this->addRoute('/professor/cv', 'ProfessorCv@index');
        $this->addRoute('/professor/cv/edit', 'ProfessorCv@edit');
        $this->addRoute('/professor/cv/update/{table}/{id}', 'ProfessorCv@update');
        $this->addRoute('/professor/cv/update/{table}', 'ProfessorCv@update');
        $this->addRoute('/professor/cv/create', 'ProfessorCv@create');
        $this->addRoute('/professor/cv/store', 'ProfessorCv@store');

        // Rutas de gestión de Educación y Formación
        $this->addRoute('/professor/education', 'CvEducation@index');
        $this->addRoute('/professor/education/store', 'CvEducation@store');
        $this->addRoute('/professor/education/delete/{id}', 'CvEducation@delete');
        $this->addRoute('/professor/education/edit/{id}', 'CvEducation@edit');
        $this->addRoute('/professor/education/update/{id}', 'CvEducation@update');
        $this->addRoute('/professor/education/create', 'CvEducation@create');

        // Rutas de gestión de Experiencia en Gestión Académica
        $this->addRoute('/professor/academic-management', 'CvAcademicManagementExperience@index');
        $this->addRoute('/professor/academic-management/store', 'CvAcademicManagementExperience@store');
        $this->addRoute('/professor/academic-management/delete/{id}', 'CvAcademicManagementExperience@delete');
        $this->addRoute('/professor/academic-management/edit/{id}', 'CvAcademicManagementExperience@edit');
        $this->addRoute('/professor/academic-management/update/{id}', 'CvAcademicManagementExperience@update');
        $this->addRoute('/professor/academic-management/create', 'CvAcademicManagementExperience@create');

        // Rutas de gestión de Experiencia Docente
        $this->addRoute('/professor/teaching-experience', 'CvTeachingExperience@index');
        $this->addRoute('/professor/teaching-experience/store', 'CvTeachingExperience@store');
        $this->addRoute('/professor/teaching-experience/delete/{id}', 'CvTeachingExperience@delete');
        $this->addRoute('/professor/teaching-experience/edit/{id}', 'CvTeachingExperience@edit');
        $this->addRoute('/professor/teaching-experience/update/{id}', 'CvTeachingExperience@update');
        $this->addRoute('/professor/teaching-experience/create', 'CvTeachingExperience@create');

        // Rutas de gestión de Experiencia Profesional
        $this->addRoute('/professor/professional-experience', 'CvProfessionalExperience @index');
        $this->addRoute('/professor/professional-experience/store', 'CvProfessionalExperience@store');
        $this->addRoute('/professor/professional-experience/delete/{id}', 'CvProfessionalExperience@delete');
        $this->addRoute('/professor/professional-experience/edit/{id}', 'CvProfessionalExperience@edit');
        $this->addRoute('/professor/professional-experience/update/{id}', 'CvProfessionalExperience@update');
        $this->addRoute('/professor/professional-experience/create', 'CvProfessionalExperience@create');
        
        // Rutas de gestión de Proyectos de Investigación
        $this->addRoute('/professor/research-projects', 'CvResearchProjects@index');
        $this->addRoute('/professor/research-projects/store', 'CvResearchProjects@store');
        $this->addRoute('/professor/research-projects/delete/{id}', 'CvResearchProjects@delete');
        $this->addRoute('/professor/research-projects/edit/{id}', 'CvResearchProjects@edit');
        $this->addRoute('/professor/research-projects/update/{id}', 'CvResearchProjects@update');
        $this->addRoute('/professor/research-projects/create', 'CvResearchProjects@create');

        // Rutas de gestión de Ponencias
        $this->addRoute('/professor/presentations', 'CvPresentations@index');
        $this->addRoute('/professor/presentations/store', 'CvPresentations@store');
        $this->addRoute('/professor/presentations/delete/{id}', 'CvPresentations@delete');
        $this->addRoute('/professor/presentations/edit/{id}', 'CvPresentations@edit');
        $this->addRoute('/professor/presentations/update/{id}', 'CvPresentations@update');
        $this->addRoute('/professor/presentations/create', 'CvPresentations@create');

        // Rutas de gestión de Publicaciones
        $this->addRoute('/professor/publications', 'CvPublications@index');
        $this->addRoute('/professor/publications/store', 'CvPublications@store');
        $this->addRoute('/professor/publications/delete/{id}', 'CvPublications@delete');
        $this->addRoute('/professor/publications/edit/{id}', 'CvPublications@edit');
        $this->addRoute('/professor/publications/update/{id}', 'CvPublications@update');
        $this->addRoute('/professor/publications/create', 'CvPublications@create');

        // Rutas de gestión de Proyectos de Vinculación
        $this->addRoute('/professor/outreach-projects', 'CvOutreachProjects@index');
        $this->addRoute('/professor/outreach-projects/store', 'CvOutreachProjects@store');
        $this->addRoute('/professor/outreach-projects/delete/{id}', 'CvOutreachProjects@delete');
        $this->addRoute('/professor/outreach-projects/edit/{id}', 'CvOutreachProjects@edit');
        $this->addRoute('/professor/outreach-projects/update/{id}', 'CvOutreachProjects@update');
        $this->addRoute('/professor/outreach-projects/create', 'CvOutreachProjects@create');

        // Rutas de gestión de Dirección de Tesis
        $this->addRoute('/professor/thesis-direction', 'CvThesisDirection@index');
        $this->addRoute('/professor/thesis-direction/store', 'CvThesisDirection@store');
        $this->addRoute('/professor/thesis-direction/delete/{id}', 'CvThesisDirection@delete');
        $this->addRoute('/professor/thesis-direction/edit/{id}', 'CvThesisDirection@edit');
        $this->addRoute('/professor/thesis-direction/update/{id}', 'CvThesisDirection@update');
        $this->addRoute('/professor/thesis-direction/create', 'CvThesisDirection@create');

        // Rutas de gestión de Referencias Laborales
        $this->addRoute('/professor/work-references', 'CvWorkReferences@index');
        $this->addRoute('/professor/work-references/store', 'CvWorkReferences@store');
        $this->addRoute('/professor/work-references/delete/{id}', 'CvWorkReferences@delete');
        $this->addRoute('/professor/work-references/edit/{id}', 'CvWorkReferences@edit');
        $this->addRoute('/professor/work-references/update/{id}', 'CvWorkReferences@update');
        $this->addRoute('/professor/work-references/create', 'CvWorkReferences@create');

        // Rutas de gestión de Referencias Personales
        $this->addRoute('/professor/personal-references', 'CvPersonalReferences@index');
        $this->addRoute('/professor/personal-references/store', 'CvPersonalReferences@store');
        $this->addRoute('/professor/personal-references/delete/{id}', 'CvPersonalReferences@delete');
        $this->addRoute('/professor/personal-references/edit/{id}', 'CvPersonalReferences@edit');
        $this->addRoute('/professor/personal-references/update/{id}', 'CvPersonalReferences@update');
        $this->addRoute('/professor/personal-references/create', 'CvPersonalReferences@create');

        // Rutas de gestión de usuarios (Super Administrador)
        $this->addRoute('/users', 'User@index');
        $this->addRoute('/users/create', 'User@create');
        $this->addRoute('/users/store', 'User@store');
        $this->addRoute('/users/edit/{id}', 'User@edit');
        $this->addRoute('/users/update/{id}', 'User@update');
        $this->addRoute('/users/delete/{id}', 'User@delete');

        // Rutas de gestión de Portafolios
        $this->addRoute('/portfolios', 'Portfolio@index');
        $this->addRoute('/portfolios/create', 'Portfolio@create');
        $this->addRoute('/portfolios/store', 'Portfolio@store');
        $this->addRoute('/portfolios/edit/{id}', 'Portfolio@edit');
        $this->addRoute('/portfolios/update/{id}', 'Portfolio@update');
        $this->addRoute('/portfolios/update-type', 'Portfolio@updateType');
        $this->addRoute('/portfolios/delete/{id}', 'Portfolio@delete');
        $this->addRoute('/professor/portfolio/{professorId}/{paoId}', 'Portfolio@viewByProfessorPao');

        // Rutas de gestión de Evaluaciones
        $this->addRoute('/evaluations', 'Evaluation@index');
        $this->addRoute('/evaluations/create', 'Evaluation@create');
        $this->addRoute('/evaluations/store', 'Evaluation@store');
        $this->addRoute('/evaluations/edit/{id}', 'Evaluation@edit');
        $this->addRoute('/evaluations/update/{id}', 'Evaluation@update');
        $this->addRoute('/evaluations/delete/{id}', 'Evaluation@delete');

        // Rutas de gestión de Continuidad
        $this->addRoute('/continuity', 'Continuity@index');
        $this->addRoute('/continuity/create', 'Continuity@create');
        $this->addRoute('/continuity/store', 'Continuity@store');
        $this->addRoute('/continuity/edit/{id}', 'Continuity@edit');
        $this->addRoute('/continuity/update/{id}', 'Continuity@update');
        $this->addRoute('/continuity/delete/{id}', 'Continuity@delete');

        // Rutas de gestión de Contratos
        $this->addRoute('/contracts', 'Contract@index');
        $this->addRoute('/contracts/create', 'Contract@create');
        $this->addRoute('/contracts/store', 'Contract@store');
        $this->addRoute('/contracts/edit/{id}', 'Contract@edit');
        $this->addRoute('/contracts/update/{id}', 'Contract@update');
        $this->addRoute('/contracts/delete/{id}', 'Contract@delete');

        // Rutas de gestión de Facturas
        $this->addRoute('/invoices', 'Invoice@index');
        $this->addRoute('/invoices/create', 'Invoice@create');
        $this->addRoute('/invoices/store', 'Invoice@store');
        $this->addRoute('/invoices/edit/{id}', 'Invoice@edit');
        $this->addRoute('/invoices/update/{id}', 'Invoice@update');
        $this->addRoute('/invoices/delete/{id}', 'Invoice@delete');

        // Rutas de gestión de Asignaciones
        $this->addRoute('/academic/assignments', 'Assignment@index');
        $this->addRoute('/academic/assignments/create', 'Assignment@create');
        $this->addRoute('/academic/assignments/store', 'Assignment@store');
        $this->addRoute('/academic/assignments/edit/{id}', 'Assignment@edit');
        $this->addRoute('/academic/assignments/update/{id}', 'Assignment@update');
        $this->addRoute('/academic/assignments/delete/{id}', 'Assignment@delete');

        // Rutas de gestión de Carreras
        $this->addRoute('/academic/careers', 'Career@index');
        $this->addRoute('/academic/careers/store', 'Career@store');
        $this->addRoute('/academic/careers/edit/{id}', 'Career@edit');
        $this->addRoute('/academic/careers/update/{id}', 'Career@update');
        $this->addRoute('/academic/careers/quick-store', 'Career@quickStore');

        // Rutas de gestión de Asignaturas
        $this->addRoute('/academic/subjects', 'Subject@index');
        $this->addRoute('/academic/subjects/create', 'Subject@create');
        $this->addRoute('/academic/subjects/store', 'Subject@store');
        $this->addRoute('/academic/subjects/edit/{id}', 'Subject@edit');
        $this->addRoute('/academic/subjects/update/{id}', 'Subject@update');
        $this->addRoute('/academic/subjects/delete/{id}', 'Subject@delete');

        // Rutas de Reportes
        $this->addRoute('/reports', 'Report@index');
        $this->addRoute('/reports/cv-by-user', 'Report@reportCvsByUser');
        $this->addRoute('/reports/billing-by-user', 'Report@reportBillingByUser');
        $this->addRoute('/reports/portfolios', 'Report@reportPortfolios');
        $this->addRoute('/reports/teachers-by-dedication', 'Report@reportTeachersByDedication');
    }


    public function addRoute($url, $controllerMethod)
    {
        $this->routes[$url] = $controllerMethod;
    }

    public function dispatch()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $basePath = BASE_PATH;

        if (strpos($requestUri, $basePath) === 0) {
            $url = substr($requestUri, strlen($basePath));
        } else {
            $url = $requestUri;
        }

        $url = trim(parse_url($url, PHP_URL_PATH), '/');
        $url = empty($url) ? '/' : '/' . $url;

        $found = false;
        $params = [];

        foreach ($this->routes as $routeUrl => $controllerMethod) {
            // Reemplazar {id} por un patrón de regex
            $pattern = preg_replace('/{[a-zA-Z0-9]+}/', '([a-zA-Z0-9]+)', $routeUrl);
            $pattern = str_replace('/', '\/', $pattern);

            if (preg_match("/^$pattern$/", $url, $matches)) {
                array_shift($matches); // Eliminar el primer elemento (la URL completa)
                $params = $matches;
                list($controller, $method) = explode('@', $controllerMethod);
                $found = true;
                break;
            }
        }

        if ($found) {
            $controllerFile = __DIR__ . '/../controllers/' . $controller . 'Controller.php';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerClass = $controller . 'Controller';
                if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                    $instance = new $controllerClass();
                    call_user_func_array([$instance, $method], $params);
                } else {
                    $this->handleNotFound();
                }
            } else {
                $this->handleNotFound();
            }
        } else {
            $this->handleNotFound();
        }
    }

    private function handleNotFound()
    {
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>La página que buscas no existe.</p>";
    }
}