<?php
// Lista blanca de tablas que participan en la papelera y en los respaldos.
// IMPORTANTE: nunca uses un nombre de tabla que venga del usuario/formulario
// directo en una consulta SQL. Siempre valida contra esta lista primero.
return [
    'abonos'           => 'Abonos',
    'clientes'         => 'Clientes',
    'cotizaciones'     => 'Cotizaciones',
    'cotizacion_items' => 'Items de cotización',
    'departamentos'    => 'Departamentos',
    'facturas'         => 'Facturas',
    'factura_items'    => 'Items de factura',
    'inventario'       => 'Inventario',
    'pagos'            => 'Pagos',
    'roles'            => 'Roles',
    'usuarios'         => 'Usuarios',
];
