# Agsoftweb CRM — contexto para agentes

CRM de cotizaciones y órdenes de compra. No es un ERP de ventas, inventario ni facturación.

## Stack

| Pieza | Uso |
| --- | --- |
| PHP | 8.4 FPM en Docker |
| Laravel | 13 |
| Base de datos | MySQL 8.4 (servicio `mysql`). Las pruebas usan SQLite en memoria |
| Colas | Redis y Horizon |
| Tareas | Contenedor `scheduler` con `schedule:work` |
| UI | Blade, Tabler 1.4 y Bootstrap 5 por CDN |
| PDF | `barryvdh/laravel-dompdf` |
| Importes | `brick/math` (`App\Support\Money`), redondeo half up |
| Auth | Sesión de Laravel. Login por nickname |
| Pruebas | PHPUnit. SQLite en memoria |
| Gráficas | Chart.js en el dashboard |

Zona horaria: `America/Mexico_City`. Idioma: español.

## Módulos

- **Clientes.** Catálogo mínimo. La cotización y la orden copian nombre, razón social, contacto, correo y teléfono.
- **Cotizaciones.** Folio, conceptos, estados, seguimiento, versiones, PDF y conversión a orden.
- **Órdenes de compra.** Alta directa o desde una cotización aprobada, entregas parciales, PDF e histórico.
- **Dashboard.** Conteos y montos por moneda, con filtros.

## Estados

Cotización: borrador, enviada, en seguimiento, en negociación, aprobada, rechazada, vencida, cancelada.

Orden: borrador, pendiente de confirmación, confirmada, en proceso, parcialmente entregada, completada, cancelada.

## Relaciones

- `Client` 1—N `Quotation` y `PurchaseOrder`.
- `Quotation` 1—N `QuotationItem`, 1—N `QuotationVersion`, 0—1 `PurchaseOrder`.
- Una versión apunta a `parent_id` y al origen `root_id`.
- `FollowUp`, `ActivityLog` y `Attachment` son polimórficos sobre cotización y orden.
- `PurchaseOrder` 1—N ítems, 1—N entregas, y cada entrega 1—N `PurchaseOrderDeliveryItem`.

## Árbol

```text
app/Enums
app/Exceptions/CrmRuleException.php
app/Features/Shared/Application
app/Features/Quotations/Application
app/Features/PurchaseOrders/Application
app/Features/Dashboard/Application/CrmMetrics.php
app/Http/Controllers
app/Http/Requests
app/Models
app/Policies
app/Support/Money.php
app/Support/Folio.php
app/Support/RolePermissions.php
database/seeders/PermissionSeeder.php
app/Console/Commands/ExpireQuotationsCommand.php
database/migrations
resources/views
public/js/line-items.js
routes/web.php
routes/console.php
docker-compose.yml
Dockerfile
docker/nginx
docker/php
tests/Feature
tests/Unit/MoneyTest.php
```

## Roles

| Rol | Alcance |
| --- | --- |
| admin | Todo |
| sales | Cotizaciones, su seguimiento, aprobar, cancelar y crear la orden al convertir |
| operations | Ver cotizaciones, crear y operar órdenes, confirmar, entregar y cancelar |

## Glosario

| Término | Significado |
| --- | --- |
| Folio | `COT-000001` o `OC-000001`. Una versión posterior usa el mismo consecutivo: `COT-000001-V2` |
| Subtotal | Suma de conceptos después del descuento y antes del impuesto |
| Instantánea | JSON en `quotation_versions` al aprobar o al crear otra versión |
| Conversión | Copia de la cotización aprobada a una orden propia |

## Fuera de alcance

Marketing, leads, inventario, facturación electrónica, contabilidad, nómina y envío real de correo o WhatsApp. El seguimiento solo registra la actividad.
