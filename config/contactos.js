// ── Contactos ──
let todosLosContactos = [];

function cargarContactos() {
    fetch('../controllers/get_clientes.php')
        .then(r => r.json())
        .then(data => {
            todosLosContactos = data;
            renderContactos(data);
        })
        .catch(() => {
            document.getElementById('tbody-contactos').innerHTML =
                '<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:32px;">Error al cargar contactos</td></tr>';
        });
}

function renderContactos(lista) {
    const tbody = document.getElementById('tbody-contactos');
    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--muted);font-size:0.78rem;padding:32px;">Sin contactos registrados</td></tr>';
        return;
    }
    tbody.innerHTML = lista.map(c => `
        <tr>
            <td><strong>${esc(c.nombre)}</strong></td>
            <td style="color:var(--muted)">${c.correo || '—'}</td>
            <td style="color:var(--muted)">${c.telefono || '—'}</td>
            <td style="color:var(--muted)">${c.rfc || '—'}</td>
            <td style="color:var(--muted);font-size:0.75rem">${c.created_at ? c.created_at.slice(0,10) : '—'}</td>
            <td>
                <button class="btn-sm btn-outline" style="font-size:0.7rem;padding:4px 10px"
                    onclick="verContacto(${c.id})">Ver</button>
            </td>
        </tr>
    `).join('');
}

function filtrarContactos() {
    const q = document.getElementById('buscador-contactos').value.toLowerCase();
    renderContactos(todosLosContactos.filter(c =>
        c.nombre.toLowerCase().includes(q) ||
        (c.correo || '').toLowerCase().includes(q) ||
        (c.telefono || '').toLowerCase().includes(q)
    ));
}

function abrirModalContacto() {
    ['c-nombre','c-telefono','c-correo','c-direccion','c-rfc','c-notas'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('modal-contacto').classList.add('open');
    document.getElementById('c-nombre').focus();
}

function cerrarModalContacto(e) {
    if (e && e.target !== document.getElementById('modal-contacto')) return;
    document.getElementById('modal-contacto').classList.remove('open');
}

function guardarContacto() {
    const nombre = document.getElementById('c-nombre').value.trim();
    if (!nombre) {
        document.getElementById('c-nombre').focus();
        document.getElementById('c-nombre').style.borderColor = '#ef4444';
        return;
    }
    document.getElementById('c-nombre').style.borderColor = '';

    const fd = new FormData();
    fd.append('nombre',    nombre);
    fd.append('telefono',  document.getElementById('c-telefono').value.trim());
    fd.append('correo',    document.getElementById('c-correo').value.trim());
    fd.append('direccion', document.getElementById('c-direccion').value.trim());
    fd.append('rfc',       document.getElementById('c-rfc').value.trim());
    fd.append('notas',     document.getElementById('c-notas').value.trim());

    fetch('../controllers/add_cliente.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.ok) {
                cerrarModalContacto();
                cargarContactos();
            } else {
                alert('Error: ' + res.msg);
            }
        });
}

function verContacto(id) {
    const c = todosLosContactos.find(x => x.id == id);
    if (!c) return;
    alert(`${c.nombre}\nTel: ${c.telefono||'—'}\nCorreo: ${c.correo||'—'}\nDirección: ${c.direccion||'—'}\nRFC: ${c.rfc||'—'}\nNotas: ${c.notas||'—'}`);
}

function esc(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Cargar al mostrar la sección
const _showSection = showSection;
window.showSection = function(id, el) {
    _showSection(id, el);
    if (id === 'contactos') cargarContactos();
};