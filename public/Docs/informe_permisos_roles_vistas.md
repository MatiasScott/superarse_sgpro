# Informe de permisos por rol y por vista en SGPRO

Fecha: 2026-04-08

## Alcance

Este informe se elaboro revisando rutas, controladores, modelos, sidebar y validaciones dentro de vistas.

Importante: en SGPRO coexisten dos mecanismos de autorizacion:

- Roles por nombre obtenidos con `RoleModel->getRolesByUserId()`.
- Rol numerico principal en `$_SESSION['user_role']`.

Por eso, en algunos modulos el acceso visible en el menu no coincide exactamente con los botones o acciones habilitadas dentro de la vista.

## Roles identificados

Segun el fallback de [app/models/RoleModel.php](../../app/models/RoleModel.php):

| ID | Rol |
| --- | --- |
| 1 | Super Administrador |
| 2 | Coordinador academico |
| 3 | Director de docencia |
| 4 | Talento humano |
| 5 | Profesor |

## Resumen general por vista

Leyenda:

- Menu: el rol ve la opcion en el sidebar.
- Ver: puede entrar y consultar informacion del modulo.
- Gestion total: puede operar sobre registros de terceros o sobre todo el modulo.
- Propio: el profesor normalmente ve o edita solo sus propios registros.

| Vista / modulo | Super Admin | Coordinador | Director | Talento humano | Profesor | Observacion principal |
| --- | --- | --- | --- | --- | --- | --- |
| Dashboard | Menu, Ver | Menu, Ver | Menu, Ver | Menu, Ver | Menu, Ver | Requiere sesion activa. |
| Mi Perfil / CV | Menu, Ver | Menu, Ver | Menu, Ver | Menu, Ver | Menu, Ver | El CV mostrado corresponde al usuario autenticado. |
| Portafolios | Menu, Gestion total | Menu, Gestion total | Menu, Gestion total | Menu, Ver | Menu, Propio | Profesor solo sobre su informacion; RH ve el modulo pero no aparece como aprobador. |
| Evaluaciones | Menu, Gestion total | Menu, Ver parcial | Menu, Ver | Menu, Ver | Menu, Propio | Hay inconsistencia entre sidebar, vista y controlador. |
| Continuidad | Menu, Gestion total | Menu, Ver | Menu, Ver parcial | Menu, Gestion total | Menu, Propio | Hay mezcla entre ids de rol y nombres de rol. |
| Asignaciones | Menu, Gestion total | Menu, Gestion total | Menu, Ver | Menu, Ver | Menu, Propio | Profesor solo ve sus asignaciones si es su unico rol. |
| Contratos | Menu, Gestion total | Menu, Ver | Menu, Ver | Menu, Gestion total | Menu, Propio | Profesor solo ve sus contratos si es su unico rol. |
| Facturas | Menu, Gestion total | Menu, Ver | Menu, Ver | Menu, Gestion total | Menu, Propio | En botones hay validaciones por id de rol. |
| Reportes | Menu, Ver | Menu, Ver parcial | Menu, Ver | Menu, Ver | Sin menu | Cada reporte tiene validacion distinta. |
| Materias | Menu, Gestion total | Menu, Gestion total | Menu, Ver | Menu, Ver | Sin menu | La vista usa ids 1 y 2 para acciones visibles. |
| Gestion de usuarios | Menu, Gestion total | Sin menu | Sin menu | Menu, Gestion total | Sin menu | Sidebar usa roles por nombre; la vista usa ids 1 y 4. |
| PAO | Menu, Gestion total | Sin menu | Menu, Gestion total | Sin menu | Sin menu | El controlador devuelve 403 si no esta autorizado. |
| Carreras | Sin menu | Ver por URL | Ver por URL | Ver por URL | Sin acceso si es solo Profesor | La ruta existe, pero no esta publicada en sidebar. |
| Notificaciones | Sin menu dedicado | Ver por ruta | Ver por ruta | Ver por ruta | Ver por ruta | Rutas disponibles para usuario autenticado. |

## Detalle por modulo

### 1. Dashboard

- Acceso para cualquier usuario autenticado.
- Evidencia: [app/controllers/DashboardController.php](../../app/controllers/DashboardController.php).

### 2. Mi Perfil / CV

- El enlace aparece sin condicion en el sidebar.
- El controlador trabaja sobre el usuario autenticado, por lo que la vista es transversal pero los datos son propios.
- Evidencia: [app/views/partials/sidebar.php](../../app/views/partials/sidebar.php), [app/core/Router.php](../../app/core/Router.php).

### 3. Portafolios

- Sidebar visible para los cinco roles.
- En [app/controllers/PortfolioController.php](../../app/controllers/PortfolioController.php), `canManageAll` solo incluye:
  - Super Administrador
  - Director de docencia
  - Coordinador academico
- El profesor solo puede editar su propio portafolio.
- Talento humano puede entrar al modulo y visualizar, pero no queda incluido en la capacidad de aprobar o gestionar todo.
- Evidencia complementaria: [app/views/partials/sidebar.php](../../app/views/partials/sidebar.php), [app/controllers/PortfolioController.php](../../app/controllers/PortfolioController.php).

### 4. Evaluaciones

- Sidebar visible para los cinco roles.
- El controlador `index()` permite entrar con sesion activa y filtra datos segun roles.
- En la vista [app/views/evaluations/index.php](../../app/views/evaluations/index.php), la cabecera para crear evaluacion solo aparece cuando `$_SESSION['user_role']` es 1 o 2.
- La misma vista define `isAdminEvaluator` como rol 5 o 1, es decir, Profesor o Super Administrador, lo que hace confusa la logica.
- En `edit()`, el controlador solo permite editar a:
  - Super Administrador
  - Profesor propietario de la evaluacion
- Resultado: Coordinador ve el modulo por sidebar, pero no tiene una politica uniforme de edicion en controlador.
- Evidencia: [app/views/evaluations/index.php](../../app/views/evaluations/index.php), [app/controllers/EvaluationController.php](../../app/controllers/EvaluationController.php).

### 5. Continuidad

- Sidebar visible para los cinco roles.
- La vista [app/views/continuity/index.php](../../app/views/continuity/index.php) muestra controles fuertes con `$_SESSION['user_role'] == 1 || 4` y casos especiales para rol 5.
- El controlador usa nombres de rol y distingue entre:
  - Super Administrador
  - Director de docencia o Talento humano
  - Profesor propietario
- Resultado: el modulo mezcla un esquema nuevo por nombre con otro viejo por id.

### 6. Asignaciones

- Sidebar visible para los cinco roles.
- El modelo [app/models/AssignmentModel.php](../../app/models/AssignmentModel.php) aplica filtro de "solo profesor": si el usuario solo tiene rol Profesor y no tiene ningun rol administrativo, solo ve sus asignaciones.
- La vista [app/views/academic/assignments.php](../../app/views/academic/assignments.php) muestra botones principales con ids 1 o 2.
- Resultado:
  - Super Administrador y Coordinador academico gestionan todo.
  - Profesor normalmente ve solo lo suyo.
  - Director y Talento humano aparecen en sidebar, pero la gestion visible no es tan amplia en la vista.

### 7. Contratos

- Sidebar visible para los cinco roles.
- El modelo [app/models/ContractModel.php](../../app/models/ContractModel.php) tambien aplica filtro de "solo profesor".
- La vista [app/views/contracts/index.php](../../app/views/contracts/index.php) usa ids 1 y 4 para varios botones.
- Resultado:
  - Super Administrador y Talento humano gestionan mas acciones visibles.
  - Profesor ve principalmente sus propios contratos.

### 8. Facturas

- Sidebar visible para los cinco roles.
- El modelo [app/models/InvoiceModel.php](../../app/models/InvoiceModel.php) filtra por profesor si es su unico rol.
- La vista [app/views/invoices/index.php](../../app/views/invoices/index.php) usa ids 1 y 5 para crear, y 1, 5 o 4 para algunas acciones por fila.
- Resultado:
  - Super Administrador y Talento humano tienen mas capacidades operativas.
  - Profesor conserva participacion sobre sus propios registros.
  - Hay inconsistencia porque Coordinador y Director ven el modulo por sidebar, pero no siempre quedan reflejados en botones por id.

### 9. Reportes

- Sidebar visible para:
  - Super Administrador
  - Coordinador academico
  - Director de docencia
  - Talento humano
- El `index()` usa una autorizacion general.
- Pero cada accion del controlador cambia la lista de roles permitidos.
- Ejemplos observados en [app/controllers/ReportController.php](../../app/controllers/ReportController.php):
  - `reportCvsByUser`: Super Administrador, Director de docencia, Talento humano.
  - `reportBillingByUser`: Super Administrador, Talento humano, Director de docencia.
  - `reportPortfolios`: Super Administrador, Director de docencia, Coordinador academico.
  - `reportTeachersByDedication`: Super Administrador, Talento humano, Director de docencia.
- Resultado: un rol puede ver el menu Reportes pero no necesariamente ejecutar todos los reportes.

### 10. Materias

- Sidebar visible para Super Administrador, Coordinador academico, Director de docencia y Talento humano.
- La vista [app/views/academic/subjects.php](../../app/views/academic/subjects.php) usa ids 1 y 2 para mostrar acciones principales.
- Resultado:
  - Super Administrador y Coordinador academico tienen gestion visible fuerte.
  - Director y Talento humano ven el modulo en menu, pero la vista no mantiene el mismo criterio.

### 11. Gestion de usuarios

- Sidebar visible para Super Administrador y Talento humano.
- El controlador [app/controllers/UserController.php](../../app/controllers/UserController.php) valida por nombre de rol.
- La vista [app/views/users/index.php](../../app/views/users/index.php) habilita crear, editar y eliminar solo si `$_SESSION['user_role']` es 1 o 4.
- Resultado: funcionalmente esta pensado para esos dos roles, pero con doble sistema de autorizacion.

### 12. PAO

- Sidebar visible para Super Administrador y Director de docencia.
- El controlador [app/controllers/PaoController.php](../../app/controllers/PaoController.php) valida por nombre de rol y corta con HTTP 403 si no corresponde.
- Es uno de los modulos con politica mas clara y cerrada.

### 13. Carreras

- La ruta existe en [app/core/Router.php](../../app/core/Router.php), pero no hay enlace en sidebar.
- El controlador [app/controllers/CareerController.php](../../app/controllers/CareerController.php) bloquea a quien sea "solo Profesor".
- Resultado:
  - Super Administrador, Coordinador academico, Director de docencia y Talento humano pueden entrar por URL directa.
  - Profesor puro no.

### 14. Notificaciones

- Existen rutas en [app/core/Router.php](../../app/core/Router.php).
- No se observa un item dedicado en el sidebar revisado.
- Operan como funcionalidad transversal para usuarios autenticados.

## Inconsistencias relevantes detectadas

### Inconsistencia 1. Doble sistema de roles

- Parte del sistema valida por nombre de rol.
- Otra parte valida por `$_SESSION['user_role']` con ids numericos.
- Esto puede producir diferencias entre:
  - lo que el menu muestra,
  - lo que el controlador deja entrar,
  - y lo que la vista permite hacer.

### Inconsistencia 2. Usuarios con multiples roles

- `getRolesByUserId()` soporta multiples roles.
- `$_SESSION['user_role']` conserva un solo rol principal.
- Si un usuario tiene mas de un rol, el resultado operativo depende de que modulo use cada mecanismo.

### Inconsistencia 3. Reportes con permisos variables

- El modulo Reportes tiene un acceso general en sidebar.
- Pero cada reporte concreto define su propia lista de roles permitidos.
- Entonces el menu no equivale a permiso total sobre todos los reportes.

### Inconsistencia 4. Modulos visibles para mas roles que los que realmente operan

- Materias, Evaluaciones, Facturas, Continuidad y otros modulos muestran el enlace a varios roles por nombre.
- Sin embargo, los botones de crear, editar o eliminar dentro de la vista siguen usando ids especificos.

### Inconsistencia 5. Carreras no publicada en el menu

- El modulo existe y tiene ruta.
- No aparece en sidebar.
- Por tanto, su acceso es tecnico por URL directa y no por navegacion normal.

## Conclusion operativa

La matriz mas estable que hoy se puede afirmar es esta:

- Super Administrador: acceso mas amplio y consistente en casi todo el sistema.
- Coordinador academico: acceso amplio en portafolios, asignaciones, materias y reportes parciales; menos consistente en evaluaciones y algunos botones internos.
- Director de docencia: acceso amplio a nivel de menu, con control fuerte en PAO y participacion importante en reportes; en otros modulos depende de si la vista sigue ids o nombres.
- Talento humano: acceso fuerte en usuarios, contratos, facturas y parte de reportes; en otros modulos a veces solo visualiza o queda fuera de acciones clave aunque vea el menu.
- Profesor: acceso transversal a varios modulos, pero normalmente limitado a sus propios registros cuando es su unico rol administrativo.

## Fuentes base revisadas

- [app/views/partials/sidebar.php](../../app/views/partials/sidebar.php)
- [app/core/Router.php](../../app/core/Router.php)
- [app/models/RoleModel.php](../../app/models/RoleModel.php)
- [app/controllers/PortfolioController.php](../../app/controllers/PortfolioController.php)
- [app/controllers/EvaluationController.php](../../app/controllers/EvaluationController.php)
- [app/controllers/ReportController.php](../../app/controllers/ReportController.php)
- [app/controllers/CareerController.php](../../app/controllers/CareerController.php)
- [app/views/users/index.php](../../app/views/users/index.php)
- [app/views/evaluations/index.php](../../app/views/evaluations/index.php)
- [app/views/academic/subjects.php](../../app/views/academic/subjects.php)
- [app/views/contracts/index.php](../../app/views/contracts/index.php)
- [app/views/invoices/index.php](../../app/views/invoices/index.php)
- [app/views/continuity/index.php](../../app/views/continuity/index.php)
