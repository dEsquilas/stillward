# Stillward — Database Schema

## Diagrama de Relaciones

```
┌──────────────┐       ┌──────────────┐       ┌──────────────┐
│    users     │       │    goals     │       │  log_entries │
├──────────────┤       ├──────────────┤       ├──────────────┤
│ id           │───┐   │ id           │───┐   │ id           │
│ name         │   │   │ user_id      │←──┘   │ user_id      │
│ email        │   │   │ category     │   │   │ goal_id      │←──┐
│ google_id    │   └──→│ type         │   │   │ value        │   │
│ avatar       │       │ title        │   │   │ note         │   │
│ created_at   │       │ description  │   │   │ created_at   │   │
│ updated_at   │       │ target_value │   │   └──────────────┘   │
└──────────────┘       │ current_value│   │                      │
                       │ unit         │   │                      │
                       │ currency     │   │                      │
                       │ is_completed │   │                      │
                       │ completed_at │   │                      │
                       │ is_archived  │   └──────────────────────┘
                       │ created_at   │
                       │ updated_at   │
                       └──────────────┘
```

---

## Tabla: users

Usuarios autenticados con Google.

| Campo        | Tipo           | Nullable | Default   | Descripción                    |
|--------------|----------------|----------|-----------|--------------------------------|
| id           | bigint (PK)    | No       | auto      | ID único                       |
| name         | varchar(255)   | No       | -         | Nombre del usuario             |
| email        | varchar(255)   | No       | -         | Email (único)                  |
| google_id    | varchar(255)   | No       | -         | ID de Google (único)           |
| avatar       | varchar(255)   | Yes      | null      | URL del avatar de Google       |
| created_at   | timestamp      | No       | now()     | Fecha de creación              |
| updated_at   | timestamp      | No       | now()     | Fecha de última actualización  |

**Índices:**
- `PRIMARY KEY (id)`
- `UNIQUE (email)`
- `UNIQUE (google_id)`

---

## Tabla: goals

Objetivos/metas del usuario.

| Campo         | Tipo           | Nullable | Default   | Descripción                              |
|---------------|----------------|----------|-----------|------------------------------------------|
| id            | bigint (PK)    | No       | auto      | ID único                                 |
| user_id       | bigint (FK)    | No       | -         | Referencia a users.id                    |
| category      | enum           | No       | -         | body_mind, money_work, growth, life      |
| type          | enum           | No       | -         | counter, yes_no, percentage, money       |
| title         | varchar(255)   | No       | -         | Título del goal                          |
| description   | text           | Yes      | null      | Descripción opcional                     |
| target_value  | decimal(12,2)  | Yes      | null      | Valor objetivo (counter, money)          |
| current_value | decimal(12,2)  | No       | 0         | Valor actual                             |
| unit          | varchar(50)    | Yes      | null      | Unidad de medida (km, libros, etc.)      |
| currency      | char(3)        | Yes      | null      | Moneda ISO 4217 (EUR, USD, etc.)         |
| is_completed  | boolean        | No       | false     | Si el goal está completado               |
| completed_at  | timestamp      | Yes      | null      | Fecha de completado                      |
| is_archived   | boolean        | No       | false     | Si el goal está archivado                |
| created_at    | timestamp      | No       | now()     | Fecha de creación                        |
| updated_at    | timestamp      | No       | now()     | Fecha de última actualización            |

**Índices:**
- `PRIMARY KEY (id)`
- `INDEX (user_id)`
- `INDEX (user_id, category)`
- `INDEX (user_id, is_archived)`

**Foreign Keys:**
- `user_id REFERENCES users(id) ON DELETE CASCADE`

**Enums:**

```php
enum GoalCategory: string
{
    case BodyMind = 'body_mind';
    case MoneyWork = 'money_work';
    case Growth = 'growth';
    case Life = 'life';
}

enum GoalType: string
{
    case Counter = 'counter';
    case YesNo = 'yes_no';
    case Percentage = 'percentage';
    case Money = 'money';
}
```

---

## Tabla: log_entries

Registros de actividad en Quick Log.

| Campo        | Tipo           | Nullable | Default   | Descripción                    |
|--------------|----------------|----------|-----------|--------------------------------|
| id           | bigint (PK)    | No       | auto      | ID único                       |
| user_id      | bigint (FK)    | No       | -         | Referencia a users.id          |
| goal_id      | bigint (FK)    | No       | -         | Referencia a goals.id          |
| value        | decimal(12,2)  | No       | -         | Valor registrado               |
| note         | varchar(500)   | Yes      | null      | Nota opcional                  |
| created_at   | timestamp      | No       | now()     | Fecha del registro             |

**Índices:**
- `PRIMARY KEY (id)`
- `INDEX (user_id)`
- `INDEX (goal_id)`
- `INDEX (user_id, created_at DESC)` -- Para Recent Activity

**Foreign Keys:**
- `user_id REFERENCES users(id) ON DELETE CASCADE`
- `goal_id REFERENCES goals(id) ON DELETE CASCADE`

---

## Lógica de Progreso por Tipo

### Counter
```php
$progress = ($current_value / $target_value) * 100;
```

### Yes/No
```php
$progress = $is_completed ? 100 : 0;
```

### Percentage
```php
$progress = $current_value; // Ya es porcentaje
```

### Money
```php
$progress = ($current_value / $target_value) * 100;
```

---

## Lógica de Log Entry por Tipo

### Counter
- `value` puede ser positivo o negativo
- Se suma al `current_value` del goal
- Ejemplo: +1, -1, +5, +10

### Yes/No
- `value` es 1 (completado) o 0 (no completado)
- Reemplaza el estado del goal
- Si `value = 1`, se marca `is_completed = true` y `completed_at = now()`

### Percentage
- `value` es el nuevo porcentaje (0-100)
- Reemplaza el `current_value` del goal
- Si `value = 100`, se marca `is_completed = true`

### Money
- `value` puede ser positivo o negativo
- Se suma al `current_value` del goal
- Ejemplo: +500, -200, +1000

---

## Migrations Laravel

```php
// create_goals_table.php
Schema::create('goals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('category', ['body_mind', 'money_work', 'growth', 'life']);
    $table->enum('type', ['counter', 'yes_no', 'percentage', 'money']);
    $table->string('title');
    $table->text('description')->nullable();
    $table->decimal('target_value', 12, 2)->nullable();
    $table->decimal('current_value', 12, 2)->default(0);
    $table->string('unit', 50)->nullable();
    $table->char('currency', 3)->nullable();
    $table->boolean('is_completed')->default(false);
    $table->timestamp('completed_at')->nullable();
    $table->boolean('is_archived')->default(false);
    $table->timestamps();

    $table->index(['user_id', 'category']);
    $table->index(['user_id', 'is_archived']);
});

// create_log_entries_table.php
Schema::create('log_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('goal_id')->constrained()->cascadeOnDelete();
    $table->decimal('value', 12, 2);
    $table->string('note', 500)->nullable();
    $table->timestamp('created_at');

    $table->index(['user_id', 'created_at']);
});

// modify_users_table.php (añadir campos de Google)
Schema::table('users', function (Blueprint $table) {
    $table->string('google_id')->unique()->after('email');
    $table->string('avatar')->nullable()->after('google_id');
    $table->dropColumn('password'); // No necesitamos password
    $table->dropColumn('email_verified_at'); // No verificamos email
});
```

---

## Datos de Ejemplo (Seeder)

```php
// Categorías con colores e iconos
$categories = [
    'body_mind' => ['color' => '#10B981', 'icon' => 'heart'],
    'money_work' => ['color' => '#F59E0B', 'icon' => 'currency-dollar'],
    'growth' => ['color' => '#8B5CF6', 'icon' => 'academic-cap'],
    'life' => ['color' => '#EC4899', 'icon' => 'sparkles'],
];

// Goals de ejemplo
$goals = [
    [
        'category' => 'body_mind',
        'type' => 'counter',
        'title' => 'Correr 500 km',
        'target_value' => 500,
        'unit' => 'km',
    ],
    [
        'category' => 'body_mind',
        'type' => 'counter',
        'title' => 'Leer 20 libros',
        'target_value' => 20,
        'unit' => 'libros',
    ],
    [
        'category' => 'money_work',
        'type' => 'money',
        'title' => 'Ahorrar 18.000€',
        'target_value' => 18000,
        'currency' => 'EUR',
    ],
    [
        'category' => 'money_work',
        'type' => 'yes_no',
        'title' => 'Conseguir certificación AWS',
    ],
    [
        'category' => 'growth',
        'type' => 'percentage',
        'title' => 'Terminar curso de Rust',
    ],
    [
        'category' => 'life',
        'type' => 'yes_no',
        'title' => 'Visitar Japón',
    ],
];
```
