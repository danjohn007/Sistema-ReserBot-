-- =====================================================
-- Crear registros de pago para reservaciones completadas
-- que no tienen pago asociado
-- =====================================================

-- Insertar pagos para todas las reservaciones completadas sin pago
INSERT INTO pagos (reservacion_id, monto, metodo_pago, estado, fecha_pago, created_at)
SELECT 
    r.id,
    r.precio_total,
    NULL, -- método de pago sin definir (se edita después)
    'completado',
    COALESCE(r.updated_at, r.created_at), -- usar fecha de actualización como fecha de pago
    NOW()
FROM reservaciones r
LEFT JOIN pagos p ON r.id = p.reservacion_id
WHERE r.estado = 'completada'
  AND p.id IS NULL; -- solo las que NO tienen pago

-- Verificar cuántos registros se crearon
SELECT COUNT(*) as pagos_creados 
FROM pagos p
JOIN reservaciones r ON p.reservacion_id = r.id
WHERE r.estado = 'completada';
