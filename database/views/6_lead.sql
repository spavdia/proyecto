USE pipeline_crm;

INSERT INTO leads (
    lead_nombre,
    estado,
    responsable_id,
    servicios,
    indicaciones,
    lead_score,
    email,
    telefono,
    valor,
    ultimo_contacto,
    prioridad,
    origen,
    created_at
) VALUES
('Jose', 'Nuevo Lead', 2, 'B1 Inglés', 'Primer contacto desde campaña local.', 12, 'lead01@test.com', '600000001', 120.00, NULL, 'Media', 'formulario_web', '2025-01-05 10:15:00'),
('Pepe', 'Contactado', 2, 'B2 Inglés', 'Pidió información por WhatsApp.', 28, 'lead02@test.com', '600000002', 180.00, '2025-01-10 17:20:00', 'Alta', 'app_interna', '2025-01-06 11:10:00'),
('Paco', 'En Progreso', 3, 'Informática', 'Interesado en clases semanales.', 45, 'lead03@test.com', '600000003', 240.00, '2025-01-18 12:00:00', 'Media', 'formulario_web', '2025-01-07 09:00:00'),
('Fatima', 'Objeciones', 2, 'Apoyo Primaria', 'Objeción de precio inicial.', 51, 'lead04@test.com', '600000004', 95.00, '2025-01-20 16:40:00', 'Alta', 'formulario_web', '2025-01-08 13:30:00'),
('Manolo', 'Ganado', 3, 'Apoyo Secundaria', 'Matrícula cerrada tras llamada.', 88, 'lead05@test.com', '600000005', 320.00, '2025-01-22 18:10:00', 'Alta', 'app_interna', '2025-01-09 10:20:00'),
('Enrique', 'Perdido', 2, 'Apoyo Bach', 'No continúa por falta de tiempo.', 22, 'lead06@test.com', '600000006', 210.00, '2025-01-19 11:45:00', 'Baja', 'formulario_web', '2025-01-10 09:40:00'),
('Susana', 'En Progreso', 3, 'Acceso Univ+25', 'Muy cerca de cierre.', 67, 'lead50@test.com', '600000050', 355.00, '2025-05-18 18:45:00', 'Alta', 'app_interna', '2025-05-10 12:55:00');