"use strict";

// --- SELECCIÓN DE ELEMENTOS DEL DOM ---
const startBtn = document.getElementById("start");
const stopBtn = document.getElementById("stop");
const huellaImg = document.getElementById("huella");
const statusMessage = document.getElementById("status-message");
const employeePhoto = document.getElementById("employee-photo");
const employeeName = document.getElementById("employee-name");
const registrationDetails = document.getElementById("registration-details");
const currentTimeEl = document.getElementById("current-time");

// Inicializar la API del lector
const fingerprint = new Fingerprint.WebApi();

// --- LÓGICA DE LA APLICACIÓN ---

/**
 * Reinicia la interfaz a su estado inicial.
 */
function resetUI() {
    statusMessage.textContent = "Por favor, coloque su dedo en el lector...";
    statusMessage.className = "";
    huellaImg.src = "assets/img/fingerprint-scan.gif";
    employeePhoto.src = "assets/img/default-user.png";
    employeeName.textContent = "-- Nombre del Empleado --";
    registrationDetails.textContent = "Esperando registro...";
}

/**
 * Inicia el proceso de captura de la huella.
 */
async function startCapture() {
    resetUI();
    try {
        await fingerprint.startAcquisition(Fingerprint.SampleFormat.PngImage);
    } catch (err) {
        updateStatus("Error al iniciar captura: " + err.message, "error");
    }
}

/**
 * Detiene el proceso de captura.
 */
async function stopCapture() {
    try {
        await fingerprint.stopAcquisition();
        updateStatus("Captura detenida por el usuario.", "info");
    } catch (err) {
        updateStatus("Error al detener captura: " + err.message, "error");
    }
}

/**
 * Actualiza el mensaje de estado y su color.
 * @param {string} message - El mensaje a mostrar.
 * @param {'success'|'error'|'info'} type - El tipo de mensaje para el estilo CSS.
 */
function updateStatus(message, type = "info") {
    statusMessage.textContent = message;
    statusMessage.className = type;
}

/**
 * Envía la huella capturada al servidor para su procesamiento.
 * @param {string} fingerprintData - La muestra de la huella en formato Base64.
 */
async function sendFingerprintToServer(fingerprintData) {
    updateStatus("Huella capturada, identificando...", "info");

    try {
        const response = await fetch('/api/endpoints/attendance/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ fingerprint: fingerprintData })
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || "Empleado no reconocido.");
        }

        // --- ÉXITO: Actualizar la interfaz con los datos del servidor ---
        updateStatus(result.message, "success");
        employeeName.textContent = result.employee.name;
        // Asumiendo que la URL de la foto viene del servidor
        employeePhoto.src = result.employee.photo_url || "assets/img/default-user.png";
        
        const regType = result.details.action === 'clock_in' ? 'Entrada' : 'Salida';
        const regTime = new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
        registrationDetails.textContent = `Registro de ${regType} a las ${regTime}`;

    } catch (error) {
        updateStatus(error.message, "error");
    } finally {
        // Después de 3 segundos, se prepara para el siguiente registro
        setTimeout(startCapture, 3000);
    }
}


// --- CONFIGURACIÓN DE EVENT HANDLERS DEL SDK ---

fingerprint.on("DeviceConnected", () => {
    updateStatus("Dispositivo conectado.", "success");
    startCapture(); // Iniciar automáticamente al conectar
});

fingerprint.on("DeviceDisconnected", () => {
    updateStatus("Dispositivo desconectado. Por favor, revise la conexión.", "error");
});

fingerprint.on("SamplesAcquired", (event) => {
    // La muestra es una cadena de texto en base64. El SDK la devuelve en un array.
    if (event.samples && event.samples.length > 0) {
        const sample = event.samples[0]; // Usamos la primera muestra
        huellaImg.src = "data:image/png;base64," + sample;
        sendFingerprintToServer(sample);
    }
});

fingerprint.on("ErrorOccurred", (error) => {
    updateStatus("Error del lector: " + error.message, "error");
});


// --- INICIALIZACIÓN ---

// Asignar eventos a los botones
startBtn.addEventListener("click", startCapture);
stopBtn.addEventListener("click", stopCapture);

// Actualizar el reloj cada segundo
setInterval(() => {
    const now = new Date();
    currentTimeEl.textContent = now.toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' - ' + now.toLocaleTimeString('es-CO');
}, 1000);

// Iniciar la comunicación con el SDK
fingerprint.enumerateDevices();