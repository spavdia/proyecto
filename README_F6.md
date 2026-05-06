# Fase 6 — Dashboard y vistas operativas de análisis

## Objetivo de la fase

Crear una capa de visualización y análisis dentro del CRM para que el usuario pueda:

- consultar métricas globales del embudo comercial
- revisar productividad comercial
- detectar leads sin contacto reciente
- controlar tareas y bloqueos
- acceder a un listado completo de leads
- filtrar la información para facilitar el trabajo comercial

Esta fase da un paso más allá del CRUD y del pipeline visual, centrándose en la explotación de la información.

---

## Funcionalidades implementadas

### 1. Dashboard comercial

Se ha creado una vista `dashboard_view` orientada a mostrar indicadores clave del CRM.

Incluye:

- total de leads
- leads ganados
- leads perdidos
- leads en objeciones
- valor del pipeline
- valor ganado
- conversión comercial
- tareas pendientes
- tareas retrasadas

---

### 2. Resumen del embudo comercial

Dentro del dashboard se muestra una visualización por etapas del embudo:

- Nuevo Lead
- Contactado
- En Progreso
- Objeciones
- Ganado
- Perdido

Para cada etapa se muestran datos como:

- número de leads
- porcentaje sobre el total
- valor económico acumulado
- tiempo medio en etapa

---

### 3. Leads sin contacto reciente

Se ha reutilizado un bloque tipo tabla para mostrar leads que necesitan atención comercial.

La tabla muestra:

- nombre del lead
- estado
- servicio
- responsable
- último contacto
- valor

Esta parte sirve como herramienta operativa directa para detectar oportunidades que necesitan seguimiento.

---

### 4. Dashboard de objeciones

Se ha añadido una parte específica para el análisis de objeciones, con datos como:

- objeciones abiertas
- objeciones resueltas
- tipos de objeción más frecuentes
- soluciones más utilizadas

Esto conecta la fase comercial con la gestión práctica del bloqueo de leads.

---

### 5. Seguimientos urgentes

Se ha incorporado un bloque para visualizar tareas o seguimientos más próximos.

El objetivo es priorizar acciones y facilitar la planificación comercial.

---

### 6. Productividad por usuario

Para usuarios admin se muestra un resumen comparativo por comercial.

Se puede consultar:

- total de leads por usuario
- leads ganados
- conversión
- valor ganado

Esto permite una visión más analítica del rendimiento del equipo.

---

### 7. Vista de Tareas mejorada

La vista de tareas ha evolucionado visual y funcionalmente.

Incluye:

- cabecera con indicadores
- bloque de bloqueos por resolver
- gráfico semáforo KPI de tareas por estado
- próximos seguimientos
- tareas retrasadas
- resumen por usuario para admin
- tabla operativa de tareas
- edición rápida del estado desde selector
- edición completa con lápiz
- formulario lateral para nueva tarea

---

### 8. Semáforo KPI de tareas

Se ha implementado un bloque visual tipo semáforo para representar el porcentaje de tareas terminadas.

Tramos:

- rojo: 0% a 50%
- amarillo: 50% a 80%
- verde: 80% a 100%

El indicador se mueve según el porcentaje real de tareas completadas.

---

### 9. Nueva vista de Listado

Se ha preparado una nueva vista `listado_view` con su CSS propio `listado.css`.

Su objetivo es mostrar todos los leads del CRM con un formato equivalente al bloque de tabla del dashboard.

La plantilla visual ya está diseñada para soportar filtros por:

- curso
- mes
- vendedor
- estado del embudo

Actualmente la estructura visual está creada y lista para conectarse con controller y modelo.

---

## Archivos principales de la fase

### Vistas

- `app/views/home/dashboard_view.php`
- `app/views/home/tareas_view.php`
- `app/views/home/listado_view.php`

### Estilos

- `public/css/dashboard.css`
- `public/css/tareas.css`
- `public/css/listado.css`

### Scripts

- `public/js/dashboard.js`
- `public/js/tareas.js`

### Modelos

- `app/models/LeadModel.php`
- `app/models/TareaModel.php`

### Controllers relacionados

- `HomeController`
- `TareaController`

---

## Estado actual de la fase

### Parte terminada

- dashboard funcional
- vista de tareas avanzada
- indicadores visuales
- resumen de productividad
- estructura visual del listado

### Parte pendiente para cerrar la fase

- conectar `listado_view` con datos reales
- implementar filtros reales del listado en controller/model
- enlazar definitivamente la vista listado desde menú o navegación
- validar que todos los filtros funcionan correctamente

---

## Aprendizajes MVC de esta fase

### Modelo

El modelo prepara datos agregados y consultas filtradas para la capa de análisis:

- resúmenes globales
- agrupaciones por estado
- comparativas por usuario
- listados de leads y tareas

### Controlador

El controller recoge filtros desde `GET`, llama al modelo y decide qué datos necesita cada vista.

### Vista

La vista se centra en pintar:

- métricas
- tablas
- bloques visuales
- estados
- filtros

Manteniendo separada la lógica de negocio de la presentación.

---

## Resultado de la fase

La aplicación deja de ser solo un CRM operativo básico y pasa a ofrecer una capa real de control comercial:

- permite analizar
- permite priorizar
- permite detectar bloqueos
- permite seguir actividad
- permite revisar rendimiento del equipo

Es una fase clave porque transforma los datos en información útil para tomar decisiones.