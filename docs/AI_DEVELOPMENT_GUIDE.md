# Guía de desarrollo

Recetas de este repositorio. Las clases citadas son las de referencia.

## Cotización

1. FormRequest `StoreQuotationRequest` / `UpdateQuotationRequest` con `QuotationRules`.
2. Caso de uso `CreateQuotation` o `UpdateQuotation`.
3. `CalculateDocumentTotals` recalcula líneas. No guardes el total que manda el navegador.
4. Vista `resources/views/quotations/_form.blade.php`.
5. Prueba en `tests/Feature/QuotationTest.php`.

## Estado

1. `ChangeQuotationStatusRequest` elige el permiso según el estado destino.
2. `ChangeQuotationStatus` rechaza transiciones fuera de `QuotationStatus::transitions()`.
3. Rechazo y cancelación exigen comentario.
4. Aprobar guarda instantánea con `SnapshotQuotation`.

## Versión

`CreateQuotationVersion` copia la cotización, conserva el `sequence_number`, sube `version_number` y deja la nueva en borrador. No crea versión si la cadena ya tiene un borrador.

## Conversión

`ConvertQuotationToPurchaseOrder` exige estado aprobada, bloquea si otra versión de la cadena ya tiene orden y copia importes guardados, sin recalcular.

## Orden y entrega

1. Alta directa: `CreatePurchaseOrder` (borrador).
2. Estado: `ChangePurchaseOrderStatus`.
3. Entrega: `RegisterDelivery`. Compara la cantidad contra `quantity - quantity_delivered`.
4. Referencia de prueba: `tests/Feature/PurchaseOrderTest.php`.

## Permiso nuevo

1. Caso en `App\Enums\Permission`.
2. Fila en `RolePermissions` y `php artisan db:seed --class=PermissionSeeder`.
3. Método en la policy.
4. `authorize()` del FormRequest o `$this->authorize()` en el controlador.
5. Actualiza `.cursor/rules/04-permissions.mdc` si el mapa cambió.

## Listado

`Quotation::filtered()` y `PurchaseOrder::filtered()` aplican búsqueda, filtros y orden. El controlador pagina 15. La vista es una tabla Blade.

## PDF

`QuotationController::pdf` y `PurchaseOrderController::pdf`. `?download=1` descarga. Sin ese parámetro, el navegador lo muestra.

## Seguimiento

`AddFollowUp` crea el registro, actualiza `next_follow_up_at` del documento y escribe en `activity_logs`.

## Adjunto

`StoreAttachment` guarda el archivo en el disco `local` y registra la actividad. La descarga pasa por `AttachmentController` y exige permiso de ver el documento padre.

## Dashboard

`DashboardController` valida filtros y llama a `CrmMetrics`. No mezcles monedas en una suma nueva.

## Prueba

```bash
docker compose exec php php artisan test
```

`phpunit.xml` fuerza SQLite en memoria. No hace falta MySQL para la suite.
