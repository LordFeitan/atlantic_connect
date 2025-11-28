$(document).ready(function() {
    $('#tablaClientes').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        }
    });
});

function resetForm() {
    $('#clienteForm')[0].reset();
    $('#cliente_id').val('');
    $('#action').val('create');
    $('#modalTitle').text('Nuevo Cliente');
}

function guardarCliente() {
    const formData = new FormData(document.getElementById('clienteForm'));
    
    fetch('../php/cliente_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                confirmButtonColor: '#d4af37'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error inesperado'
        });
    });
}

function editarCliente(id) {
    const formData = new FormData();
    formData.append('action', 'get_one');
    formData.append('id', id);

    fetch('../php/cliente_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        $('#cliente_id').val(data.cliente_id);
        $('#nombre').val(data.nombre);
        $('#direccion').val(data.direccion);
        $('#correo').val(data.correo);
        $('#telefono').val(data.telefono);
        $('#preferencias').val(data.preferencias);
        $('#segmento').val(data.segmento);
        
        $('#action').val('update');
        $('#modalTitle').text('Editar Cliente');
        
        var modal = new bootstrap.Modal(document.getElementById('clienteModal'));
        modal.show();
    });
}

function eliminarCliente(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            fetch('../php/cliente_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Eliminado!',
                        data.message,
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}
