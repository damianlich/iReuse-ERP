// Declara las variables globales de los SDK para que TypeScript las reconozca.
declare var Fingerprint: any;
declare var WebSdk: any;

class AttendanceApp {
    private statusDiv: HTMLElement;
    private fpImage: HTMLImageElement;
    private fingerprintSdk: any;

    constructor() {
        this.statusDiv = document.getElementById('status') as HTMLElement;
        this.fpImage = document.getElementById('fpImage') as HTMLImageElement;

        // 1. Inicializar el SDK de la API de huellas
        this.fingerprintSdk = new Fingerprint.WebApi();
        this.setupEventHandlers();
        
        // Iniciar la conexión con el lector
        this.fingerprintSdk.enumerateDevices();
    }

    // 2. Centralizar la configuración de los manejadores de eventos
    private setupEventHandlers(): void {
        this.fingerprintSdk.on("DeviceConnected", this.onDeviceConnected.bind(this));
        this.fingerprintSdk.on("DeviceDisconnected", this.onDeviceDisconnected.bind(this));
        this.fingerprintSdk.on("SamplesAcquired", this.onSamplesAcquired.bind(this));
        this.fingerprintSdk.on("ErrorOccurred", this.onErrorOccurred.bind(this));
    }

    private onDeviceConnected(e: any): void {
        this.statusDiv.textContent = "Lector de huellas conectado.";
        this.startCapture();
    }

    private onDeviceDisconnected(e: any): void {
        this.statusDiv.textContent = "Lector de huellas desconectado. Por favor, conecte el dispositivo.";
    }

    private onErrorOccurred(e: any): void {
        this.statusDiv.textContent = `Ocurrió un error: ${e.error}`;
    }

    // ¡Este es el evento clave!
    private onSamplesAcquired(s: any): void {
        this.statusDiv.textContent = "Huella capturada. Procesando...";

        // s.samples es un array, tomamos la primera muestra que es la de mejor calidad.
        const fingerprintData = s.samples[0];

        // Muestra la imagen si el formato es PngImage
        if (s.sampleFormat === Fingerprint.SampleFormat.PngImage) {
            const fingerprintImage = Fingerprint.b64UrlTo64(fingerprintData);
            this.fpImage.src = `data:image/png;base64,${fingerprintImage}`;
        }

        // Enviamos la huella a nuestro backend PHP para identificarla
        this.sendFingerprintToServer(fingerprintData);
    }

    // 3. Función para iniciar la captura
    private startCapture(): void {
        this.statusDiv.textContent = "Esperando huella...";
        this.fpImage.src = "/assets/images/placeholder.png"; // Reset image
        
        // IMPORTANTE: Lee la sección "Paso 2" sobre el formato de la muestra
        this.fingerprintSdk.startAcquisition(Fingerprint.SampleFormat.Intermediate)
            .catch((error: any) => {
                this.statusDiv.textContent = `Error al iniciar captura: ${error}`;
            });
    }

    // 4. Función para enviar los datos al servidor (usando fetch, más moderno)
    private async sendFingerprintToServer(fingerprintData: string): Promise<void> {
        try {
            const response = await fetch("/api/endpoints/attendance/register.php", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ fingerprint: fingerprintData })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `Error del servidor: ${response.status}`);
            }
            
            this.statusDiv.textContent = result.message;

        } catch (error) {
            const errorMessage = (error instanceof Error) ? error.message : "Error de conexión con el servidor.";
            this.statusDiv.textContent = errorMessage;
        } finally {
            // Después de 2 segundos, reinicia la captura para el siguiente registro.
            setTimeout(() => this.startCapture(), 2000);
        }
    }
}

// Iniciar la aplicación cuando la página se haya cargado
window.onload = () => new AttendanceApp();