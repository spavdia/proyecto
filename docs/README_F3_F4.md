# PipelineDesk - README Fase 3 y Fase 4

## Estado actual del proyecto

En este punto del proyecto, PipelineDesk ya dispone de una base funcional sólida para la gestión comercial de leads dentro de una aplicación CRM en PHP con arquitectura MVC.

Este README reúne lo desarrollado en la **Fase 3** y la **Fase 4**.

> Nota: las **reglas de negocio del embudo** no se incluyen aquí porque quedan pendientes para una fase posterior de ajuste y cierre funcional.

---

# FASE 3 - Detalle del lead, notas, historial y CRUD avanzado

## Objetivo de la fase

La Fase 3 se centró en convertir cada lead en una ficha operativa completa dentro del CRM. El objetivo fue pasar de un simple listado de leads a una vista de trabajo real, donde se pudiera consultar información, registrar actividad, ver historial y editar datos del lead.

---

## Funcionalidades implementadas

### 1. Vista `detalles_view.php`

Se creó una vista de detalle del lead con estructura de trabajo real.

La vista incluye:

- cabecera del lead
- información general del lead
- estado actual dentro del embudo
- días que lleva en el sistema
- acceso rápido a cambio de estado
- zona de actividad
- zona de historial

La vista se integra con el layout privado de la aplicación y reutiliza el menú lateral común.

---

### 2. Gestión de notas del lead

Se implementó el registro de actividad comercial mediante notas asociadas al lead.

Cada nota permite guardar:

- tipo de actividad:
  - Llamada
  - Email
  - Cita presencial
- contenido de la actividad
- usuario que realizó la acción
- fecha de creación

Las notas se almacenan en la tabla `notas_lead` y se muestran dentro de la ficha del lead.

---

### 3. Historial del lead

Se añadió un sistema de historial para registrar acciones importantes relacionadas con el lead.

El historial registra eventos como:

- alta de lead
- cambio de estado
- creación de notas
- actualización de datos del lead

El objetivo del historial es dejar trazabilidad sobre la evolución del lead dentro del CRM.

---

### 4. Cambio de estado desde detalle

Además del cambio de estado desde panel, la ficha del lead permite actualizar el estado desde su propia vista.

Esto permite gestionar el avance del lead sin salir del detalle.

---

### 5. Update Lead desde `detalles_view`

Se implementó la edición del lead directamente dentro de la vista detalle.

La misma vista funciona en dos modos:

- modo lectura
- modo edición

El cambio a modo edición se realiza mediante parámetro `GET`, y el guardado mediante `POST`.

Campos editables generales:

- lead_nombre
- email
- teléfono
- responsable
- servicios
- valor
- prioridad
- estado

Además, el usuario administrador puede editar todos los campos del lead, incluidos los más sensibles o técnicos.

---

### 6. Delete Lead

Se implementó la eliminación del lead.

La acción de borrado quedó restringida al rol **admin**. El control se realiza en dos niveles:

- ocultando el botón en la vista si el usuario no es admin
- validando también el rol en el controller antes de borrar

Con ello se evita que un usuario sin permisos pueda eliminar leads aunque intente forzar la petición manualmente.

---

### 7. Integración con historial

Las operaciones principales de Fase 3 quedan conectadas con el historial:

- creación de nota
- cambio de estado
- actualización del lead
- eliminación del lead cuando corresponda registrar evento previo

---

## Flujo técnico de la fase

### Flujo de detalle del lead

1. El usuario entra al detalle del lead.
2. El controller busca el lead en base de datos.
3. Se cargan notas e historial.
4. La vista muestra la ficha completa.

### Flujo de edición

1. El usuario pulsa **Editar**.
2. La vista se recarga en modo edición.
3. El formulario envía `POST` al controller.
4. El controller valida los datos.
5. El model realiza el `UPDATE`.
6. Se registra historial.
7. Se redirige al detalle en modo lectura.

### Flujo de notas

1. El usuario completa la actividad.
2. El controller valida los datos.
3. Se inserta la nota en `notas_lead`.
4. Se registra el evento en historial.
5. Se vuelve al detalle del lead.

---

## Archivos principales de la fase

### Controllers
- `LeadController.php`

### Models
- `LeadModel.php`

### Views
- `app/views/lead/detalles_view.php`

### CSS / JS
- `public/css/detalles.css`
- `public/js/detalles.js` o lógica equivalente del menú lateral

### Base de datos
- tabla `notas_lead`
- tabla `historial_lead`

---

## Resultado de la Fase 3

Con esta fase, cada lead deja de ser solo una fila dentro del panel y pasa a tener una ficha completa de trabajo. El CRM gana trazabilidad, contexto comercial y capacidad de mantenimiento de datos reales.

---

# FASE 4 - Pipeline Kanban interactivo

## Objetivo de la fase

La Fase 4 tuvo como objetivo crear una vista específica del pipeline comercial en formato Kanban, separada del panel clásico.

Se decidió no mezclar esta funcionalidad con `panel_view`, sino construir una nueva vista independiente:

- `kanban_view.php`

Esto permitió desarrollar la experiencia de arrastrar y soltar leads entre estados sin afectar la estabilidad del panel tradicional.

---

## Funcionalidades implementadas

### 1. Nueva vista `kanban_view.php`

Se creó una vista privada independiente para el pipeline comercial.

Esta vista incluye:

- layout común de la aplicación
- menú lateral compartido
- cabecera propia
- columnas por estado del embudo
- tarjetas de lead dentro de cada columna

---

### 2. Columnas por estado

Se representaron los estados del embudo como columnas visuales:

- Nuevo Lead
- Contactado
- En Progreso
- Objeciones
- Ganado
- Perdido

Cada columna agrupa automáticamente los leads según su estado actual.

---

### 3. Tarjetas Kanban de leads

Cada lead se representa como una tarjeta individual dentro de su columna.

La tarjeta muestra información comercial útil y enlaza con la ficha detalle del lead.

Además, se añadió una configuración dinámica para decidir qué campos se ven dentro de cada tarjeta.

Campos configurables:

- nombre
- email
- teléfono
- responsable
- servicio
- valor
- prioridad
- estado
- indicaciones
- último contacto
- origen
- score

Campos obligatorios en tarjeta:

- nombre
- servicio

---

### 4. Configuración visual dinámica de tarjetas

En la cabecera de `kanban_view` se añadió un botón de configuración.

Desde ese botón se abre un panel desplegable que permite activar o desactivar los campos visibles en las tarjetas del Kanban.

La configuración se guarda en el navegador mediante `localStorage`, por lo que se mantiene al recargar la página.

---

### 5. Drag and Drop con HTML5 nativo

Se implementó arrastrar y soltar usando HTML5 Drag and Drop.

Cada tarjeta:

- es arrastrable
- guarda su `id`
- guarda su estado actual

Cada columna:

- representa un estado destino posible

Esto permite mover leads entre estados del embudo de forma visual.

---

### 6. Persistencia con `fetch()` y endpoint POST

Se utilizó la opción de actualización asíncrona con `fetch()`.

Al soltar una tarjeta en una nueva columna:

1. JavaScript detecta el lead movido
2. obtiene el estado destino
3. envía `POST` al endpoint del pipeline
4. el controller valida y actualiza estado
5. el model persiste el cambio en base de datos
6. se registra historial
7. el frontend mantiene o revierte el movimiento según la respuesta

---

### 7. Endpoint específico para Kanban

Se creó un endpoint específico para el cambio de estado desde el tablero Kanban, devolviendo respuesta JSON.

Esto permite separar:

- lógica HTML tradicional con redirección
- lógica AJAX con `fetch()` y respuesta asíncrona

---

### 8. Resumen por columna

Cada columna muestra:

- número de leads
- suma total en euros de los valores de las tarjetas de esa columna

Ese resumen se actualiza también cuando una tarjeta se mueve de una columna a otra.

---

### 9. Mejora visual de columnas y tarjetas

Se añadieron mejoras visuales para que el pipeline resulte más claro y profesional:

- columnas con color de fondo según etapa
- cabecera de columna en forma de flecha
- resumen visual de conteo y valor
- tarjeta con barra superior según prioridad
- chip de prioridad coloreado
- feedback visual al arrastrar
- resaltado de la columna destino
- mensajes visuales de éxito o error

---

### 10. Menú lateral común y navegación

La vista Kanban usa el mismo menú lateral común del panel privado.

Se enlazó correctamente la opción **Pipeline** del menú a esta nueva vista.

Además, la vista dispone de:

- botón menú para mostrar el aside
- botón volver al panel

---

## Flujo técnico de la fase

### Flujo de carga del Kanban

1. El usuario entra en `/pipeline`.
2. El controller carga los leads agrupados por estado.
3. La vista construye las columnas del Kanban.
4. Cada columna pinta sus tarjetas correspondientes.

### Flujo de movimiento de tarjeta

1. El usuario arrastra una tarjeta.
2. La suelta en otra columna.
3. JavaScript detecta el nuevo estado.
4. Se envía `fetch()` al endpoint.
5. Si la respuesta es correcta:
   - se mantiene la tarjeta en la nueva columna
   - se actualizan contadores y valor total
6. Si falla:
   - la tarjeta vuelve a la columna anterior
   - se muestra mensaje de error

---

## Archivos principales de la fase

### Controllers
- `HomeController.php`
- `LeadController.php`

### Views
- `app/views/home/kanban_view.php`
- `app/views/layouts/panel_aside.php`

### CSS / JS
- `public/css/kanban.css`
- `public/js/kanban.js`

### Rutas
- ruta GET para `/pipeline`
- ruta POST para actualización asíncrona de estado

---

## Resultado de la Fase 4

Con esta fase, PipelineDesk incorpora un pipeline visual real en formato Kanban, preparado para gestión comercial más rápida y visual.

El usuario ya puede mover leads entre etapas del embudo desde una interfaz moderna, con persistencia inmediata y reflejo directo en base de datos.

---

# Estado al finalizar Fase 4

Tras completar Fase 3 y Fase 4, el proyecto ya dispone de:

- autenticación
- panel privado
- CRUD funcional de leads
- detalle completo del lead
- notas
- historial
- actualización de estado
- pipeline Kanban interactivo
- persistencia en tiempo real del movimiento de leads

Las reglas de negocio más avanzadas del embudo quedan pendientes para una fase posterior de refinado.

---

# Próximo paso recomendado

El siguiente bloque natural del proyecto es la **Fase 5**, centrada en productividad comercial, seguimiento operativo y gestión más avanzada del trabajo diario del equipo de ventas.