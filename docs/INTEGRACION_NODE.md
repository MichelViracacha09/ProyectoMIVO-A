# Integración MIVO (CodeIgniter 4) con el módulo técnico (Node.js)

Este documento describe lo que la aplicación principal ya ofrece y lo que haría falta acordar en el grupo. El módulo técnico lo desarrolla otro integrante; aquí no se implementa.

## Lo que ofrece hoy la aplicación principal

**API REST de solo lectura** (`app/Controllers/Api`, rutas en `app/Config/Routes.php`):

| Método | Ruta | Acceso |
|---|---|---|
| POST | `/api/login` `{correo, password}` | Público. Devuelve `token` (vence en 1 h). |
| GET | `/api/citas`, `/api/citas/{id}` | Administrador: todas. Cliente: solo las suyas. |
| GET | `/api/motos`, `/api/motos/{id}` | Administrador: todas. Cliente: solo las suyas. |
| GET | `/api/clientes`, `/api/clientes/{id}` | Administrador: todos. Cliente: solo el propio. |

Token: HS256 (formato JWT). Clave en `.env` (`api.tokenSecret`). Si Node valida el token por su cuenta debe usar la misma clave, que se comparte fuera de Git. Claims: `user_id`, `id_rol`, `id_cliente` o `id_administrador`, `iat`, `exp`.

**Autorización web**: tabla `modulo_rol` (ver, crear, editar, eliminar) validada en el servidor con el filtro `permiso:<modulo>,<accion>`. Usuarios, Roles y Permisos son exclusivos del rol Administrador (filtro `role:admin`).

## Brechas del esquema frente a la integración (requieren decisión del grupo)

El diccionario de datos vigente no define lo siguiente, por lo que no se creó:

1. **Perfil técnico.** No existe la tabla `tecnico`. Hoy un usuario con rol Técnico (id_rol 2) no tiene correo ni nombre, así que no puede iniciar sesión por el flujo actual (el login busca el correo en `administrador` y `cliente`).
2. **Técnico asignado a la cita.** `citas` no tiene `id_tecnico`.
3. **Moto en la cita.** `citas` no tiene `id_moto`, por eso no se puede validar que la moto de la cita pertenezca al cliente.
4. **Historial normalizado.** `historial_de_servicios` guarda cliente, técnico, placa y servicio como texto. Solo tiene llave foránea a `citas`.
5. **Estado activo/inactivo** en `servicio`, `moto` y `rol`: no existe la columna.

Propuesta, sujeta a aprobación y a actualizar el diccionario antes de ejecutarla:

- Crear `tecnico` (`id_tecnico`, `nombre_completo`, `correo`, `numero_celular`, `id_usuario` único con FK a `usuario`). El perfil solo se asocia a usuarios con rol Técnico.
- Agregar `citas.id_tecnico` e `citas.id_moto` (FK, nulos al inicio para no romper datos existentes).
- Agregar a `historial_de_servicios` las columnas `id_tecnico` e `id_moto` (FK, nulas), conservando las columnas de texto actuales.
- Migraciones aditivas y reversibles. No se borra ni se modifica ningún dato.

## Reglas de negocio vigentes que Node debe respetar

- Estados de cita (diccionario): `PENDIENTE`, `CONFIRMADA`, `COMPLETADA`, `CANCELADA`. Una cita `COMPLETADA` o `CANCELADA` no se puede cancelar.
- Cupo: máximo 1 cita activa por fecha y hora (`CitaModel::MAX_POR_FRANJA`).
- Un cliente `Inactivo` o `Bloqueado` (`estado_cliente`) no inicia sesión ni agenda citas.
- Las contraseñas se guardan con `password_hash` (bcrypt) en `usuario.hash`. Node debe verificar con bcrypt y nunca escribir contraseñas en texto plano.
