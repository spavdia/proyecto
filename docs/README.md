# PipelineDesk CRM — README general del proyecto

## 1. Visión general

**PipelineDesk** es un CRM Pipeline desarrollado en **PHP** con arquitectura **MVC**, base de datos **MySQL** y una interfaz web construida con **HTML5**, **CSS**, **JavaScript** y mejoras visuales progresivas con **Tailwind**.

El proyecto está orientado a la gestión comercial completa de leads dentro de un embudo de ventas, combinando una parte pública de captación con una parte privada de trabajo interno para comerciales y administradores.

Su evolución se ha organizado por fases, lo que ha permitido construir la aplicación de forma progresiva: primero la base técnica, después la autenticación, la gestión de leads, el detalle comercial, el pipeline visual, las tareas y objeciones, la capa analítica del dashboard y, por último, la mejora visual global con modo oscuro, toasts y objetivos comerciales.

---

## 2. Objetivo del proyecto

El objetivo principal de PipelineDesk es ofrecer una aplicación web clara, usable y ampliable para:

- iniciar sesión con usuarios reales,
- trabajar con una base de datos MySQL real,
- gestionar leads y su evolución dentro del pipeline,
- registrar actividad comercial,
- controlar tareas y objeciones,
- visualizar métricas y productividad,
- y presentar una interfaz sólida y profesional.

Además de su utilidad funcional, el proyecto tiene una finalidad académica: aprender a construir una aplicación web completa en PHP MVC entendiendo cómo se relacionan rutas, controladores, modelos, vistas y base de datos.

---

## 3. Tecnologías utilizadas

### Backend
- PHP
- Programación orientada a objetos
- Arquitectura MVC
- MySQL
- Sesiones PHP
- Clase `Database` para acceso a datos

### Frontend
- HTML5
- CSS / CSS3
- JavaScript puro
- Tailwind CSS aplicado de forma progresiva

### Entorno de desarrollo
- Windows
- XAMPP
- Visual Studio Code
- Git y GitHub

---

## 4. Arquitectura del proyecto

PipelineDesk sigue una arquitectura **MVC** con separación de responsabilidades:

- **Route**: decide qué controlador atiende cada URL.
- **Controller**: recibe la petición, valida, coordina la lógica y decide qué vista o modelo utilizar.
- **Model**: encapsula la interacción con la base de datos.
- **View**: genera la salida HTML/PHP que ve el usuario.

Este enfoque permite ordenar el proyecto, mantener el código más limpio y facilitar la evolución por fases.

---

## 5. Estructura general

```text
crm-pipeline/
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
│       ├── errors/
│       ├── home/
│       ├── lead/
│       └── layouts/
├── database/
│   └── migrations/
├── lib/
│   ├── config.php
│   ├── Database.php
│   ├── Route.php
│   └── SessionManager.php
├── public/
│   ├── css/
│   ├── img/
│   ├── js/
│   └── index.php
├── routes/
│   └── web.php
└── storage/
```

### Archivos base más importantes
- `public/index.php`: punto de entrada de la aplicación.
- `routes/web.php`: registro de rutas GET y POST.
- `lib/Route.php`: resolución del enrutado.
- `lib/config.php`: constantes globales.
- `lib/Database.php`: conexión y consultas.
- `lib/SessionManager.php`: sesiones, usuario autenticado y mensajes.
- `app/controllers/`: controladores de la aplicación.
- `app/models/`: lógica de acceso a datos.
- `app/views/`: vistas y layouts compartidos.

---

## 6. Evolución del proyecto por fases — resumen ejecutivo

### Fase 0
Se construye la base técnica del proyecto: estructura MVC, router, controlador inicial, layouts y preparación de la conexión con MySQL.

### Fase 1
Se implementa la autenticación real: usuarios, login, sesiones, rutas protegidas y logout.

### Fase 2
Se construye el módulo inicial de leads: entrada pública, entrada interna, panel por estados y cambio de estado desde tabla.

### Fase 3
Cada lead pasa a tener ficha propia: detalle completo, notas, historial, edición avanzada y eliminación controlada.

### Fase 4
Se crea el pipeline visual en formato Kanban: columnas por estado, tarjetas de lead, drag and drop y persistencia en tiempo real.

### Fase 5
Se añade la vista de Tareas y la lógica de Objeciones: CRUD de tareas, edición inline, tareas automáticas y apoyo comercial al vendedor.

### Fase 6
Se incorpora la capa analítica: dashboard, productividad, embudo comercial, seguimientos urgentes y base de la vista de listado.

### Fase 7
Se moderniza la interfaz: Tailwind progresivo, modo oscuro, sistema de toast, footer global, privacidad, objetivos comerciales y mejoras visuales operativas.

---

## 7. Desarrollo detallado por fases

# FASE 0 — Base MVC y arranque del proyecto

## Objetivo
Crear la base técnica sobre la que crecerá toda la aplicación.

## Qué se implementó
- estructura de carpetas MVC,
- configuración base,
- sistema de rutas,
- controlador inicial,
- vistas y layouts comunes,
- CSS y JS base,
- preparación de la conexión a MySQL.

## Flujo básico
```text
Navegador
→ public/index.php
→ routes/web.php
→ Route
→ Controller
→ View
→ HTML al navegador
```

## Resultado
La aplicación ya podía servir una vista pública y tenía una arquitectura preparada para crecer con nuevas funcionalidades.

---

# FASE 1 — Usuarios, login, sesiones y protección de rutas

## Objetivo
Dar acceso real al sistema y separar zona pública de zona privada.

## Qué se implementó
- conexión real con base de datos,
- tabla `usuarios`,
- login funcional,
- validación del usuario,
- uso de `password_hash` y `password_verify`,
- sesiones,
- panel privado básico,
- logout,
- protección de rutas privadas.

## Flujo de autenticación
```text
GET /login
→ mostrar formulario

POST /login
→ validar campos
→ buscar usuario
→ comprobar password
→ guardar usuario en sesión
→ redirigir a /panel
```

## Resultado
PipelineDesk ya dispone de una puerta de entrada real al CRM y de un contexto de usuario autenticado.

---

# Mejora visual previa a Fase 2

Antes de desarrollar el módulo de leads se reforzó la capa visual inicial.

## Qué se mejoró
- portada pública,
- login,
- branding de `PipelineDesk`,
- layouts comunes,
- CSS específico por vista,
- visor de diapositivas para presentación académica.

## Resultado
El proyecto pasó a tener una identidad visual reconocible y una mejor base de presentación.

---

# FASE 2 — Leads, formularios de entrada y panel por estados

## Objetivo
Transformar la aplicación en un CRM funcional inicial con gestión real de leads.

## Funcionalidades implementadas
- tabla `leads`,
- formulario público de contacto,
- formulario interno de creación de leads,
- validación por campo,
- sticky form,
- panel agrupado por estados,
- cambio de estado desde el panel.

## Datos principales del lead
- `lead_nombre`
- `estado`
- `responsable_id`
- `servicios`
- `indicaciones`
- `lead_score`
- `email`
- `telefono`
- `valor`
- `ultimo_contacto`
- `prioridad`
- `origen`

## Estados del embudo
- Nuevo Lead
- Contactado
- En Progreso
- Objeciones
- Ganado
- Perdido

## Entradas de lead
### Pública
Ruta `/contacto`, pensada para captación externa.

### Interna
Ruta `/leads/nuevo`, pensada para crear leads desde la app.

## Resultado
La aplicación ya permite crear leads, verlos agrupados por fase y moverlos entre estados desde el panel.

---

# FASE 3 — Detalle del lead, notas, historial y CRUD avanzado

## Objetivo
Convertir cada lead en una ficha operativa completa con trazabilidad comercial.

## Funcionalidades implementadas
- vista `detalles_view.php`,
- información general del lead,
- estado actual y días en sistema,
- registro de notas comerciales,
- historial del lead,
- cambio de estado desde detalle,
- edición del lead en modo lectura / edición,
- eliminación restringida al rol admin.

## Tablas añadidas o reforzadas
- `notas_lead`
- `historial_lead`

## Resultado
Cada lead deja de ser solo una fila dentro del panel y pasa a tener una ficha completa de trabajo.

---

# FASE 4 — Pipeline Kanban interactivo

## Objetivo
Crear una vista independiente del pipeline comercial en formato Kanban.

## Funcionalidades implementadas
- nueva vista `kanban_view.php`,
- columnas por estado,
- tarjetas de lead,
- configuración dinámica de campos visibles,
- drag and drop con HTML5,
- persistencia con `fetch()` y endpoint POST,
- resumen por columna,
- mejoras visuales de columnas y tarjetas,
- reutilización del menú lateral común.

## Resultado
PipelineDesk incorpora un pipeline visual real, más rápido y más intuitivo para mover leads entre etapas del embudo.

---

# FASE 5 — Tareas y Objeciones

## Objetivo
Añadir una vista operativa de tareas y la primera lógica funcional de objeciones dentro del flujo comercial.

## Funcionalidades implementadas
- vista independiente de `Tareas`,
- CRUD de tareas,
- edición inline desde tabla,
- cabecera de productividad,
- detección de tareas retrasadas,
- integración con usuarios `admin` y `ventas`,
- integración con leads,
- creación automática de tarea cuando un lead entra en `Objeciones`,
- definición de `tipo_bloqueo` y `solucion_bloqueo`,
- generación automática de texto `INFO:`,
- actualización de `ultimo_contacto` al crear actividad o tarea,
- avisos relacionados con tareas y objeciones.

## Base de datos
La tabla `tareas_lead` se amplía para soportar:
- `tipo_actividad`
- `tipo_bloqueo`
- `solucion_bloqueo`
- `descripcion`
- `fecha_final`
- `estado`
- `leida_asignado`

## Lógica de Objeciones
Cuando un lead entra en estado `Objeciones`, se puede crear automáticamente una tarea de objeción si no existe ya otra abierta para ese lead.

## Resultado
El CRM gana una capa operativa diaria real para seguimiento, bloqueo comercial y resolución de objeciones.

---

# FASE 6 — Dashboard y vistas operativas de análisis

## Objetivo
Construir la capa de visualización y análisis del CRM.

## Funcionalidades implementadas
- `dashboard_view`,
- métricas globales del embudo,
- bloque de embudo comercial,
- leads sin contacto reciente,
- análisis de objeciones,
- seguimientos urgentes,
- productividad por usuario,
- mejora funcional y visual de la vista Tareas,
- semáforo KPI de tareas,
- base visual de `listado_view` con filtros previstos.

## Dashboard comercial
Muestra indicadores como:
- total de leads,
- leads ganados,
- leads perdidos,
- leads en objeciones,
- valor del pipeline,
- valor ganado,
- conversión,
- tareas pendientes,
- tareas retrasadas.

## Resultado
La aplicación deja de ser solo operativa y pasa a ofrecer información útil para analizar rendimiento, detectar bloqueos y priorizar trabajo comercial.

---

# FASE 7 — Mejora visual con Tailwind, modo oscuro y objetivos comerciales

## Objetivo
Modernizar la interfaz del CRM sin romper la base CSS existente.

## Bloques principales trabajados
- integración progresiva de Tailwind,
- modo claro / oscuro,
- sistema de toast centralizado,
- limpieza de mensajes flash antiguos,
- cabeceras privadas mejoradas,
- footer global,
- página de privacidad,
- mejora del formulario de edición en Tareas,
- ajustes visuales en dark mode,
- enlace desde Tareas al detalle del lead,
- selector visual de estado en Panel,
- avance automático de estado al registrar actividad,
- Dashboard mejorado,
- bloque de Objetivos del Mes,
- lógica de `lead_score`,
- toast comercial de felicitación.

## Objetivos del Mes
El Dashboard incorpora un bloque que muestra:
- leads ganados este mes,
- referencia del mes anterior,
- porcentaje de progreso,
- indicador visual tipo semáforo.

## Lógica actual de `lead_score`
- si un lead pasa a **Ganado** → `lead_score = 1`
- si un lead pasa a **Perdido** → `lead_score = 0`

## Notificaciones de felicitación
Cuando un lead pasa a `Ganado`, se puede mostrar un toast comercial con:
- mensaje corto,
- imagen del responsable asignado,
- enlace a detalle del lead,
- comportamiento acumulable y visible una sola vez por usuario.

## Resultado
La fase 7 deja la interfaz mucho más madura, moderna, coherente y preparada para seguir creciendo.

---

## 8. Estado actual del proyecto

A cierre de la fase 7, PipelineDesk dispone de:

- arquitectura MVC completa,
- autenticación real,
- gestión completa de leads,
- ficha de detalle con histórico y notas,
- pipeline Kanban,
- vista operativa de tareas,
- lógica de objeciones,
- dashboard comercial,
- sistema de notificaciones moderno,
- modo oscuro,
- footer corporativo,
- política de privacidad,
- objetivos comerciales mensuales,
- mejoras visuales avanzadas.

En conjunto, el proyecto ya funciona como un CRM comercial bastante completo para el alcance previsto.

---

## 9. Aprendizajes principales del proyecto

A lo largo del desarrollo se han trabajado y consolidado estos aprendizajes:

- organización real de un proyecto MVC,
- validación backend y control de formularios,
- uso de sesiones y autenticación,
- trabajo con MySQL y consultas agregadas,
- construcción de CRUD reales,
- trazabilidad comercial mediante historial,
- interfaz operativa con tablas, tarjetas y paneles,
- actualización de estado en formularios tradicionales y por AJAX,
- mejora progresiva de frontend sin perder estabilidad,
- separación entre lógica de negocio, acceso a datos y presentación.

---

## 10. Cómo ejecutar el proyecto

### Requisitos
- XAMPP
- Apache activo
- MySQL activo
- PHP
- Visual Studio Code

### Pasos recomendados
1. Colocar el proyecto dentro de `htdocs`.
2. Abrirlo en Visual Studio Code.
3. Iniciar Apache y MySQL desde XAMPP.
4. Ejecutar los SQL de `database/migrations` o del script de base de datos del proyecto.
5. Abrir la aplicación en el navegador.

### Ejemplo de URL local
```text
http://localhost/php/crm-pipeline/public/
```

---

## 11. Flujo de trabajo recomendado

El proyecto está planteado para evolucionar de forma incremental:

1. implementar,
2. probar,
3. hacer commit,
4. hacer push,
5. continuar con la siguiente mejora.

Este enfoque ha sido clave para mantener estabilidad durante el desarrollo por fases.

---

## 12. Autoría y contexto académico

Proyecto desarrollado por **Sergio Pavón Díaz** como parte de **Proyecto 2ºDAW**.

PipelineDesk no solo ha servido para construir una aplicación funcional, sino también para comprender cómo se diseña, organiza y evoluciona un CRM real en PHP MVC.

---

## 13. Conclusión general

PipelineDesk ha evolucionado desde una estructura base MVC hasta una aplicación comercial completa con autenticación, gestión de leads, detalle e historial, pipeline Kanban, tareas, objeciones, dashboard, objetivos y mejora visual avanzada.

La evolución por fases ha permitido construir una base técnica sólida y, al mismo tiempo, incorporar una capa de presentación moderna, usable y apta para una defensa académica y una demostración funcional convincente.
