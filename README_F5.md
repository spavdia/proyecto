# Fase 5 · Tareas y Objeciones

## Objetivo

La Fase 5 añade a PipelineDesk una vista operativa de **Tareas** y la primera lógica funcional de **Objeciones** dentro del flujo comercial.

El objetivo de esta fase es que el vendedor pueda:
- gestionar seguimientos desde una vista específica,
- ver productividad básica,
- recibir tareas automáticas cuando un lead entra en la etapa **Objeciones**,
- definir el bloqueo comercial,
- proponer una solución,
- y convertir esa definición en una guía práctica visible dentro de la propia tarea.

---

## Alcance de la fase

Esta fase incorpora:

- vista independiente de **Tareas**
- CRUD de tareas
- edición inline de tareas desde la tabla
- resumen visual en cabecera
- detección de tareas retrasadas
- integración con usuarios `admin` y `ventas`
- integración con leads
- creación automática de tarea cuando un lead entra en estado `Objeciones`
- definición de `tipo_bloqueo` y `solucion_bloqueo`
- generación automática de texto `INFO:` para orientar al vendedor
- actualización de `ultimo_contacto` del lead al crear actividad o tarea
- avisos flash en panel principal relacionados con tareas y objeciones

---

## Base de datos

### Tabla principal usada en esta fase
`tas_lead` / `tareas_lead` según estructura definitiva del proyecto.

Campos relevantes usados por la lógica de la fase:

- `id`
- `lead_id`
- `usuario_creador_id`
- `usuario_asignado_id`
- `tipo_actividad`
- `tipo_bloqueo`
- `solucion_bloqueo`
- `descripcion`
- `fecha_final`
- `estado`
- `leida_asignado`
- `created_at`
- `updated_at`

### Cambios funcionales aplicados
Se amplía `tipo_actividad` para soportar:

- `Llamada`
- `Email`
- `Cita presencial`
- `Objeciones`

Se añaden dos campos nuevos para trabajar el bloqueo comercial:

- `tipo_bloqueo`
- `solucion_bloqueo`

---

## Arquitectura MVC aplicada

### Modelos
- `LeadModel`
- `TareaModel`

### Controladores
- `LeadController`
- `TareaController`
- `HomeController`

### Vistas
- `home/tareas_view.php`
- `home/panel_view.php`

### Assets
- `public/css/tareas.css`
- `public/js/tareas.js`

---

## Flujo principal de tareas

## 1. Acceso a la vista Tareas
Desde el menú lateral, el usuario accede a la vista `Tareas`.

La pantalla muestra:
- cabecera de productividad
- bloque de bloqueos por resolver
- gráfico de tareas por estado
- próximos seguimientos
- tareas retrasadas
- tabla de tareas
- formulario lateral para nueva tarea

---

## 2. Crear tarea manual
El usuario pulsa **Nueva tarea**.

Se abre el formulario lateral con:
- lead
- usuario asignado
- actividad
- fecha final
- estado
- nota

Si la actividad elegida es `Objeciones` y el lead está realmente en estado `Objeciones`, se abre además el bloque específico:

- tipo de bloqueo
- solución propuesta

Al guardar:
- se inserta la tarea en base de datos
- se actualiza `ultimo_contacto` del lead
- se crea entrada en historial del lead
- se lanza mensaje flash

---

## 3. Editar tarea
Cada fila de la tabla puede entrar en modo edición pulsando el icono lápiz.

Se puede actualizar:
- nota
- fecha final
- estado
- y, si es tarea de objeción:
  - tipo de bloqueo
  - solución propuesta

Al guardar:
- se actualiza la tarea
- se registra historial en el lead
- se mantiene la edición dentro del flujo MVC tradicional con POST + redirect

---

## 4. Eliminar tarea
Cada fila permite eliminar la tarea.

Condiciones:
- admin puede gestionar todas
- ventas solo las que le pertenecen o ha creado

Al eliminar:
- se borra el registro
- se registra entrada de historial en el lead
- se muestra mensaje flash

---

## Lógica de Objeciones

## Activación automática
Cuando un lead entra en estado `Objeciones` desde:
- panel
- kanban
- edición del lead
- creación directa del lead

se intenta crear automáticamente una tarea de objeción.

### Condiciones
La tarea automática solo se crea si:
- el lead está realmente en estado `Objeciones`
- no existe ya otra objeción abierta para ese lead

### Valores por defecto
La tarea automática se crea con:
- `tipo_actividad = Objeciones`
- `tipo_bloqueo = Definir`
- `solucion_bloqueo = Definir`
- `estado = Pendiente`
- `fecha_final = hoy + 3 días`
- `descripcion = texto provisional de definición`

---

## Tipos de bloqueo definidos

En esta fase se han definido los siguientes tipos de objeción:

- `Definir`
- `Precio`
- `No me interesa`
- `No tengo tiempo`
- `Tiene que consultarlo`
- `Falta de confianza`
- `No sabe si es su nivel`
- `Ya usa otra solución`
- `No ve utilidad laboral`
- `Dudas sobre el método`

Estos bloqueos cubren escenarios comerciales generales y también escenarios concretos de academia o formación.

---

## Soluciones disponibles

Las soluciones iniciales configuradas son:

- `Definir`
- `Reencuadre de valor`
- `Facilidad y acompañamiento`
- `Prueba o demostración`

---

## Conversión automática a texto INFO

Cuando una tarea de objeción deja de estar en `Definir` y el vendedor selecciona una solución, el sistema reemplaza la descripción genérica por un texto práctico que comienza con `INFO:`.

Ejemplo funcional:

- bloqueo: `Precio`
- solución: `Reencuadre de valor`

Resultado visible en la nota:
`INFO: Relaciona el servicio con el bloqueo "Precio". Explica el beneficio clave y el coste de no avanzar. Cierra con una propuesta clara.`

### Objetivo
Ese texto sirve como micro-guía comercial para que el vendedor sepa qué hacer a continuación.

---

## Textos INFO implementados

### Reencuadre de valor
Ayuda al vendedor a:
- conectar problema y valor
- justificar inversión
- reforzar el resultado esperado

### Facilidad y acompañamiento
Ayuda al vendedor a:
- reducir fricción
- simplificar el siguiente paso
- acompañar al lead en la decisión

### Prueba o demostración
Ayuda al vendedor a:
- validar la propuesta
- mostrar evidencia
- mover la decisión con una acción concreta

---

## Actualización de último contacto

En esta fase se consolida la lógica para que el campo `ultimo_contacto` del lead se actualice cuando:

- se crea una actividad en la ficha del lead
- se crea una tarea asociada a un lead

Esto permite que el panel principal refleje movimiento real del lead.

---

## Panel principal

Se añaden o completan los siguientes comportamientos:

- aviso flash si existen tareas retrasadas
- enlace `Ver tareas`
- aviso flash cuando un lead entra en `Objeciones`
- enlace `Ver tareas` cuando el usuario debe definir la objeción pendiente

---

## Vista Tareas · mejoras visuales implementadas

### Cabecera
Incluye cuatro bloques:
- bloqueos por resolver
- gráfico por estado
- próximos seguimientos
- tareas retrasadas

### Tabla
- edición inline
- filas retrasadas en rojo
- chips visuales para bloqueo y solución
- color especial para leads cuya etapa actual es `Objeciones`

---

## Permisos

### Usuario ventas
Puede:
- ver sus tareas
- crear tareas
- editar tareas propias o creadas por él
- eliminar tareas propias o creadas por él

### Usuario admin
Puede:
- ver todas las tareas
- crear tareas
- editar cualquier tarea
- eliminar cualquier tarea
- ver resumen por usuarios en cabecera

---

## Historial del lead

Toda acción relevante de esta fase genera trazabilidad en el historial del lead:

- creación de lead
- cambio de estado
- nueva actividad
- creación de tarea
- actualización de tarea
- eliminación de tarea

Esto mantiene el lead como eje central de la aplicación.

---

## Estado al cierre de la fase

La Fase 5 deja implementado:

- módulo operativo de tareas
- integración real con leads
- automatización inicial de objeciones
- apoyo visual al vendedor
- primera capa de productividad comercial

---

## Pendiente para fases futuras

Queda fuera de esta fase:

- reglas de negocio avanzadas
- dashboard analítico completo
- automatizaciones complejas
- catálogo administrable de objeciones y soluciones
- métricas avanzadas por conversión y rendimiento