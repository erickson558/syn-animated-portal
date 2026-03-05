# Syn Animated Portal

Portal web en PHP para centralizar accesos a herramientas locales con una interfaz moderna, animaciones y buscador en tiempo real.

## Version actual

`V1.1.0`

## Caracteristicas

- Interfaz animada con efectos visuales (particulas, glow, tilt 3D, confetti, ripple, spotlight)
- Busqueda por nombre, URL o descripcion con resaltado
- Ordenamiento A-Z / Z-A
- Tema claro/oscuro persistente
- Modo de densidad de tarjetas (Confort / Compacta)
- Recarga de tarjetas por AJAX desde `sites.json`
- Atajos de teclado y accesibilidad basica

## Requisitos

- PHP 5.4+ (compatible con EasyPHP)
- Navegador moderno con JavaScript habilitado
- Conexion a internet para cargar CDNs:
  - Bootstrap 5.3.2
  - Bootstrap Icons 1.10.5
  - Animate.css 4.1.1
  - AOS 2.3.4
  - particles.js 2.0.0

## Estructura minima

- `index.php`: aplicacion principal
- `VERSION`: version canonica del proyecto
- `CHANGELOG.md`: historial de cambios
- `LICENSE`: licencia Apache 2.0

## Ejecucion local

1. Coloca el proyecto en tu servidor local (por ejemplo EasyPHP).
2. Abre en navegador la ruta donde esta `index.php`.
3. Ajusta el array `$sites` en `index.php` para agregar o editar accesos.

## Versionado (SemVer con prefijo V)

Este proyecto usa versionado `Vx.y.z`.

- `x` (major): cambios incompatibles
- `y` (minor): funcionalidades nuevas compatibles
- `z` (patch): correcciones o mejoras menores

Regla de trabajo recomendada:

1. Actualizar `VERSION`
2. Reflejar la misma version en la app (variable `$appVersion`)
3. Agregar entrada en `CHANGELOG.md`
4. Hacer commit con mensaje claro
5. Publicar a `main`
6. Crear tag Git de la misma version

Ejemplo:

```bash
git add .
git commit -m "feat: nueva animacion de cabecera"
git tag V1.1.0
git push origin main --tags
```

## Buenas practicas en GitHub

- Mantener `README.md`, `VERSION` y `CHANGELOG.md` sincronizados
- Un commit por cambio logico
- Mensajes tipo Conventional Commits (`feat:`, `fix:`, `docs:`)
- Version/tag por cada commit publicado

## Licencia

Apache License 2.0. Ver archivo `LICENSE`.
