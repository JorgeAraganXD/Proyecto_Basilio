// Función para calcular fecha de fin según el tipo de membresía
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');

    if (tipoSelect && fechaInicio && fechaFin) {
        function calcularFechaFin() {
            if (fechaInicio.value) {
                const inicio = new Date(fechaInicio.value);
                let fin = new Date(inicio);

                switch (tipoSelect.value) {
                    case 'dia':
                        fin.setDate(fin.getDate() + 1);
                        break;
                    case 'semanal':
                        fin.setDate(fin.getDate() + 7);
                        break;
                    case 'mensual':
                        fin.setMonth(fin.getMonth() + 1);
                        break;
                }

                fechaFin.value = fin.toISOString().split('T')[0];
            }
        }

        tipoSelect.addEventListener('change', calcularFechaFin);
        fechaInicio.addEventListener('change', calcularFechaFin);
    }

    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Confirmar eliminación
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de que desea eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });

    // Formato de moneda para inputs de precio
    const precioInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    precioInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
});

// Función para imprimir recibo
function imprimirRecibo(id) {
    window.open('imprimir_recibo.php?id=' + id, 'Recibo', 'width=800,height=600');
}

// Función para validar formulario
function validarFormulario(form) {
    let inputs = form.querySelectorAll('input[required], select[required]');
    let valid = true;

    inputs.forEach(input => {
        if (!input.value) {
            input.classList.add('is-invalid');
            valid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    return valid;
}

// Mostrar/ocultar contraseña
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.querySelector(`[onclick="togglePassword('${inputId}')"] i`);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
