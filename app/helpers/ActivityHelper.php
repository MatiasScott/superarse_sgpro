<?php
// app/helpers/ActivityHelper.php

require_once __DIR__ . '/../models/ActivityLogModel.php';

class ActivityHelper
{
    private static $activityLogModel;
    
    private static function getModel()
    {
        if (self::$activityLogModel === null) {
            self::$activityLogModel = new ActivityLogModel();
        }
        return self::$activityLogModel;
    }
    
    public static function logActivity($action, $tableName, $recordId, $description = null)
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $userId = $_SESSION['user_id'];
        $model = self::getModel();
        
        return $model->createActivity($userId, $action, $tableName, $recordId, $description);
    }
    
    public static function logCreate($tableName, $recordId, $description = null)
    {
        return self::logActivity('CREATE', $tableName, $recordId, $description);
    }
    
    public static function logUpdate($tableName, $recordId, $description = null)
    {
        return self::logActivity('UPDATE', $tableName, $recordId, $description);
    }
    
    public static function logDelete($tableName, $recordId, $description = null)
    {
        return self::logActivity('DELETE', $tableName, $recordId, $description);
    }
    
    // Funciones específicas para cada tabla
    public static function logUserCreate($userId, $userName)
    {
        return self::logCreate('users', $userId, "Usuario: {$userName}");
    }
    
    public static function logUserUpdate($userId, $userName)
    {
        return self::logUpdate('users', $userId, "Usuario: {$userName}");
    }
    
    public static function logUserDelete($userId, $userName)
    {
        return self::logDelete('users', $userId, "Usuario: {$userName}");
    }
    
    public static function logPaoCreate($paoId, $paoName)
    {
        return self::logCreate('paos', $paoId, "PAO: {$paoName}");
    }
    
    public static function logPaoUpdate($paoId, $paoName)
    {
        return self::logUpdate('paos', $paoId, "PAO: {$paoName}");
    }
    
    public static function logPaoDelete($paoId, $paoName)
    {
        return self::logDelete('paos', $paoId, "PAO: {$paoName}");
    }
    
    public static function logSubjectCreate($subjectId, $subjectName)
    {
        return self::logCreate('subjects', $subjectId, "Materia: {$subjectName}");
    }
    
    public static function logSubjectUpdate($subjectId, $subjectName)
    {
        return self::logUpdate('subjects', $subjectId, "Materia: {$subjectName}");
    }
    
    public static function logSubjectDelete($subjectId, $subjectName)
    {
        return self::logDelete('subjects', $subjectId, "Materia: {$subjectName}");
    }
    
    public static function logAssignmentCreate($assignmentId, $professorName, $subjectName)
    {
        return self::logCreate('assignments', $assignmentId, "Asignación: {$professorName} - {$subjectName}");
    }
    
    public static function logAssignmentUpdate($assignmentId, $professorName, $subjectName)
    {
        return self::logUpdate('assignments', $assignmentId, "Asignación: {$professorName} - {$subjectName}");
    }
    
    public static function logAssignmentDelete($assignmentId, $professorName, $subjectName)
    {
        return self::logDelete('assignments', $assignmentId, "Asignación: {$professorName} - {$subjectName}");
    }
    
    public static function logContractCreate($contractId, $professorName)
    {
        return self::logCreate('contracts', $contractId, "Contrato: {$professorName}");
    }
    
    public static function logContractUpdate($contractId, $professorName)
    {
        return self::logUpdate('contracts', $contractId, "Contrato: {$professorName}");
    }
    
    public static function logContractDelete($contractId, $professorName)
    {
        return self::logDelete('contracts', $contractId, "Contrato: {$professorName}");
    }
    
    public static function logInvoiceCreate($invoiceId, $professorName, $amount)
    {
        return self::logCreate('invoices', $invoiceId, "Factura: {$professorName} - $" . number_format($amount, 2));
    }
    
    public static function logInvoiceUpdate($invoiceId, $professorName, $amount)
    {
        return self::logUpdate('invoices', $invoiceId, "Factura: {$professorName} - $" . number_format($amount, 2));
    }
    
    public static function logInvoiceDelete($invoiceId, $professorName, $amount)
    {
        return self::logDelete('invoices', $invoiceId, "Factura: {$professorName} - $" . number_format($amount, 2));
    }
    
    public static function logEvaluationCreate($evaluationId, $professorName)
    {
        return self::logCreate('evaluations', $evaluationId, "Evaluación: {$professorName}");
    }
    
    public static function logEvaluationUpdate($evaluationId, $professorName)
    {
        return self::logUpdate('evaluations', $evaluationId, "Evaluación: {$professorName}");
    }
    
    public static function logEvaluationDelete($evaluationId, $professorName)
    {
        return self::logDelete('evaluations', $evaluationId, "Evaluación: {$professorName}");
    }
    
    public static function logPortfolioCreate($portfolioId, $professorName)
    {
        return self::logCreate('portfolios', $portfolioId, "Portafolio: {$professorName}");
    }
    
    public static function logPortfolioUpdate($portfolioId, $professorName)
    {
        return self::logUpdate('portfolios', $portfolioId, "Portafolio: {$professorName}");
    }
    
    public static function logPortfolioDelete($portfolioId, $professorName)
    {
        return self::logDelete('portfolios', $portfolioId, "Portafolio: {$professorName}");
    }
    
    public static function logContinuityCreate($continuityId, $professorName)
    {
        return self::logCreate('continuity', $continuityId, "Continuidad: {$professorName}");
    }
    
    public static function logContinuityUpdate($continuityId, $professorName)
    {
        return self::logUpdate('continuity', $continuityId, "Continuidad: {$professorName}");
    }
    
    public static function logContinuityDelete($continuityId, $professorName)
    {
        return self::logDelete('continuity', $continuityId, "Continuidad: {$professorName}");
    }
}