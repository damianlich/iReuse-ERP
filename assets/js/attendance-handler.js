window.onload = function() {
    const statusDiv = document.getElementById('status');
    const fpImage = document.getElementById('fpImage');
    
    // 1. Inicializar el SDK de la API de huellas
    const fingerprintSdk = new Fingerprint.WebApi();

    // 2. Asignar manejadores para los eventos del SDK
    fingerprintSdk.on("DeviceConnected", function (e) {
        statusDiv.textContent = "Lector de huellas conectado.";
        startCapture();
    });

    fingerprintSdk.on("DeviceDisconnected", function (e) {
        statusDiv.textContent = "Lector de huellas desconectado. Por favor, conecte el dispositivo.";
    });

    fingerprintSdk.on("QualityReported", function (e) {
        // Muestra la calidad de la muestra de la huella (opcional)
        // console.log("Calidad:", Fingerprint.QualityCode[e.quality]);
    });

    fingerprintSdk.on("SamplesAcquired", function (s) {
        // ¡Este es el evento clave! Ocurre cuando se captura una huella exitosamente.
        statusDiv.textContent = "Huella capturada. Procesando...";
        
        // s.samples contiene los datos de la huella. Generalmente es un string en Base64.
        // El formato puede variar, pero usualmente se envía el primer resultado.
        const fingerprintData = s.samples[0];

        // Convertir la imagen PNG (si está disponible) para mostrarla
        const fingerprintImage = Fingerprint.b64UrlTo64(fingerprintData);
        fpImage.src = "data:image/png;base64," + fingerprintImage;

        // Ahora, enviamos la huella a nuestro backend PHP para identificarla
        sendFingerprintToServer(fingerprintData);
    });

    fingerprintSdk.on("ErrorOccurred", function (e) {
        statusDiv.textContent = "Ocurrió un error: " + e.error;
    });

    // 3. Función para iniciar la captura
    function startCapture() {
        statusDiv.textContent = "Esperando huella...";
        // El formato PngImage es útil para visualización, pero podrías necesitar otro formato
        // como Intermediate para que el backend lo procese. Revisa la doc del SDK.
        fingerprintSdk.startAcquisition(Fingerprint.SampleFormat.PngImage)
            .catch(error => {
                statusDiv.textContent = "Error al iniciar captura: " + error;
            });
    }

    // 4. Función para enviar los datos al servidor vía AJAX
    function sendFingerprintToServer(fingerprintData) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/api/endpoints/attendance/register.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onload = function() {
            if (xhr.status === 200 || xhr.status === 201) {
                const response = JSON.parse(xhr.responseText);
                statusDiv.textContent = response.message;
            } else {
                const errorResponse = JSON.parse(xhr.responseText);
                statusDiv.textContent = "Error del servidor: " + (errorResponse.message || xhr.statusText);
            }
            // Después de un intento, reinicia la captura para el siguiente empleado
            setTimeout(startCapture, 2000); // Espera 2 segundos antes de volver a activar
        };

        xhr.onerror = function() {
            statusDiv.textContent = "Error de conexión con el servidor.";
            setTimeout(startCapture, 2000);
        };

        const data = JSON.stringify({
            fingerprint: fingerprintData
        });
        
        xhr.send(data);
    }

    // Iniciar el listado de dispositivos para que se conecte
    fingerprintSdk.enumerateDevices();
};