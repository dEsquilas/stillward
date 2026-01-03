# Stillward — TODO & Roadmap

Este documento contiene todas las tareas necesarias para desarrollar Stillward, organizadas por fases. Cada fase incluye un checklist de pruebas.

---

## Fase 0: Setup Inicial con Laravel Sail

### Tareas

#### 0.1 Inicialización del proyecto

- [x] Inicializar proyecto Laravel con Sail
  ```bash
  curl -s "https://laravel.build/stillward?with=mysql,redis" | bash
  cd stillward
  ```
- [x] Configurar Git e inicializar repositorio
  ```bash
  git init
  git add .
  git commit -m "chore: initial Laravel Sail setup"
  ```
- [x] Levantar entorno con Docker
  ```bash
  ./vendor/bin/sail up -d
  ```
- [x] Configurar alias de Sail (añadir a `~/.zshrc` o `~/.bashrc`)
  ```bash
  alias sail='./vendor/bin/sail'
  ```

#### 0.2 Scaffolding Vue + Inertia

- [x] Instalar Inertia.js (lado servidor)
  ```bash
  sail composer require inertiajs/inertia-laravel
  ```
- [x] Crear middleware de Inertia
  ```bash
  sail artisan inertia:middleware
  ```
- [x] Registrar middleware en `bootstrap/app.php`:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->web(append: [
          \App\Http\Middleware\HandleInertiaRequests::class,
      ]);
  })
  ```
- [x] Crear template blade `resources/views/app.blade.php`:
  ```blade
  <!DOCTYPE html>
  <html>
    <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
      @vite(['resources/js/app.js', 'resources/css/app.css'])
      @inertiaHead
    </head>
    <body>
      @inertia
    </body>
  </html>
  ```
- [x] Instalar dependencias de frontend
  ```bash
  sail npm install vue@latest @inertiajs/vue3 @vitejs/plugin-vue
  ```
- [x] Configurar Vite (`vite.config.js`):
  ```js
  import { defineConfig } from 'vite';
  import laravel from 'laravel-vite-plugin';
  import vue from '@vitejs/plugin-vue';

  export default defineConfig({
      plugins: [
          laravel({
              input: ['resources/css/app.css', 'resources/js/app.js'],
              refresh: true,
          }),
          vue({
              template: {
                  transformAssetUrls: {
                      base: null,
                      includeAbsolute: false,
                  },
              },
          }),
      ],
      resolve: {
          alias: {
              '@': '/resources/js',
          },
      },
  });
  ```
- [x] Configurar app.js (`resources/js/app.js`):
  ```js
  import { createApp, h } from 'vue';
  import { createInertiaApp } from '@inertiajs/vue3';
  import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

  createInertiaApp({
      resolve: (name) => resolvePageComponent(
          `./Pages/${name}.vue`,
          import.meta.glob('./Pages/**/*.vue')
      ),
      setup({ el, App, props, plugin }) {
          createApp({ render: () => h(App, props) })
              .use(plugin)
              .mount(el);
      },
  });
  ```
- [x] Crear estructura de carpetas para Vue:
  ```bash
  mkdir -p resources/js/Pages
  mkdir -p resources/js/Components
  mkdir -p resources/js/Layouts
  ```
- [x] Crear página de prueba `resources/js/Pages/Welcome.vue`:
  ```vue
  <script setup>
  defineProps({
      message: String,
  });
  </script>

  <template>
      <div>
          <h1>{{ message }}</h1>
      </div>
  </template>
  ```
- [x] Actualizar ruta en `routes/web.php`:
  ```php
  use Inertia\Inertia;

  Route::get('/', function () {
      return Inertia::render('Welcome', [
          'message' => 'Hello from Inertia + Vue!'
      ]);
  });
  ```

#### 0.3 Tailwind CSS

- [x] Instalar Tailwind CSS
  ```bash
  sail npm install -D tailwindcss postcss autoprefixer
  sail npx tailwindcss init -p
  ```
- [x] Configurar `tailwind.config.js`:
  ```js
  /** @type {import('tailwindcss').Config} */
  export default {
      content: [
          './resources/**/*.blade.php',
          './resources/**/*.js',
          './resources/**/*.vue',
      ],
      theme: {
          extend: {
              colors: {
                  'body-mind': '#10B981',
                  'money-work': '#F59E0B',
                  'growth': '#8B5CF6',
                  'life': '#EC4899',
              },
          },
      },
      plugins: [],
  };
  ```
- [x] Configurar `resources/css/app.css`:
  ```css
  @tailwind base;
  @tailwind components;
  @tailwind utilities;
  ```

#### 0.4 Herramientas de calidad de código

- [x] Instalar ESLint + Prettier
  ```bash
  sail npm install -D eslint prettier eslint-plugin-vue @vue/eslint-config-prettier
  ```
- [x] Crear `.eslintrc.cjs`:
  ```js
  module.exports = {
      root: true,
      env: {
          node: true,
      },
      extends: [
          'plugin:vue/vue3-recommended',
          '@vue/eslint-config-prettier',
      ],
      rules: {
          'vue/multi-word-component-names': 'off',
      },
  };
  ```
- [x] Crear `.prettierrc`:
  ```json
  {
      "semi": true,
      "singleQuote": true,
      "tabWidth": 4,
      "trailingComma": "es5"
  }
  ```
- [x] Instalar PHPStan
  ```bash
  sail composer require --dev phpstan/phpstan
  ```
- [x] Crear `phpstan.neon`:
  ```neon
  parameters:
      level: 8
      paths:
          - app
  ```
- [x] Añadir scripts a `package.json`:
  ```json
  "scripts": {
      "lint": "eslint resources/js --ext .js,.vue",
      "lint:fix": "eslint resources/js --ext .js,.vue --fix",
      "format": "prettier --write resources/js/**/*.{js,vue}"
  }
  ```

#### 0.5 Verificación final

- [x] Ejecutar migrations iniciales (`sail artisan migrate`)
- [x] Compilar assets (`sail npm run dev`)
- [x] Verificar que todo funciona

### Checklist de Pruebas

- [x] `sail up` levanta contenedores sin errores
- [x] `sail artisan serve` no es necesario (ya corre en el contenedor)
- [x] Acceso a `http://localhost` muestra página de Laravel
- [x] `sail npm run dev` compila assets sin errores
- [x] Hot reload funciona (cambiar Vue component y ver cambio)
- [x] `sail artisan test` pasa (tests por defecto de Laravel)
- [x] Conexión a MySQL funciona (`sail artisan migrate`)
- [x] Acceso a MySQL desde host (`localhost:3306`)
- [x] Redis funciona (`sail artisan tinker` -> `Cache::put('test', 'ok')`)

---

## Fase 1: Autenticación con Google

### Tareas

- [x] Instalar Laravel Socialite (`sail composer require laravel/socialite`)
- [x] Crear proyecto en Google Cloud Console
- [x] Configurar OAuth 2.0 credentials (Client ID, Client Secret)
- [x] Añadir variables de entorno para Google OAuth en `.env`
- [x] Crear rutas de autenticación (`/auth/google`, `/auth/google/callback`)
- [x] Implementar `AuthController` con métodos redirect y callback
- [x] Crear/actualizar modelo `User` con campos necesarios (google_id, avatar, etc.)
- [x] Crear migration para campos adicionales de User
- [x] Implementar lógica de crear usuario si no existe, o login si existe
- [x] Configurar middleware de autenticación
- [x] Crear página de Login con botón "Sign in with Google"
- [x] Implementar logout
- [x] Redirigir usuarios no autenticados a login

### Checklist de Pruebas

- [x] Botón "Sign in with Google" redirige a Google OAuth
- [x] Después de autorizar en Google, vuelve a la app
- [x] Usuario nuevo: se crea registro en BD con datos de Google
- [x] Usuario existente: hace login sin crear duplicado
- [x] Sesión persiste al refrescar página
- [x] Logout cierra sesión correctamente
- [x] Rutas protegidas redirigen a login si no autenticado
- [x] No se puede acceder a `/auth/google/callback` directamente sin código OAuth
- [x] Avatar del usuario se guarda y muestra correctamente

---

## Fase 2: Modelos y Base de Datos

### Tareas

- [x] Diseñar schema de base de datos
- [x] Crear migration para tabla `goals`
  - `id`, `user_id`, `category`, `type`, `title`, `description`
  - `target_value`, `current_value`, `unit`, `currency`
  - `is_completed`, `completed_at`, `is_archived`
  - `created_at`, `updated_at`
- [x] Crear migration para tabla `log_entries`
  - `id`, `user_id`, `goal_id`, `value`, `note`, `created_at`
- [x] Crear modelo `Goal` con relaciones y casts
- [x] Crear modelo `LogEntry` con relaciones
- [x] Definir constantes/enums para categories y goal types
- [x] Implementar accessors para progreso calculado en Goal
- [ ] Crear factories para Goal y LogEntry
- [ ] Crear seeders con datos de ejemplo

### Checklist de Pruebas

- [x] Migrations ejecutan sin errores (`sail artisan migrate:fresh`)
- [ ] Seeders crean datos correctamente (`sail artisan db:seed`)
- [x] Relación User -> Goals funciona
- [x] Relación Goal -> LogEntries funciona
- [x] Relación LogEntry -> Goal funciona
- [x] Cast de `category` a enum funciona
- [x] Cast de `type` a enum funciona
- [x] Accessor de progreso calcula correctamente para cada tipo:
  - [x] Counter: `current_value / target_value`
  - [x] Yes/No: 0% o 100%
  - [x] Percentage: valor directo
  - [x] Money: `current_amount / target_amount`
- [ ] Factory genera datos válidos
- [ ] Soft delete de goals funciona (si se implementa)

---

## Fase 3: Goals Management (Area 1)

### Tareas

#### Backend

- [x] Crear `GoalController` con métodos CRUD
- [x] Implementar `index` - listar goals del usuario agrupados por categoría
- [x] Implementar `store` - crear nuevo goal con validación
- [x] Implementar `show` - ver detalle de un goal
- [x] Implementar `update` - editar goal existente
- [x] Implementar `archive` - archivar goal sin eliminar
- [x] Implementar `restore` - restaurar goal archivado
- [x] Crear Form Requests para validación (`StoreGoalRequest`, `UpdateGoalRequest`)
- [x] Definir rutas en `web.php`
- [ ] Añadir policies para autorización (solo owner puede editar)

#### Frontend

- [x] Crear layout principal con navegación (bottom tabs o sidebar)
- [x] Crear página `Goals/Index.vue` - listado por categorías
- [x] Crear componente `GoalCard.vue` - tarjeta de goal individual (luego eliminado, ahora inline)
- [x] Crear página `Goals/Create.vue` - formulario de creación
- [x] Crear página `Goals/Edit.vue` - formulario de edición
- [x] Crear página `Goals/Show.vue` - detalle de goal
- [x] Implementar selección de categoría con iconos/colores
- [x] Implementar selección de tipo con explicación de cada uno
- [x] Implementar campos condicionales según tipo (unit, currency, target, etc.)
- [x] Añadir confirmación antes de archivar
- [x] Mostrar goals archivados en sección separada

### Checklist de Pruebas

- [x] Lista de goals carga correctamente
- [x] Goals se agrupan por categoría
- [x] Crear goal tipo Counter funciona
- [x] Crear goal tipo Yes/No funciona
- [x] Crear goal tipo Percentage funciona
- [x] Crear goal tipo Money funciona
- [x] Validación de formulario muestra errores
- [x] Campos requeridos no pueden estar vacíos
- [x] Editar goal guarda cambios
- [x] Archivar goal lo mueve a sección archivados
- [x] Restaurar goal lo devuelve a activos
- [ ] No se puede editar goal de otro usuario (403)
- [x] Navegación entre páginas funciona
- [x] Responsive: funciona en móvil y desktop

---

## Fase 4: Quick Log (Area 3)

### Tareas

#### Backend

- [x] Crear `LogEntryController`
- [x] Implementar `store` - registrar entrada de log
- [x] Implementar lógica para actualizar `current_value` del goal según tipo
- [x] Implementar `index` - listar entries recientes (para Recent Activity) — integrado en Goals/Show
- [x] Crear Form Request para validación de log entry
- [x] Definir rutas

#### Frontend

- [x] Crear página `QuickLog/Index.vue` - vista de 4 categorías — integrado en Goals/Index con botón rápido
- [x] Crear componente `CategoryCard.vue` - tarjeta de categoría clickeable — no necesario, ya agrupado
- [x] Crear página `QuickLog/Category.vue` - goals de una categoría — integrado en Goals/Index
- [x] Crear componente `QuickLogAction.vue` - acciones según tipo de goal — integrado en Goals/Show
  - [x] Counter: botones +1, -1, input custom
  - [x] Yes/No: toggle switch
  - [x] Percentage: slider 0-100
  - [x] Money: input numérico con +/-
- [x] Implementar feedback visual al registrar (animación, toast, etc.) — spinner de loading
- [ ] Añadir campo opcional de nota
- [x] Mostrar valor actual y progreso del goal

### Checklist de Pruebas

- [x] Vista de categorías muestra las 4 categorías con iconos/colores
- [x] Click en categoría muestra goals activos de esa categoría
- [x] Goal tipo Counter:
  - [x] Botón +1 suma 1 al valor actual
  - [x] Botón -1 resta 1 al valor actual
  - [x] Input custom permite valores arbitrarios
  - [ ] No permite valor negativo total
- [x] Goal tipo Yes/No:
  - [x] Toggle marca como completado
  - [x] Toggle desmarca (vuelve a 0%)
  - [x] Fecha de completado se guarda
- [x] Goal tipo Percentage:
  - [x] Slider actualiza valor
  - [ ] Input numérico funciona
  - [x] Valor se limita a 0-100
- [x] Goal tipo Money:
  - [x] Input permite sumar cantidad
  - [x] Input permite restar cantidad
  - [x] Muestra currency correcta
- [x] LogEntry se crea en base de datos
- [ ] Nota opcional se guarda
- [x] Progreso del goal se actualiza inmediatamente
- [x] Feedback visual aparece tras log exitoso
- [x] Funciona bien en móvil (touch friendly)

---

## Fase 5: Dashboard (Area 2)

### Tareas

#### Backend

- [x] Crear `DashboardController`
- [x] Implementar endpoint para datos agregados del dashboard
- [x] Calcular progreso global (media de todos los goals)
- [x] Calcular progreso por categoría
- [x] Calcular datos para gráfico de evolución temporal
- [x] Calcular pace esperado vs real
- [x] Obtener actividad reciente

#### Frontend

- [x] Crear página `Dashboard/Index.vue` — integrado en Dashboard.vue
- [x] Implementar Year Progress Bar
  - [x] Calcular porcentaje del año transcurrido
  - [x] Mostrar barra visual con porcentaje
- [x] Implementar Category Summary Cards
  - [x] 4 tarjetas con color/icono de categoría
  - [x] Mostrar # goals activos
  - [x] Mostrar progreso medio (%)
  - [x] Mostrar completados vs en progreso
  - [x] Mini barra de progreso o sparkline
- [x] Implementar Global Progress Chart
  - [x] Instalar librería de gráficos (Chart.js, ApexCharts, etc.)
  - [x] Gráfico de barras: actividad semanal (logs)
  - [x] Eje X: tiempo (semanas)
  - [x] Eje Y: número de logs
- [x] Implementar Pace vs Year Chart
  - [x] Barra de progreso real
  - [x] Barra de progreso del año (referencia)
  - [x] Indicador visual de ahead/on-track/behind
- [ ] Implementar Per-Goal Charts (en vista de detalle)
  - [ ] Counter/Money: línea de valor acumulado + línea target
  - [ ] Percentage: línea de % + referencia de pace
  - [ ] Yes/No: solo estado y fecha si completado
- [x] Implementar Recent Activity
  - [x] Lista de últimos 10-20 log entries
  - [x] Mostrar: goal, categoría, valor, fecha/hora

### Checklist de Pruebas

- [x] Year Progress Bar muestra porcentaje correcto del año
- [x] Category Summary:
  - [x] Muestra datos correctos por categoría
  - [x] Colores e iconos correctos
  - [x] Click lleva a filtro de esa categoría (opcional)
- [x] Global Chart:
  - [x] Se renderiza sin errores
  - [x] Datos son correctos
  - [x] Responsive (se adapta a pantalla)
- [x] Pace vs Year Chart:
  - [x] Muestra ambas barras
  - [x] Indicador ahead/behind es correcto
- [ ] Per-Goal Charts:
  - [ ] Counter muestra evolución correcta
  - [ ] Money muestra evolución correcta
  - [ ] Percentage muestra evolución correcta
  - [ ] Yes/No muestra estado correcto
- [x] Recent Activity:
  - [x] Muestra entries más recientes primero
  - [x] Información es correcta
  - [x] Timestamps son legibles
- [x] Dashboard carga en tiempo razonable (<2s)
- [x] Funciona en móvil (scroll, touch)

---

## Fase 6: PWA & Mobile Polish

### Tareas

- [x] Crear `manifest.json` con:
  - [x] Nombre de la app
  - [x] Iconos en varios tamaños (SVG)
  - [x] Colores (theme_color, background_color)
  - [x] Display mode (standalone)
  - [x] Start URL
- [x] Crear Service Worker básico — via vite-plugin-pwa
- [x] Configurar caching de assets estáticos — workbox config
- [x] Añadir meta tags necesarios en `<head>`
- [x] Crear iconos de app en tamaños requeridos (192x192, 512x512, etc.) — SVG scalable
- [ ] Crear splash screens para iOS (opcional)
- [x] Optimizar para mobile:
  - [x] Touch targets de tamaño adecuado (48px mínimo)
  - [ ] Gestos (swipe para acciones, pull to refresh)
  - [x] Teclado numérico para inputs de números
  - [x] Viewport meta tag correcto
- [ ] Testear en dispositivos reales
- [ ] Implementar "Add to Home Screen" prompt (opcional)
- [ ] Configurar HTTPS (requerido para PWA) — necesario en producción

### Checklist de Pruebas

- [ ] Lighthouse PWA audit pasa (o score alto)
- [x] `manifest.json` es válido
- [x] Service Worker se registra correctamente
- [ ] App se puede instalar desde Chrome (desktop)
- [ ] App se puede añadir a home screen en Android
- [ ] App se puede añadir a home screen en iOS
- [ ] Abre en modo standalone (sin barra de navegador)
- [x] Iconos se muestran correctamente
- [ ] Funciona offline (al menos mensaje de offline)
- [x] Touch targets son suficientemente grandes
- [x] Inputs numéricos abren teclado numérico
- [x] No hay scroll horizontal involuntario
- [ ] Funciona en:
  - [ ] iPhone Safari
  - [ ] Android Chrome
  - [ ] iPad

---

## Fase 7: Testing & Calidad

### Tareas

- [ ] Configurar PHPUnit para tests de backend
- [ ] Escribir tests unitarios para modelos
- [ ] Escribir tests de feature para controllers
- [ ] Escribir tests de integración para flujos completos
- [ ] Configurar Vitest/Jest para tests de frontend
- [ ] Escribir tests de componentes Vue
- [ ] Configurar CI/CD (GitHub Actions)
- [ ] Ejecutar tests en cada PR
- [ ] Configurar code coverage
- [ ] Añadir Cypress/Playwright para E2E tests (opcional)

### Checklist de Pruebas

- [ ] `sail artisan test` pasa al 100%
- [ ] `sail npm run test` pasa al 100%
- [ ] Coverage de backend > 80%
- [ ] Coverage de frontend > 70%
- [ ] CI pipeline pasa en PRs
- [ ] E2E tests cubren flujos críticos:
  - [ ] Login con Google
  - [ ] Crear goal
  - [ ] Quick log de cada tipo
  - [ ] Ver dashboard

---

## Fase 8: Deployment

### Tareas

- [ ] Elegir hosting (Forge, Vapor, DigitalOcean, Railway, etc.)
- [ ] Configurar servidor/contenedores de producción
- [ ] Configurar dominio y DNS
- [ ] Configurar certificado SSL (Let's Encrypt)
- [ ] Configurar variables de entorno de producción
- [ ] Configurar base de datos de producción (MySQL/PostgreSQL)
- [ ] Configurar Redis de producción
- [ ] Configurar backups automáticos de BD
- [ ] Configurar queue worker (si se usa)
- [ ] Configurar logging (Sentry, etc.)
- [ ] Optimizar para producción:
  - [ ] `sail artisan config:cache`
  - [ ] `sail artisan route:cache`
  - [ ] `sail artisan view:cache`
  - [ ] `sail npm run build`
- [ ] Configurar deploy automático (CI/CD)

### Checklist de Pruebas

- [ ] App accesible desde dominio público
- [ ] HTTPS funciona correctamente
- [ ] Login con Google funciona en producción
- [ ] Todas las funcionalidades funcionan
- [ ] Performance aceptable (< 3s load time)
- [ ] No hay errores en logs
- [ ] Backups se ejecutan correctamente
- [ ] Deploy automático funciona

---

## Fase 9: Futuro — Streaks (Por Definir)

### Ideas a Explorar

- [ ] Definir concepto final de streaks
- [ ] Opción A: Streak como propiedad opcional de cualquier goal
- [ ] Opción B: Streak global de la app (días consecutivos usando Quick Log)
- [ ] Opción C: Tipo de goal "Daily Habit" con streak derivado
- [ ] Opción D: Combinación de anteriores
- [ ] Diseñar UI/UX para streaks
- [ ] Implementar lógica de cálculo de streak
- [ ] Añadir visualización de streak en Dashboard
- [ ] Implementar notificaciones/recordatorios (opcional)

---

## Notas Adicionales

### Comandos Útiles de Sail

```bash
# Levantar entorno
sail up -d

# Parar entorno
sail down

# Ejecutar artisan
sail artisan <comando>

# Ejecutar composer
sail composer <comando>

# Ejecutar npm
sail npm <comando>

# Ejecutar tests
sail artisan test

# Acceder a MySQL
sail mysql

# Acceder a Redis CLI
sail redis

# Ver logs
sail logs -f

# Ejecutar Tinker
sail artisan tinker
```

### Prioridades

1. **MVP**: Fases 0-4 (Setup, Auth, Models, Goals, Quick Log)
2. **Core**: Fase 5 (Dashboard)
3. **Polish**: Fases 6-7 (PWA, Testing)
4. **Launch**: Fase 8 (Deployment)
5. **Enhance**: Fase 9 (Streaks)

### Convenciones de Código

- Backend: PSR-12, PHPStan level 8
- Frontend: ESLint + Prettier, Composition API
- Commits: Conventional Commits
- PRs: Requieren review antes de merge

### Recursos

- [Laravel Sail Docs](https://laravel.com/docs/sail)
- [Laravel Docs](https://laravel.com/docs)
- [Inertia.js Docs](https://inertiajs.com)
- [Vue 3 Docs](https://vuejs.org)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Laravel Socialite](https://laravel.com/docs/socialite)
