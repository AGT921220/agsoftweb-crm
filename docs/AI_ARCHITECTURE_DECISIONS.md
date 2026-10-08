# Decisiones de arquitectura

## ADR-001 — Casos de uso en Features, Eloquent directo

El negocio vive en clases invocables de `app/Features`. El controlador valida y delega. No hay repositorios ni interfaces de persistencia: Eloquent es el acceso a datos de este proyecto.

## ADR-002 — Importes con brick/math

`App\Support\Money` redondea half up con `Brick\Math`. La librería ya llega con Laravel. No se usa `float` para guardar totales. El navegador imita el redondeo solo para mostrar.

## ADR-003 — Monedas separadas

No hay tipo de cambio. Dashboard, listados y PDF muestran MXN y USD por separado.

## ADR-004 — Copia al convertir

La orden guarda sus conceptos e importes. La cotización puede versionarse después sin cambiar la orden. `quotation_id` único evita una segunda orden de la misma cotización, y el caso de uso revisa toda la cadena de versiones.

## ADR-005 — Histórico append-only

Cambios de estado, precios, condiciones, seguimientos, versiones y entregas se agregan a `activity_logs`. Las instantáneas quedan en `quotation_versions`. Esos registros no se editan ni se borran con la operación.

## ADR-006 — Permisos con Spatie

Los tres roles siguen en `users.role`. El mapa de `RolePermissions` se carga en `spatie/laravel-permission` con `PermissionSeeder`. Las policies no leen el mapa: preguntan el permiso guardado. El acceso es por `nickname`. La contraseña de todos los usuarios sembrados es `admin`.

## ADR-007 — PDF en servidor

Dompdf genera el PDF desde Blade con CSS embebido. No depende del navegador del usuario.

## ADR-008 — UI Tabler por CDN

El panel usa Tabler y Bootstrap 5. No hay Tailwind de producto ni bundler obligatorio para operar el CRM. El cálculo de conceptos está en `public/js/line-items.js`.

## ADR-010 — Docker para el entorno local

Nginx, PHP-FPM 8.4, Horizon, el scheduler, MySQL 8.4, Redis y phpMyAdmin viven en `docker-compose.yml`. Los nombres de contenedor usan el sufijo `-crm` y el puerto web por defecto es 8090, porque 8080 ya lo usa otro stack de esta máquina. La aplicación habla con los hosts `mysql` y `redis`. Las URLs generadas van en HTTPS con `URL::forceScheme`. Las pruebas no entran a esos servicios.

## ADR-009 — Folios con bloqueo

`folio_sequences` se incrementa con `lockForUpdate` dentro de la transacción que crea el documento. Una versión nueva reutiliza el consecutivo y cambia el sufijo.
