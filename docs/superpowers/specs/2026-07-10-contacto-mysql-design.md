# Diseño: Formulario de contacto con almacenamiento en MySQL

**Fecha:** 2026-07-10
**Estado:** Aprobado

## Objetivo

Los mensajes enviados desde el formulario de contacto de `ASPTI.html` (nombre, correo, mensaje) deben guardarse en una base de datos MySQL/MariaDB local. Hoy el formulario no envía datos a ningún lado. El sitio sigue siendo HTML/CSS/JS; no se necesita página de administración: los mensajes se consultan por consola de MySQL.

## Arquitectura

Un único servidor Node.js con Express con dos responsabilidades:

1. **Servir el sitio estático** existente (`ASPTI.html`, `APSTI.css`, `img/`, `logo1.png`) en `http://localhost:3000`. Al servir frontend y API desde el mismo origen se evita CORS.
2. **Exponer `POST /api/contacto`**, que valida el cuerpo JSON `{ nombre, correo, mensaje }` y lo inserta en MySQL con consultas parametrizadas (librería `mysql2/promise`, pool de conexiones).

Flujo: usuario llena el formulario → JS intercepta el `submit` → `fetch POST /api/contacto` → Express valida e inserta en la tabla `mensajes` → responde JSON → el frontend muestra confirmación o error sin recargar la página.

## Base de datos

Base `aspti`, tabla `mensajes`:

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `INT UNSIGNED AUTO_INCREMENT` | Clave primaria |
| `nombre` | `VARCHAR(100) NOT NULL` | |
| `correo` | `VARCHAR(150) NOT NULL` | |
| `mensaje` | `TEXT NOT NULL` | |
| `fecha_envio` | `TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP` | Se llena automáticamente |

`db/schema.sql` crea la base, la tabla y un usuario dedicado `aspti_user` (con permisos solo sobre `aspti`), para no usar root desde la aplicación. Se ejecuta una sola vez: `sudo mariadb < db/schema.sql`.

Las credenciales de conexión viven en `.env` (cargado con `dotenv`); `.env` queda excluido de git y `.env.example` documenta las variables: `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`, `PORT`.

## Cambios en el frontend (`ASPTI.html`)

- Inputs del formulario reciben `name="nombre"`, `name="correo"`, `name="mensaje"` y `required`.
- Un `<script>` al final del documento intercepta el `submit`, envía los datos con `fetch` y muestra el resultado en un elemento de estado dentro de la sección de contacto ("Mensaje enviado ✓" en éxito; mensaje de error en fallo). En éxito, limpia el formulario.
- El diseño visual existente no cambia.

## Validación y manejo de errores

En el servidor (la validación del navegador es solo conveniencia):

- `nombre`: no vacío tras `trim`, máximo 100 caracteres.
- `correo`: no vacío, formato básico de email, máximo 150 caracteres.
- `mensaje`: no vacío tras `trim`, máximo 5000 caracteres.
- Entrada inválida → `400` con `{ error: "<motivo>" }`.
- Error de base de datos → `500` con mensaje genérico (el detalle solo al log del servidor).
- Inserción correcta → `201` con `{ ok: true }`.

## Archivos

| Archivo | Acción |
|---|---|
| `server.js` | Nuevo — Express: estáticos + endpoint |
| `db/schema.sql` | Nuevo — base, tabla, usuario |
| `package.json` | Nuevo — deps: `express`, `mysql2`, `dotenv` |
| `.env` / `.env.example` | Nuevo — credenciales / plantilla |
| `.gitignore` | Nuevo — excluye `node_modules/`, `.env` |
| `ASPTI.html` | Modificado — atributos del form + script de envío |

## Verificación

1. `node server.js` y abrir `http://localhost:3000/ASPTI.html`.
2. Enviar el formulario con datos de prueba y ver la confirmación en pantalla.
3. `mariadb -u aspti_user -p aspti -e "SELECT * FROM mensajes;"` debe mostrar el registro.
4. Casos de error: envío con campos vacíos o correo inválido vía `curl` debe responder `400`.
