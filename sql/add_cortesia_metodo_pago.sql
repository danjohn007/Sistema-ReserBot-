-- Agrega Cortesia como metodo de pago disponible.
ALTER TABLE `pagos`
    MODIFY COLUMN `metodo_pago`
    ENUM('paypal', 'efectivo', 'tarjeta', 'transferencia', 'cortesia')
    COLLATE utf8mb4_unicode_ci DEFAULT 'efectivo';
