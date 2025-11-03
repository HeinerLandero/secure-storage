// JavaScript para carga de archivos
console.log('Script de carga de archivos cargado');

// Timestamp para evitar caché
const scriptLoadTime = Date.now();
console.log('Script cargado a las:', scriptLoadTime);

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM cargado, inicializando handlers de carga de archivos');
    const fileUploadForm = document.getElementById('fileUploadForm');
    const uploadStatus = document.getElementById('uploadStatus');

    if (fileUploadForm) {
        fileUploadForm.addEventListener('submit', function (e) {
            e.preventDefault();
            uploadFile();
        });
    }

    function uploadFile() {
        const fileInput = document.getElementById('file');
        const file = fileInput.files[0];

        if (!file) {
            showStatus('Por favor selecciona un archivo', 'error');
            return;
        }

        // Verificar tamaño del archivo (límite 10MB)
        if (file.size > 10 * 1024 * 1024) {
            showStatus('El archivo es demasiado grande. Tamaño máximo: 10MB', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', document.querySelector('input[name="_token"]').value);

        showStatus('Subiendo archivo...', 'info');

        fetch('/files/upload', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData,
        })
            .then(async response => {
                let data;
                try {
                    data = await response.clone().json();
                } catch {
                    const text = await response.text();
                    throw new Error('El servidor devolvió una respuesta inesperada: ' + text.substring(0, 200));
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Error en la respuesta del servidor');
                }

                return data;
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showStatus(data.message, 'success');
                    fileInput.value = '';
                    // Refresh the page to show new file
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showStatus(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error de carga:', error);
                showStatus('Error al subir el archivo: ' + error.message, 'error');
            });
    }

    function showStatus(message, type) {
        if (!uploadStatus) return;

        uploadStatus.innerHTML = `<div class="${type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
            type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
                'bg-blue-100 border border-blue-400 text-blue-700'} px-4 py-3 rounded" role="alert">
                                    ${message}
                                 </div>`;
        uploadStatus.classList.remove('hidden');
    }
});

// Función para eliminar archivo
function deleteFile(fileId) {
    const deleteUrl = `/files/${fileId}`;

    console.log('Eliminando archivo con ID:', fileId);
    console.log('URL de eliminación:', deleteUrl);

    // Confirmar eliminación
    if (!confirm('¿Estás seguro de que deseas eliminar este archivo?')) {
        return;
    }

    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
        .then(response => {
            console.log('Estado de respuesta:', response.status);
            console.log('Respuesta ok:', response.ok);

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Delete response:', data);
            if (data.success) {
                // Show success message and remove the file row from the table
                alert(data.message);

                // Find and remove the file row from the table
                const button = document.querySelector(`button[onclick="deleteFile(${fileId})"]`);
                if (button) {
                    const row = button.closest('tr');
                    if (row) {
                        row.remove();
                    }
                }

                // Refresh storage info if needed
                // You could also make an AJAX call to refresh storage info here
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error al eliminar el archivo:', error);
            alert('Error al eliminar el archivo: ' + error.message);
        });
}
