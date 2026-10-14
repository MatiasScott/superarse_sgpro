-- Ejecutar una vez antes de desplegar la habilitacion por factura.
-- Las facturas existentes y nuevas empiezan deshabilitadas.
-- No elimina PDF ni observaciones. La antigua columna users.invoice_upload_enabled
-- puede conservarse; ya no se usa para autorizar cargas.
ALTER TABLE invoices
    ADD COLUMN invoice_upload_enabled TINYINT(1) NOT NULL DEFAULT 0;
