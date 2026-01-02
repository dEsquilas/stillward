# Stillward — TODO & Roadmap

Este documento contiene todas las tareas necesarias para desarrollar Stillward, organizadas por fases. Cada fase incluye un checklist de pruebas.

---

## Fase 0: Setup Inicial con Laravel Sail

### Tareas

#### 0.1 Inicialización del proyecto

- [ ] Inicializar proyecto Laravel con Sail
  ```bash
  curl -s "https://laravel.build/stillward?with=mysql,redis" | bash
  cd stillward
  ```
- [ ] Configurar Git e inicializar repositorio
  ```bash
  git init
  git add .
  git commit -m "chore: initial Laravel Sail setup"
  ```
- [ ] Levantar entorno con Docker
  ```bash
  ./vendor/bin/sail up -d
  ```
- [ ] Configurar alias de Sail (añadir a `~/.zshrc` o `~/.bashrc`)
  ```bash
  alias sail='./vendor/bin/sail'
  ```

#### 0.2 Scaffolding Vue + Inertia

- [ ] Instalar Inertia.js (lado servidor)
  ```bash
  sail composer require inertiajs/inertia-laravel
  ```
- [ ] Crear middleware de Inertia
  ```bash
  sail artisan inertia:middleware
  ```
- [ ] Registrar middleware en `bootstrap/app.php`:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->web(append: [
          \App\Http\Middleware\HandleInertiaRequests::class,
      ]);
  })
  ```
- [ ] Crear template blade `resources/views/app.blade.php`:
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
- [ ] Instalar dependencias de frontend
  ```bash
  sail npm install vue@latest @inertiajs/vue3 @vitejs/plugin-vue
  ```
- [ ] Configurar Vite (`vite.config.js`):
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
- [ ] Configurar app.js (`resources/js/app.js`):
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
- [ ] Crear estructura de carpetas para Vue:
  ```bash
  mkdir -p resources/js/Pages
  mkdir -p resources/js/Components
  mkdir -p resources/js/Layouts
  ```
- [ ] Crear página de prueba `resources/js/Pages/Welcome.vue`:
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
- [ ] Actualizar ruta en `routes/web.php`:
  ```php
  use Inertia\Inertia;

  Route::get('/', function () {
      return Inertia::render('Welcome', [
          'message' => 'Hello from Inertia + Vue!'
      ]);
  });
  ```

#### 0.3 Tailwind CSS

- [ ] Instalar Tailwind CSS
  ```bash
  sail npm install -D tailwindcss postcss autoprefixer
  sail npx tailwindcss init -p
  ```
- [ ] Configurar `tailwind.config.js`:
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
- [ ] Configurar `resources/css/app.css`:
  ```css
  @tailwind base;
  @tailwind components;
  @tailwind utilities;
  ```

#### 0.4 Herramientas de calidad de código

- [ ] Instalar ESLint + Prettier
  ```bash
  sail npm install -D eslint prettier eslint-plugin-vue @vue/eslint-config-prettier
  ```
- [ ] Crear `.eslintrc.cjs`:
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
- [ ] Crear `.prettierrc`:
  ```json
  {
      "semi": true,
      "singleQuote": true,
      "tabWidth": 4,
      "trailingComma": "es5"
  }
  ```
- [ ] Instalar PHPStan
  ```bash
  sail composer require --dev phpstan/phpstan
  ```
- [ ] Crear `phpstan.neon`:
  ```neon
  parameters:
      level: 8
      paths:
          - app
  ```
- [ ] Añadir scripts a `package.json`:
  ```json
  "scripts": {
      "lint": "eslint resources/js --ext .js,.vue",
      "lint:fix": "eslint resources/js --ext .js,.vue --fix",
      "format": "prettier --write resources/js/**/*.{js,vue}"
  }
  ```

#### 0.5 Verificación final

- [ ] Ejecutar migrations iniciales (`sail artisan migrate`)
- [ ] Compilar assets (`sail npm run dev`)
- [ ] Verificar que todo funciona

### Checklist de Pruebas

- [ ] `sail up` levanta contenedores sin errores
- [ ] `sail artisan serve` no es necesario (ya corre en el contenedor)
- [ ] Acceso a `http://localhost` muestra página de Laravel
- [ ] `sail npm run dev` compila assets sin errores
- [ ] Hot reload funciona (cambiar Vue component y ver cambio)
- [ ] `sail artisan test` pasa (tests por defecto de Laravel)
- [ ] Conexión a MySQL funciona (`sail artisan migrate`)
- [ ] Acceso a MySQL desde host (`localhost:3306`)
- [ ] Redis funciona (`sail artisan tinker` -> `Cache::put('test', 'ok')`)

---

## Fase 1: Autenticación con Google

### Tareas

- [ ] Instalar Laravel Socialite (`sail composer require laravel/socialite`)
- [ ] Crear proyecto en Google Cloud Console
- [ ] Configurar OAuth 2.0 credentials (Client ID, Client Secret)
- [ ] Añadir variables de entorno para Google OAuth en `.env`
- [ ] Crear rutas de autenticación (`/auth/google`, `/auth/google/callback`)
- [ ] Implementar `AuthController` con métodos redirect y callback
- [ ] Crear/actualizar modelo `User` con campos necesarios (google_id, avatar, etc.)
- [ ] Crear migration para campos adicionales de User
- [ ] Implementar lógica de crear usuario si no existe, o login si existe
- [ ] Configurar middleware de autenticación
- [ ] Crear página de Login con botón "Sign in with Google"
- [ ] Implementar logout
- [ ] Redirigir usuarios no autenticados a login

### Checklist de Pruebas

- [ ] Botón "Sign in with Google" redirige a Google OAuth
- [ ] Después de autorizar en Google, vuelve a la app
- [ ] Usuario nuevo: se crea registro en BD con datos de Google
- [ ] Usuario existente: hace login sin crear duplicado
- [ ] Sesión persiste al refrescar página
- [ ] Logout cierra sesión correctamente
- [ ] Rutas protegidas redirigen a login si no autenticado
- [ ] No se puede acceder a `/auth/google/callback` directamente sin código OAuth
- [ ] Avatar del usuario se guarda y muestra correctamente

---

## Fase 2: Modelos y Base de Datos

### Tareas

- [ ] Diseñar schema de base de datos
- [ ] Crear migration para tabla `goals`
  - `id`, `user_id`, `category`, `type`, `title`, `description`
  - `target_value`, `current_value`, `unit`, `currency`
  - `is_completed`, `completed_at`, `is_archived`
  - `created_at`, `updated_at`
- [ ] Crear migration para tabla `log_entries`
  - `id`, `user_id`, `goal_id`, `value`, `note`, `created_at`
- [ ] Crear modelo `Goal` con relaciones y casts
- [ ] Crear modelo `LogEntry` con relaciones
- [ ] Definir constantes/enums para categories y goal types
- [ ] Implementar accessors para progreso calculado en Goal
- [ ] Crear factories para Goal y LogEntry
- [ ] Crear seeders con datos de ejemplo

### Checklist de Pruebas

- [ ] Migrations ejecutan sin errores (`sail artisan migrate:fresh`)
- [ ] Seeders crean datos correctamente (`sail artisan db:seed`)
- [ ] Relación User -> Goals funciona
- [ ] Relación Goal -> LogEntries funciona
- [ ] Relación LogEntry -> Goal funciona
- [ ] Cast de `category` a enum funciona
- [ ] Cast de `type` a enum funciona
- [ ] Accessor de progreso calcula correctamente para cada tipo:
  - [ ] Counter: `current_value / target_value`
  - [ ] Yes/No: 0% o 100%
  - [ ] Percentage: valor directo
  - [ ] Money: `current_amount / target_amount`
- [ ] Factory genera datos válidos
- [ ] Soft delete de goals funciona (si se implementa)

---

## Fase 3: Goals Management (Area 1)

### Tareas

#### Backend

- [ ] Crear `GoalController` con métodos CRUD
- [ ] Implementar `index` - listar goals del usuario agrupados por categoría
- [ ] Implementar `store` - crear nuevo goal con validación
- [ ] Implementar `show` - ver detalle de un goal
- [ ] Implementar `update` - editar goal existente
- [ ] Implementar `archive` - archivar goal sin eliminar
- [ ] Implementar `restore` - restaurar goal archivado
- [ ] Crear Form Requests para validación (`StoreGoalRequest`, `UpdateGoalRequest`)
- [ ] Definir rutas en `web.php`
- [ ] Añadir policies para autorización (solo owner puede editar)

#### Frontend

- [ ] Crear layout principal con navegación (bottom tabs o sidebar)
- [ ] Crear página `Goals/Index.vue` - listado por categorías
- [ ] Crear componente `GoalCard.vue` - tarjeta de goal individual
- [ ] Crear página `Goals/Create.vue` - formulario de creación
- [ ] Crear página `Goals/Edit.vue` - formulario de edición
- [ ] Crear página `Goals/Show.vue` - detalle de goal
- [ ] Implementar selección de categoría con iconos/colores
- [ ] Implementar selección de tipo con explicación de cada uno
- [ ] Implementar campos condicionales según tipo (unit, currency, target, etc.)
- [ ] Añadir confirmación antes de archivar
- [ ] Mostrar goals archivados en sección separada

### Checklist de Pruebas

- [ ] Lista de goals carga correctamente
- [ ] Goals se agrupan por categoría
- [ ] Crear goal tipo Counter funciona
- [ ] Crear goal tipo Yes/No funciona
- [ ] Crear goal tipo Percentage funciona
- [ ] Crear goal tipo Money funciona
- [ ] Validación de formulario muestra errores
- [ ] Campos requeridos no pueden estar vacíos
- [ ] Editar goal guarda cambios
- [ ] Archivar goal lo mueve a sección archivados
- [ ] Restaurar goal lo devuelve a activos
- [ ] No se puede editar goal de otro usuario (403)
- [ ] Navegación entre páginas funciona
- [ ] Responsive: funciona en móvil y desktop

---

## Fase 4: Quick Log (Area 3)

### Tareas

#### Backend

- [ ] Crear `LogEntryController`
- [ ] Implementar `store` - registrar entrada de log
- [ ] Implementar lógica para actualizar `current_value` del goal según tipo
- [ ] Implementar `index` - listar entries recientes (para Recent Activity)
- [ ] Crear Form Request para validación de log entry
- [ ] Definir rutas

#### Frontend

- [ ] Crear página `QuickLog/Index.vue` - vista de 4 categorías
- [ ] Crear componente `CategoryCard.vue` - tarjeta de categoría clickeable
- [ ] Crear página `QuickLog/Category.vue` - goals de una categoría
- [ ] Crear componente `QuickLogAction.vue` - acciones según tipo de goal
  - [ ] Counter: botones +1, -1, input custom
  - [ ] Yes/No: toggle switch
  - [ ] Percentage: slider 0-100
  - [ ] Money: input numérico con +/-
- [ ] Implementar feedback visual al registrar (animación, toast, etc.)
- [ ] Añadir campo opcional de nota
- [ ] Mostrar valor actual y progreso del goal

### Checklist de Pruebas

- [ ] Vista de categorías muestra las 4 categorías con iconos/colores
- [ ] Click en categoría muestra goals activos de esa categoría
- [ ] Goal tipo Counter:
  - [ ] Botón +1 suma 1 al valor actual
  - [ ] Botón -1 resta 1 al valor actual
  - [ ] Input custom permite valores arbitrarios
  - [ ] No permite valor negativo total
- [ ] Goal tipo Yes/No:
  - [ ] Toggle marca como completado
  - [ ] Toggle desmarca (vuelve a 0%)
  - [ ] Fecha de completado se guarda
- [ ] Goal tipo Percentage:
  - [ ] Slider actualiza valor
  - [ ] Input numérico funciona
  - [ ] Valor se limita a 0-100
- [ ] Goal tipo Money:
  - [ ] Input permite sumar cantidad
  - [ ] Input permite restar cantidad
  - [ ] Muestra currency correcta
- [ ] LogEntry se crea en base de datos
- [ ] Nota opcional se guarda
- [ ] Progreso del goal se actualiza inmediatamente
- [ ] Feedback visual aparece tras log exitoso
- [ ] Funciona bien en móvil (touch friendly)

---

## Fase 5: Dashboard (Area 2)

### Tareas

#### Backend

- [ ] Crear `DashboardController`
- [ ] Implementar endpoint para datos agregados del dashboard
- [ ] Calcular progreso global (media de todos los goals)
- [ ] Calcular progreso por categoría
- [ ] Calcular datos para gráfico de evolución temporal
- [ ] Calcular pace esperado vs real
- [ ] Obtener actividad reciente

#### Frontend

- [ ] Crear página `Dashboard/Index.vue`
- [ ] Implementar Year Progress Bar
  - [ ] Calcular porcentaje del año transcurrido
  - [ ] Mostrar barra visual con porcentaje
- [ ] Implementar Category Summary Cards
  - [ ] 4 tarjetas con color/icono de categoría
  - [ ] Mostrar # goals activos
  - [ ] Mostrar progreso medio (%)
  - [ ] Mostrar completados vs en progreso
  - [ ] Mini barra de progreso o sparkline
- [ ] Implementar Global Progress Chart
  - [ ] Instalar librería de gráficos (Chart.js, ApexCharts, etc.)
  - [ ] Gráfico de línea: evolución del progreso global
  - [ ] Eje X: tiempo (semanas/meses)
  - [ ] Eje Y: porcentaje
- [ ] Implementar Pace vs Year Chart
  - [ ] Línea de progreso real
  - [ ] Línea de progreso esperado (lineal)
  - [ ] Indicador visual de ahead/on-track/behind
- [ ] Implementar Per-Goal Charts (en vista de detalle)
  - [ ] Counter/Money: línea de valor acumulado + línea target
  - [ ] Percentage: línea de % + referencia de pace
  - [ ] Yes/No: solo estado y fecha si completado
- [ ] Implementar Recent Activity
  - [ ] Lista de últimos 10-20 log entries
  - [ ] Mostrar: goal, categoría, valor, fecha/hora

### Checklist de Pruebas

- [ ] Year Progress Bar muestra porcentaje correcto del año
- [ ] Category Summary:
  - [ ] Muestra datos correctos por categoría
  - [ ] Colores e iconos correctos
  - [ ] Click lleva a filtro de esa categoría (opcional)
- [ ] Global Chart:
  - [ ] Se renderiza sin errores
  - [ ] Datos son correctos
  - [ ] Responsive (se adapta a pantalla)
- [ ] Pace vs Year Chart:
  - [ ] Muestra ambas líneas
  - [ ] Línea esperada es correcta (lineal desde 0 a 100%)
  - [ ] Indicador ahead/behind es correcto
- [ ] Per-Goal Charts:
  - [ ] Counter muestra evolución correcta
  - [ ] Money muestra evolución correcta
  - [ ] Percentage muestra evolución correcta
  - [ ] Yes/No muestra estado correcto
- [ ] Recent Activity:
  - [ ] Muestra entries más recientes primero
  - [ ] Información es correcta
  - [ ] Timestamps son legibles
- [ ] Dashboard carga en tiempo razonable (<2s)
- [ ] Funciona en móvil (scroll, touch)

---

## Fase 6: PWA & Mobile Polish

### Tareas

- [ ] Crear `manifest.json` con:
  - [ ] Nombre de la app
  - [ ] Iconos en varios tamaños
  - [ ] Colores (theme_color, background_color)
  - [ ] Display mode (standalone)
  - [ ] Start URL
- [ ] Crear Service Worker básico
- [ ] Configurar caching de assets estáticos
- [ ] Añadir meta tags necesarios en `<head>`
- [ ] Crear iconos de app en tamaños requeridos (192x192, 512x512, etc.)
- [ ] Crear splash screens para iOS (opcional)
- [ ] Optimizar para mobile:
  - [ ] Touch targets de tamaño adecuado (48px mínimo)
  - [ ] Gestos (swipe para acciones, pull to refresh)
  - [ ] Teclado numérico para inputs de números
  - [ ] Viewport meta tag correcto
- [ ] Testear en dispositivos reales
- [ ] Implementar "Add to Home Screen" prompt (opcional)
- [ ] Configurar HTTPS (requerido para PWA)

### Checklist de Pruebas

- [ ] Lighthouse PWA audit pasa (o score alto)
- [ ] `manifest.json` es válido
- [ ] Service Worker se registra correctamente
- [ ] App se puede instalar desde Chrome (desktop)
- [ ] App se puede añadir a home screen en Android
- [ ] App se puede añadir a home screen en iOS
- [ ] Abre en modo standalone (sin barra de navegador)
- [ ] Iconos se muestran correctamente
- [ ] Funciona offline (al menos mensaje de offline)
- [ ] Touch targets son suficientemente grandes
- [ ] Inputs numéricos abren teclado numérico
- [ ] No hay scroll horizontal involuntario
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
