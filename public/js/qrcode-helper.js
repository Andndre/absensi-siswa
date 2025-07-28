// Simple QR Code generator menggunakan pure JavaScript
// Versi sederhana untuk menghindari dependency external

function generateQRCode(text, size = 200) {
    // Untuk sementara, kita akan menggunakan service API
    // Nanti bisa diganti dengan library lokal jika diperlukan
    return `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodeURIComponent(text)}`;
}

// Alternative: Menampilkan QR sebagai text jika API tidak tersedia
function generateQRText(text) {
    return `
        <div class="qr-text-fallback p-3 border rounded bg-light">
            <h6>QR Code Data:</h6>
            <code style="word-break: break-all;">${text}</code>
            <br><small class="text-muted">Scan dengan aplikasi QR scanner</small>
        </div>
    `;
}

// Export functions
window.QRCodeHelper = {
    generateQRCode,
    generateQRText
};
