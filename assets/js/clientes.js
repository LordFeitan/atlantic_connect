$(document).ready(function() {
    $('#tablaClientes').DataTable({
        ajax: {
            url: '../../php/cliente_controller.php',
            type: 'POST',
            data: { action: 'list' }
        },
        columns: [
            { data: 'cliente_id' },
            { data: 'nombre' },
            { data: 'dni' },
            { data: 'correo' },
            { data: 'telefono' },
            { 
                data: 'segmento_nombre',
                render: function(data, type, row) {
                    return `<span class="badge bg-primary">${data || 'Sin Asignar'}</span>`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <a href="perfil_cliente.php?id=${row.cliente_id}" class="btn btn-sm btn-info text-white" title="Ver Perfil"><i class="fas fa-eye"></i></a>
                        <button class="btn btn-sm btn-warning" onclick="editarCliente(${row.cliente_id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarCliente(${row.cliente_id})"><i class="fas fa-trash"></i></button>
                    `;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        }
    });

    // Input Restrictions
    ['dni', 'telefono'].forEach(id => {
        const el = document.getElementById(id);
        if(el) {
            el.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });

    // Real-time DNI Validation
    const dniInput = document.getElementById('dni');
    if(dniInput) {
        dniInput.addEventListener('input', function() {
            if (this.value.length === 8) {
                validateDNI(this.value);
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
});

function validateDNI(dni) {
    const currentId = $('#cliente_id').val();
    const formData = new FormData();
    formData.append('field', 'dni');
    formData.append('value', dni);
    if (currentId) {
        formData.append('exclude_id', currentId);
    }

    fetch('../../auth/check_duplicate.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const dniInput = document.getElementById('dni');
        const dniFeedback = document.getElementById('dniFeedback');
        if (data.exists) {
            dniInput.classList.add('is-invalid');
            dniInput.classList.remove('is-valid');
            if(dniFeedback) dniFeedback.textContent = 'Este DNI ya está registrado.';
        } else {
            dniInput.classList.remove('is-invalid');
            dniInput.classList.add('is-valid');
        }
    });
}

function resetForm() {
    $('#clienteForm')[0].reset();
    $('#cliente_id').val('');
    $('#action').val('create');
    $('#modalTitle').text('Nuevo Cliente');
    $('#clienteForm').removeClass('was-validated');
    $('.form-control').removeClass('is-valid is-invalid');
}

function guardarCliente() {
    const form = document.getElementById('clienteForm');
    
    // 1. Age Validation
    const dobInput = document.getElementById('fecha_nacimiento');
    if (dobInput && dobInput.value) {
        const dob = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        if (age < 18) {
            dobInput.classList.add('is-invalid');
            return; // Stop
        } else {
            dobInput.classList.remove('is-invalid');
        }
    }

    // 2. Check Validity
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    // 3. Check if DNI is invalid (from real-time check)
    if ($('#dni').hasClass('is-invalid')) {
        return;
    }

    const formData = new FormData(form);
    
    fetch('../../php/cliente_controller.php', {
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
                showConfirmButton: false,
                timer: 1500
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

    fetch('../../php/cliente_controller.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        $('#cliente_id').val(data.cliente_id);
        $('#nombre').val(data.nombre);
        $('#dni').val(data.dni);
        $('#fecha_nacimiento').val(data.fecha_nacimiento);
        $('#direccion').val(data.direccion);
        $('#correo').val(data.correo);
        $('#telefono').val(data.telefono);
        $('#preferencias').val(data.preferencias);
        $('#segmento_id').val(data.segmento_id);
        
        $('#action').val('update');
        $('#modalTitle').text('Editar Cliente');
        
        var myModal = new bootstrap.Modal(document.getElementById('clienteModal'));
        myModal.show();
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

            fetch('../../php/cliente_controller.php', {
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
                    Swal.fire(
                        'Error!',
                        data.message,
                        'error'
                    );
                }
            });
        }
    });
}
