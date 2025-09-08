<?php

require_once '../../app/models/Attachment.php';
require_once '../../app/middleware/AuthMiddleware.php';

class AttachmentController {
    
    public function upload() {
        // 1. Autenticación y Control de Permisos
        $authMiddleware = new AuthMiddleware();
        $currentUser = $authMiddleware->getCurrentUser();
        
        $allowed_roles = ['super_admin', 'manager'];
        if (!in_array($currentUser['role'], $allowed_roles)) {
            // Si el rol no está permitido, redirigir con un mensaje de error.
            header('Location: index.php?action=employees_index&error=permission_denied');
            exit();
        }

        // 2. Validación de Datos de Entrada (POST y FILES)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['document'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '&error=invalid_request');
            exit();
        }

        $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $attachment_type = filter_input(INPUT_POST, 'attachment_type', FILTER_SANITIZE_STRING);
        $file = $_FILES['document'];

        // Verificar que el archivo no tenga errores de subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '&error=upload_failed');
            exit();
        }

        // 3. Validación de Seguridad del Archivo
        $max_file_size = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] > $max_file_size) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '&error=file_too_large');
            exit();
        }

        $allowed_mime_types = [
            'application/pdf' => '.pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
            'application/msword' => '.doc',
            'image/jpeg' => '.jpg',
            'image/png' => '.png'
        ];
        
        $file_mime_type = mime_content_type($file['tmp_name']);
        if (!array_key_exists($file_mime_type, $allowed_mime_types)) {
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '&error=invalid_file_type');
            exit();
        }

        // 4. Lógica de Almacenamiento Seguro
        $upload_dir = '../../public/assets/uploads/documents/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $original_filename = basename($file['name']);
        $extension = $allowed_mime_types[$file_mime_type];
        // Generar un nombre único para evitar colisiones y ofuscar el nombre original
        $new_filename = uniqid('doc_' . $employee_id . '_', true) . $extension;
        $destination_path = $upload_dir . $new_filename;
        $public_path = 'assets/uploads/documents/' . $new_filename; // Ruta relativa para la BD

        if (move_uploaded_file($file['tmp_name'], $destination_path)) {
            // 5. Guardar en la Base de Datos
            $attachmentModel = new Attachment();
            if ($attachmentModel->create($employee_id, $original_filename, $public_path, $attachment_type)) {
                // Éxito: redirigir a la ficha del empleado
                header('Location: index.php?action=employees_show&id=' . $employee_id . '&success=upload_complete');
            } else {
                // Error de base de datos, opcionalmente eliminar el archivo subido
                unlink($destination_path);
                header('Location: index.php?action=employees_show&id=' . $employee_id . '&error=db_error');
            }
        } else {
            // Error al mover el archivo
            header('Location: index.php?action=employees_show&id=' . $employee_id . '&error=move_failed');
        }
        exit();
    }
}