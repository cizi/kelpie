# Copilot instructions for kelpie

Kelpie is a PHP web application built on the **Nette Framework 3.2** (PHP 8.1+). It powers a
dog-breeding club website (collie/sheltie) with a public frontend and an admin backend for
managing dogs, litters, shows, referees, vets and site content. Much of the domain code,
comments and database columns are in **Czech**.

## Build, run, test

- Install dependencies: `composer install`
- Local dev server: `php -S localhost:8000 -t www` (document root is `www/`)
- `app/`, `log/` and `temp/` must stay writable and must **not** be web-accessible.
- Tests use **nette/tester** (`.phpt` files in `tests/`):
  - Whole suite: `vendor/bin/tester tests`
  - Single test: `vendor/bin/tester tests/ExampleTest.phpt`
  - Tests boot a real DI container via `tests/bootstrap.php`, so they require a valid
    `app/config/config.local.neon`.
- Lint Latte templates: `vendor/bin/latte-lint app`; lint NEON: `vendor/bin/neon-lint <file>`
- There is no CI config and only a placeholder test; add tests deliberately.

## Configuration

- Copy `app/config/config.local.neon.example` to `config.local.neon` and fill in the
  `database` credentials. `config.local.neon` is machine-specific and not committed meaningfully.
- `app/config/config.neon` wires services. **Every form, repository and controller must be
  registered as a service here** (see the large `services:` list) or DI injection will fail.
- Path constants (`UPLOAD_PATH`, `LANG_PATH`, `SITEMAP_PATH`) are defined in `app/path.php`,
  loaded first by `app/bootstrap.php`.

## Architecture (big picture)

- **Two modules**, mapped by `RouterFactory` (`app/router/RouterFactory.php`):
  - `Admin` → `admin/<presenter>/<action>[/<id>]`, presenters in `app/AdminModule/presenters`.
  - `Frontend` → `<presenter>/<action>[/<id>]`, presenters in `app/FrontendModule/presenters`.
  - Presenter class mapping: `App\*Module\Presenters\*Presenter` (from `config.neon`).
- **Database access uses Dibi, not Nette Database.** `\Dibi\Connection` is injected. Repositories
  in `app/model/*Repository.php` extend `BaseRepository` (holds the connection + `Nette\SmartObject`)
  and write **raw SQL against Czech-named tables** (e.g. `appdata_zmeny`).
- **Entities** live in `app/model/entity`. They are plain PHP objects with public Czech-named
  properties, getters/setters, and a `hydrate(array $data)` method that maps a Dibi row's array
  to the entity. Repositories fetch rows and hydrate entities manually — there is no ORM.
- **Presenter hierarchy:** frontend `BasePresenter` (abstract) handles language, web config,
  header/footer/slider loading and shared services via `injectBaseSettings(...)`. Admin presenters
  extend `SignPresenter` (which extends `BasePresenter`) to enforce login + role checks in
  `startup()`.
- **Frontend page presenters are generated/nested by menu structure**: classes named like
  `FeItem1velord2Presenter` encode menu level/order (`PRESENTER_PREFIX = "FeItem"`,
  `LEVEL_ORDER_DELIMITER = "velord"`). Don't hand-edit these naming conventions casually.
- **Editorial change-approval workflow:** edits to dogs can be staged in `appdata_zmeny` via
  `AwaitingChangesRepository` and diffed with `DogChangesComparatorController` before an admin
  approves them (states in `DogChangeStateEnum`).

## Key conventions

- **Forms are dedicated service classes** in `app/forms` (e.g. `UserForm`), constructed via the
  injected `FormFactory`, exposing a `create(...)` method that returns a `Nette\Application\UI\Form`.
  Presenters call these in `createComponent*` methods. Register new forms in `config.neon`.
- **Enums** live in `app/Enum` and extend the reflection-based `Enum` base class. Constants are the
  stored integer values (e.g. `UserRoleEnum::USER_ROLE_ADMINISTRATOR = 99`); use
  `translatedForSelect()` to build select options from translation constants.
- **Localization is file-based `define()` constants**, not gettext runtime calls despite the
  dependency. `LangRepository::loadLanguageMutation($lang)` `require`s
  `www/locale/<lang>/translation.php`, which defines UPPER_SNAKE_CASE constants (e.g.
  `USER_EDIT_EMAIL_LABEL`). Forms and templates reference these constants directly. Adding UI text
  means adding the constant to **every** locale file (`cs`, `en`).
- Role-based access is enforced imperatively in presenter `startup()` via
  `$this->getUser()->getRoles()[0]` compared against `UserRoleEnum` constants.
- Templates are Latte (`.latte`) under each module's `templates/<Presenter>/` directory; shared
  layout is `@layout.latte`.
- Add a new admin CRUD area by creating: entity + repository (`app/model`), form(s) (`app/forms`),
  presenter extending `SignPresenter`, templates, translation constants, and service registrations.

## Gotchas

- SQL table/column names are Czech and legacy; check `sql/schema.sql` and `sql/updates` before
  assuming a schema.
- `composer-php83.json` is an alternate manifest for a PHP 8.3 target; keep it in sync when
  changing `composer.json` dependencies.
- Adminer (`www/adminer`) is bundled for DB inspection.
