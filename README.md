## Symfony App Structure & Conventions

```
src
├── Controller
│   ├── Rest
│   │   └── v1
│   │       └── Connection
│   │           └── ConnectionController.php
│   └── Web
│       └── Connection
│           └── ConnectionController.php
├── DTO
│   └── Connection
│       ├── ConnectionDTO.php
│       └── NewConnectionDTO.php
├── Formatter
│   └── Connection
│       └── ConnectionFormatter.php
└── Feature
    └── ConnectionPanel
        ├── DTO
        │   └── ConnectionPanelItemDTO.php
        ├── Formatter
        │   └── ConnectionPanelFormatter.php
        └── Enum
            └── ConfirmStatus.php
```

**Key Directories:**
- `Controller/Rest/v1` — REST API controllers
- `Controller/Web` — Web controllers
- `DTO/`, `Feature/*/DTO|Enum` — Data transfer objects and enums (annotate with `#[DTO]` or `#[DTOEnum]`)
- `Entity/` — Doctrine entities
- `Formatter/` — Output formatting helpers
- `Feature/` — Feature modules (may contain DTO, Enum, Formatter)

**Service Wiring:**
- Services are auto-wired via `config/services.yaml` (see file for exclusions)

**TypeScript Type Generation:**
- Run `php bin/generate-types` to sync PHP DTOs/enums to `components/types.ts`
- Use `#[ArrayOf(SomeClass::class)]` for array-typed DTO properties

**Testing & Analysis:**
- Run `./scripts/phpunit.sh` for PHPUnit tests
- Run `./scripts/phpstan.sh` for static analysis
- Test config: `phpunit.xml.dist`, PHPStan config: `phpstan.dist.neon`

**Twig & React Integration:**
- Use `.react-component` and `data-component`/`data-parameters` in Twig to mount React
- Use `loadComponent`, `loadStyles`, `loadScripts` Twig helpers

**Database:**
- MySQL, configured via `.env.local` and `DATABASE_URL`
- Migrations in `/migrations`, managed by Doctrine

**See Also:**
- Project root `README.md` for high-level overview
- `.github/copilot-instructions.md` for AI agent conventions