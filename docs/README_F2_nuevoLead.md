# PipelineDesk CRM

PipelineDesk es un proyecto CRM desarrollado en **PHP** con arquitectura **MVC**, orientado a la gestión de leads dentro de un embudo comercial.

El objetivo del proyecto es construir una aplicación web clara, usable y ampliable, con una parte pública de captación de leads y una parte privada para la gestión interna comercial.

---

## Estado actual del proyecto

En este momento están completadas:

- **Fase 0**: base del proyecto MVC
- **Fase 1**: usuarios, login, sesiones y protección de rutas
- **Fase 2**: leads, formularios de entrada y panel por estados

La siguiente fase será la **Fase 3**, centrada en notas, histórico y reglas de negocio del embudo.

---

## Tecnologías usadas

### Backend
- PHP
- Arquitectura MVC
- Programación orientada a objetos
- MySQL
- XAMPP

### Frontend
- HTML5
- CSS
- JavaScript puro

### Entorno de trabajo
- Visual Studio Code
- Git y GitHub
- Windows

---

## Estructura general del proyecto

```text
crm-pipeline/
├── app/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   │   ├── home/
│   │   ├── lead/
│   │   └── layouts/
├── database/
│   └── migrations/
├── lib/
├── public/
│   ├── css/
│   ├── js/
│   └── img/
├── routes/
└── storage/
```

---

## Filosofía del proyecto

El proyecto está organizado para aprender cómo se conecta cada capa:

- **Ruta**: decide qué controlador se ejecuta
- **Controller**: recibe la petición, valida, coordina el flujo y llama al modelo o a la vista
- **Model**: trabaja con la base de datos
- **View**: pinta HTML y muestra datos al usuario

La idea es mantener el código ordenado y separar bien responsabilidades.

---

# Fase 0 completada

La Fase 0 dejó preparada la base del proyecto.

## Objetivos de la Fase 0
- estructura MVC inicial
- configuración base
- rutas funcionando
- layouts comunes
- CSS y JS preparados
- identidad visual inicial del proyecto

## Elementos clave
- `public/index.php`: punto de entrada
- `routes/web.php`: registro de rutas
- `lib/Route.php`: resolución de rutas
- `layouts/header.php` y `layouts/footer.php`: estructura común
- `style.css`: estilos generales del proyecto

---

# Fase 1 completada

La Fase 1 se centró en la autenticación.

## Objetivos de la Fase 1
- conexión real con base de datos
- tabla `usuarios`
- login funcional
- sesiones
- logout
- protección de rutas privadas

## Funcionalidades implementadas
- formulario de login
- validación básica del formulario
- sticky form en login
- mensajes flash para errores globales y éxitos
- rutas protegidas para evitar acceso sin sesión

## Archivos clave de Fase 1
- `LoginController.php`
- `LoginModel.php`
- `SessionManager.php`
- `login_view.php`

## Flujo del login
```text
GET /login
→ mostrar formulario

POST /login
→ validar campos
→ buscar usuario por email
→ comprobar password
→ guardar usuario en sesión
→ redirigir a /panel
```

---

# Fase 2 completada

La Fase 2 convierte el proyecto en un CRM funcional inicial.

## Objetivos de la Fase 2
- crear el módulo de leads
- permitir la entrada de leads desde dos puntos
- mostrar los leads agrupados por estado en el panel
- permitir cambio de estado desde el panel

---

## Tabla principal de Fase 2

La tabla `leads` almacena la información principal del embudo comercial.

### Campos principales
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

### Estados del embudo
- Nuevo Lead
- Contactado
- En Progreso
- Objeciones
- Ganado
- Perdido

### Constantes por defecto
En el proyecto ya se han definido valores por defecto como:
- usuario por defecto
- prioridad por defecto
- estado inicial por defecto

---

## Dos entradas de leads

### 1. Entrada pública
Desde la ruta pública de contacto:

```text
/contacto
```

Esta vista simula la página de contacto de una academia o centro de estudios.

El visitante puede enviar:
- nombre
- email
- teléfono
- servicio
- indicaciones

Ese formulario crea un lead nuevo en la aplicación.

### 2. Entrada interna
Desde la ruta privada:

```text
/leads/nuevo
```

Un usuario autenticado puede crear un lead desde el propio CRM.

En esta vista interna ya se puede indicar:
- nombre del lead
- email
- teléfono
- servicio
- responsable
- estado
- prioridad
- valor
- indicaciones

---

## Validación de formularios

En la Fase 2 se ha establecido una norma importante para el proyecto:

### Regla usada en formularios
- se usa `errores = []`
- cada campo se valida en el controller
- los errores se muestran debajo de cada input con `error-campo`
- se usa sticky form
- los mensajes flash se reservan para operaciones de base de datos o mensajes globales

### Qué significa sticky form
Cuando el usuario envía un formulario con errores:
- no pierde lo que ha escrito
- la vista vuelve a cargar los datos ya introducidos

Esto mejora mucho la experiencia de uso.

---

## Panel principal

El panel es la vista central del CRM en la Fase 2.

### Funciones del panel
- mostrar leads agrupados por estado
- mostrar información comercial útil
- permitir cambio de estado desde la misma tabla
- servir como punto principal de trabajo del CRM

### Columnas actuales del panel
- Lead
- Responsable
- Estado
- Servicio
- Teléfono
- Indicaciones
- Último contacto
- Origen

### Elementos visuales importantes
- estado visible por color
- servicios en formato etiqueta
- borde lateral del lead con el color del estado
- menú lateral responsive
- selector de estado integrado en la tabla

---

## Cambio de estado desde el panel

Uno de los puntos clave de Fase 2 es que el lead puede avanzar entre estados desde el propio panel.

### Lógica general
Cada fila del panel contiene un formulario pequeño con un selector de estado.

Cuando el usuario cambia el estado:
- el navegador envía un `POST`
- el controller valida el estado
- el model actualiza la base de datos
- el panel se recarga
- el lead aparece en la tabla del nuevo estado

### Flujo
```text
Panel
→ cambiar selector de estado
→ enviar POST
→ LeadController::cambiarEstado()
→ LeadModel::updateEstado()
→ redirección a /panel
→ lead reagrupado en su nuevo estado
```

---

## Controllers principales del proyecto

### HomeController
Responsable de:
- mostrar la portada
- mostrar el panel

### LoginController
Responsable de:
- mostrar login
- validar acceso
- iniciar sesión
- cerrar sesión

### LeadController
Responsable de:
- mostrar formulario público de contacto
- guardar leads desde contacto
- mostrar formulario interno de nuevo lead
- guardar leads internos
- cambiar estado desde el panel

---

## Modelos principales

### LeadModel
Responsable de:
- crear leads
- obtener listas de servicios
- obtener estados válidos
- obtener responsables válidos
- agrupar leads por estado
- actualizar estado

### LoginModel
Responsable de:
- buscar usuarios
- apoyar la lógica de autenticación

---

## Rutas principales actuales

### Públicas
- `/`
- `/login`
- `/contacto`

### Privadas
- `/panel`
- `/leads/nuevo`
- `/logout`

### Acciones POST
- `POST /login`
- `POST /contacto`
- `POST /leads/guardar`
- `POST /leads/cambiar-estado/{id}`

---

## Seguridad aplicada

### Sesión
Las rutas privadas requieren sesión iniciada.

### Protección de acceso
Si el usuario no está autenticado:
- no puede entrar en panel
- no puede crear leads internos
- no puede ejecutar cambios internos

### Validación backend
Toda validación importante se hace en PHP, no solo en frontend.

### Datos que no deben guardarse en sesión
En general, no se deben guardar en sesión:
- contraseñas
- hashes de contraseña
- datos innecesarios del formulario
- información sensible sin necesidad

La sesión debe usarse solo para:
- usuario autenticado
- datos mínimos de contexto
- mensajes flash

---

## Frontend de la Fase 2

### Vistas trabajadas
- `index_view.php`
- `login_view.php`
- `contacto_view.php`
- `lead_create_view.php`
- `panel_view.php`

### CSS por vista
El proyecto usa archivos CSS por vista para mantener orden:
- `style.css`
- `index.css`
- `login.css`
- `contacto.css`
- `lead_create.css`
- `panel.css`

### Responsive
Ya existe una base responsive para:
- escritorio
- escritorio reducido
- tablet
- móvil

El panel mantiene el aside visible en escritorio y cambia a menú hamburguesa en tablet y móvil.

---

## JavaScript usado hasta ahora

En Fase 2 el JavaScript se ha usado de forma contenida y concreta.

### Usos principales
- menú hamburguesa del panel
- envío automático del formulario del selector de estado

### Idea del proyecto
Usar JavaScript solo cuando aporta una mejora concreta y controlada, manteniendo la lógica principal en backend.

---

## Qué se ha aprendido en Fase 2

Esta fase ha servido para entender y practicar:

- cómo construir formularios en PHP MVC
- cómo validar datos en controller
- cómo usar sticky form
- cómo separar errores de validación de mensajes flash
- cómo insertar datos en base de datos
- cómo mostrar datos reales en una vista
- cómo agrupar datos por estados
- cómo actualizar registros desde una tabla
- cómo conectar backend y frontend en un CRM real

---

## Estado funcional actual

En este punto ya se puede:

- iniciar sesión
- entrar al panel
- crear leads desde contacto
- crear leads desde la app
- ver leads agrupados por estado
- cambiar el estado de un lead desde el panel

Esto deja una base real y usable para continuar creciendo el proyecto.

---

# Siguiente paso: Fase 3

La siguiente fase se centrará en:

- notas
- histórico
- cambio de estados con más contexto
- reglas de negocio del embudo

La Fase 3 hará que cada lead deje de ser solo un registro y pase a tener trazabilidad comercial real.

---

## Autoría y objetivo académico

Este proyecto está pensado como aprendizaje progresivo para construir un CRM completo en PHP MVC, entendiendo cada archivo, cada flujo y cada decisión de backend y frontend.

La meta no es solo que funcione, sino aprender:
- cómo se organiza un proyecto
- cómo se valida
- cómo se estructura MVC
- cómo se conecta una aplicación real con base de datos
- cómo evolucionar una aplicación por fases

---
