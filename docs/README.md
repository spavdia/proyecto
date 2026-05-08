# PipelineDesk

CRM Pipeline desarrollado con **PHP**, **MySQL**, **HTML5**, **CSS3** y **JavaScript**, siguiendo una arquitectura **MVC** y pensado para ejecutarse en **Windows + XAMPP + Visual Studio Code**.

El proyecto está orientado a construir un sistema de gestión comercial con foco en:

- usuarios y autenticación
- leads y pipeline de ventas
- seguimiento comercial
- tareas y objeciones
- estadísticas y dashboard
- accesibilidad, usabilidad y rendimiento
- presentación visual del proyecto ante profesores

---

## 1. Objetivo del proyecto

**PipelineDesk** es un CRM Pipeline pensado para organizar el trabajo comercial y representar la evolución de los leads dentro de un embudo de ventas.

La idea del proyecto es construir una aplicación que permita:

- iniciar sesión con usuarios reales
- trabajar con una base de datos MySQL real
- gestionar leads y su estado dentro del pipeline
- registrar notas, objeciones y tareas
- generar estadísticas e indicadores
- tener una interfaz clara, usable y presentable

---

## 2. Tecnologías usadas

### Backend
- PHP
- Arquitectura MVC
- Sesiones PHP
- Base de datos MySQL
- Acceso a datos con clase `Database`

### Frontend
- HTML5
- CSS3
- JavaScript puro
- Más adelante: jQuery para estadísticas interactivas
- Más adelante: Tailwind como mejora visual avanzada

### Entorno de desarrollo
- Windows
- XAMPP
- Visual Studio Code
- Git y GitHub

---

## 3. Estado actual del proyecto

Actualmente el proyecto tiene completadas estas partes:

- **Fase 0**
  - estructura base MVC
  - router
  - controlador inicial
  - vistas base
  - layouts
  - estructura de carpetas del proyecto
  - conexión preparada con MySQL

- **Fase 1**
  - base de datos real
  - tabla `usuarios`
  - login real
  - sesiones
  - protección de rutas
  - logout
  - panel privado básico

- **Mejora visual previa a Fase 2**
  - portada pública con imagen de marca
  - página de login mejorada
  - logo `PipelineDesk`
  - layouts comunes
  - CSS común y CSS específico por vista
  - visor de diapositivas para presentación del proyecto

---

## 4. Estructura general del proyecto

```text
crm-pipeline/
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
│       ├── errors/
│       ├── home/
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
└── README.md
```

---

## 5. Explicación rápida de carpetas y archivos importantes

### `public/index.php`
Punto de entrada de la aplicación.

Su función es arrancar el proyecto cargando:
- autoload
- configuración
- rutas

### `routes/web.php`
Define las rutas web de la aplicación.

Ejemplos:
- `/`
- `/login`
- `/panel`
- `/logout`

### `lib/Route.php`
Gestiona el enrutado y decide qué controlador se ejecuta para cada URL.

### `lib/config.php`
Define constantes globales como:
- `APP_ROOT`
- `BASE_URL`

### `lib/Database.php`
Gestiona la conexión y consultas a base de datos.

### `lib/SessionManager.php`
Gestiona:
- inicio de sesión
- cierre de sesión
- datos de usuario autenticado
- mensajes flash

### `app/controllers/`
Contiene los controladores, que coordinan la lógica entre rutas, modelos y vistas.

### `app/models/`
Contiene los modelos, que se encargan de hablar con la base de datos.

### `app/views/`
Contiene las vistas HTML/PHP.

Organización actual:
- `errors` → errores
- `home` → vistas principales y centrales
- `layouts` → partes comunes como header y footer

---

## 6. Fase 0

### Objetivo
Construir la base técnica del proyecto.

### Qué se implementó
- estructura de carpetas MVC
- router funcional
- `HomeController`
- vista inicial
- layouts comunes
- CSS y JS base
- base de datos preparada para evolucionar

### Flujo básico de Fase 0

```text
Navegador
→ public/index.php
→ routes/web.php
→ Route
→ HomeController
→ vista
→ HTML al navegador
```

### Métodos clave en esta fase
- `Route::get()`
- `Route::post()`
- `Route::handleRoute()`
- `Controller::view()`

### Resultado
La aplicación ya podía servir una vista pública y tenía una arquitectura preparada para crecer.

---

## 7. Fase 1

### Objetivo
Implementar acceso real al sistema.

### Qué se implementó
- base de datos MySQL real
- tabla `usuarios`
- usuario admin inicial
- login real
- validación contra base de datos
- uso de `password_hash` y `password_verify`
- sesión de usuario
- ruta privada protegida
- logout

### Tabla principal de esta fase
- `usuarios`

Campos:
- `id`
- `nombre`
- `email`
- `password_hash`
- `rol`
- `activo`
- `created_at`

### Flujo del login

```text
GET /login
→ LoginController::mostrarLoginForm()
→ login_view

POST /login
→ LoginController::login()
→ LoginModel::findByEmail()
→ Database consulta MySQL
→ password_verify()
→ SessionManager guarda usuario
→ redirect a /panel
```

### Flujo de protección de rutas

```text
GET /panel
→ HomeController::panel()
→ SessionManager comprueba si hay usuario
→ si no hay sesión: redirect a /login
→ si hay sesión: carga panel_view
```

### Métodos clave en Fase 1

#### `LoginController`
- `mostrarLoginForm()`
- `login()`
- `logout()`

#### `HomeController`
- `index()`
- `panel()`

#### `LoginModel`
- `findByEmail()`

#### `SessionManager`
- `iniciarSesion()`
- `set()`
- `get()`
- `eliminar()`
- `destruirSesion()`
- `setMensajeFlash()`
- `getMensajeFlash()`
- `usuarioAutenticado()`
- `usuarioNoAutenticado()`

### Resultado
La aplicación ya tiene una puerta de entrada real al CRM.

---

## 8. Mejora visual antes de Fase 2

Antes de continuar con leads, se ha mejorado el aspecto visual de la aplicación para dejar una base más profesional.

### Vistas trabajadas
- `index_view`
- `login_view`

### Decisiones visuales tomadas
- mantener HTML + CSS tradicional
- no introducir Tailwind todavía
- usar clases en español
- crear un CSS específico por vista
- reutilizar layouts comunes
- integrar la marca `PipelineDesk`

### Elementos visuales añadidos en `index_view`
- header con logo
- botón de inicio de sesión
- botón para futura página de formulario de lead
- barra negra con eslogan
- bloque central de presentación
- visor de diapositivas clicable para exposición del proyecto

### Elementos visuales añadidos en `login_view`
- cabecera con logo
- tarjeta central
- formulario más claro
- mensajes visuales integrados
- foco visible
- diseño más limpio y usable

### Archivo JS destacado
- `public/js/index_view.js`

Su función es controlar el pase de diapositivas de la portada.

### Lógica usada para las diapositivas
Las diapositivas se leen desde una `section` concreta de la vista mediante un atributo `data-diapositivas`, y el JavaScript se enlaza a esa sección para:
- avanzar
- retroceder
- responder a clic
- responder a teclado

Esto permite usar la portada como apoyo visual durante la presentación del proyecto.

---

## 9. Criterios de usabilidad y accesibilidad aplicados

Desde estas primeras fases se ha intentado trabajar con:

- etiquetas correctas en formularios
- inputs grandes y claros
- botones visibles
- contraste razonable
- foco visible para teclado
- estructura semántica
- navegación simple
- responsive básico
- mensajes comprensibles

Estos criterios se ampliarán más adelante en las fases finales del proyecto.

---

## 10. Próximas fases previstas

### Fase 2
- leads
- CRUD básico
- listados
- formularios accesibles
- pipeline base

### Fase 3
- notas
- histórico
- cambios de estado
- reglas de negocio del embudo

### Fase 4
- tablas por estado del pipeline
- drag and drop
- actualización visual y persistencia del estado

### Fase 5
- tareas
- objeciones
- productividad comercial

### Fase 6
- dashboard
- estadísticas
- jQuery
- gráficos interactivos
- multimedia útil para análisis

### Fase 7
- accesibilidad avanzada
- usabilidad
- pruebas cross-browser
- optimización final
- cierre técnico
- posible mejora con Tailwind

---

## 11. Cómo arrancar el proyecto

### Requisitos
- XAMPP
- Apache activo
- MySQL activo
- PHP
- VS Code

### Pasos generales
1. Colocar el proyecto dentro de `htdocs`
2. Abrirlo en VS Code
3. Iniciar Apache y MySQL desde XAMPP
4. Ejecutar los SQL de `database/migrations`
5. Abrir la aplicación en navegador

Ejemplo de URL local:

```text
http://localhost/php/crm-pipeline/public/
```

---

## 12. Git y trabajo por fases

El proyecto está pensado para evolucionar fase por fase.

Flujo recomendado:
- implementar
- probar
- hacer commit
- hacer push
- pasar a la siguiente fase

Esto permite mantener el proyecto estable y facilitar el trabajo entre varios PCs.

---

## 13. Marca del proyecto

Nombre de la aplicación:
**PipelineDesk**

La identidad visual actual usa:
- logo propio
- portada con branding
- login coherente con la marca
- lenguaje visual pensado para presentación académica

---

## 14. Resumen final

PipelineDesk ya tiene una base sólida:

- estructura MVC
- base de datos real
- login funcional
- sesiones
- ruta privada
- layouts
- vistas mejoradas
- branding
- soporte visual para presentación

El siguiente gran bloque del proyecto será la gestión de **leads** y el inicio del **pipeline comercial real**.
