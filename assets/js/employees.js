// public/assets/js/employees.js

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('employee-form');
    if (!form) return; // Si no hay formulario en la página, no hacer nada

    form.addEventListener('submit', function (event) {
        // Prevenir el envío del formulario para realizar la validación
        event.preventDefault();

        // Limpiar errores previos
        clearAllErrors();

        let isValid = true;

        // --- VALIDACIONES ---

        // 1. Nombre Completo (requerido)
        const fullName = document.getElementById('full_name');
        if (fullName.value.trim() === '') {
            showError(fullName, 'El nombre completo es obligatorio.');
            isValid = false;
        }

        // 2. Número de Documento (requerido y numérico)
        const docNumber = document.getElementById('document_number');
        if (docNumber.value.trim() === '') {
            showError(docNumber, 'El número de documento es obligatorio.');
            isValid = false;
        } else if (!/^\d+$/.test(docNumber.value)) {
            showError(docNumber, 'El documento solo debe contener números.');
            isValid = false;
        }

        // 3. Fecha de Nacimiento (requerida y no futura)
        const birthDate = document.getElementById('birth_date');
        if (birthDate.value === '') {
            showError(birthDate, 'La fecha de nacimiento es obligatoria.');
            isValid = false;
        } else {
            const today = new Date();
            const selectedDate = new Date(birthDate.value);
            // Ajustar la hora para comparar solo fechas
            today.setHours(0, 0, 0, 0);
            if (selectedDate > today) {
                showError(birthDate, 'La fecha de nacimiento no puede ser una fecha futura.');
                isValid = false;
            }
        }
        
        // 4. Salario (debe ser un número positivo si se ingresa)
        const salary = document.getElementById('salary');
        if (salary.value.trim() !== '' && (isNaN(salary.value) || parseFloat(salary.value) < 0)) {
            showError(salary, 'El salario debe ser un número positivo.');
            isValid = false;
        }


        // --- FIN DE VALIDACIONES ---

        // Si todo es válido, enviar el formulario
        if (isValid) {
            form.submit();
        }
    });

    /**
     * Muestra un mensaje de error debajo de un campo del formulario.
     * @param {HTMLElement} inputElement - El campo de entrada que tiene el error.
     * @param {string} message - El mensaje de error a mostrar.
     */
    function showError(inputElement, message) {
        inputElement.classList.add('is-invalid'); // Añade el borde rojo de Bootstrap
        const errorContainer = document.createElement('div');
        errorContainer.className = 'error-message';
        errorContainer.textContent = message;
        // Insertar el mensaje después del campo
        inputElement.parentNode.appendChild(errorContainer);
    }

    /**
     * Limpia todos los mensajes de error y clases de invalidez del formulario.
     */
    function clearAllErrors() {
        // Quitar las clases 'is-invalid' de todos los campos
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        // Eliminar todos los mensajes de error
        form.querySelectorAll('.error-message').forEach(el => el.remove());
    }
});