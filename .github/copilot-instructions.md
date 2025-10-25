<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

All code comments should be written in English.

All messages, including error messages, should be written in English.

All code should be written in PHP 8.4 or later.

All code should follow the PSR-12 coding standard.

When writing code, always include a docblock for functions and classes, describing their purpose, parameters, and return types.

When writing tests, use PHPUnit and follow the PSR-12 coding standard.

When writing documentation in docs folder, use Markdown format.

When writing commit messages, use the imperative mood and keep them concise.

When writing code comments, use complete sentences and proper grammar.

When writing code, always use meaningful variable names that describe their purpose.

When writing code, avoid using magic numbers or strings; instead, define constants for them.

When writing code, always handle exceptions properly and provide meaningful error messages.

When writing code, always include type hints for function parameters and return types.

We are using the i18n library for internationalization, so always use the _() functions for strings that need to be translated.

When writing code, always ensure that it is secure and does not expose any sensitive information.

When writing code, always consider performance and optimize where necessary.

When writing code, always ensure that it is compatible with the latest version of PHP and the libraries we are using.

When writing code, always ensure that it is well-tested and includes unit tests where applicable.

When writing code, always ensure that it is maintainable and follows best practices.

When create new class or update existing class, always create or update its phpunit test files.

When developing or testing this application, always run the main script from the src/ or lib/ directory:
```bash
cd src/
php index.php
```

This ensures the relative paths (../vendor/autoload.php and ../.env) work correctly during development.

The application uses relative paths intentionally - they are resolved during Debian packaging via sed commands in debian/rules file for production deployment.

After every single edit to a PHP file, always run `php -l` on the edited file to lint it and ensure code sanity before proceeding further. This is mandatory for all PHP code changes.

---

## ABRA Flexi API Integration Context (for Flexplorer)

Flexplorer is an alternative web interface for ABRA Flexi (formerly FlexiBee) intended for developers to explore and test API endpoints.

Copilot must understand and correctly use the **ABRA Flexi REST API** when generating or modifying code.

### General Rules
- Always use **real ABRA Flexi API structure** — never invent endpoints or parameters.
- Always access data through the official REST API, not direct SQL or local JSON files.
- Treat all API responses as structured under the root key `"winstrom"`.
- Handle both `.json` and `.xml` formats, but prefer `.json` for most operations.
- When adding new features (DataGrid, API inspector, editor, etc.), ensure all API calls comply with ABRA Flexi conventions.

### API Base
```
https://{server}/c/{company}/{evidence}.{format}
```

Examples:
- `https://demo.flexibee.eu/c/demo_de/evidence-list.json`
- `https://demo.flexibee.eu/c/demo_de/faktura-vydana.json`

Each `.html` endpoint has `.json` and `.xml` machine-readable counterparts.

### Evidences
Each **evidence** represents a data entity such as:
- `faktura-vydana` (issued invoices)
- `faktura-prijata` (received invoices)
- `adresar` (address book)
- `cenik` (price list)
- `pokladna` (cash register)
- `objednavka-prijata` (customer orders)

The list of all evidences is available via `/c/{company}/evidence-list.json`.

### Common Query Parameters
| Parameter | Description |
|------------|-------------|
| `limit` | Number of records to return |
| `start` | Offset for pagination |
| `order` | Sorting order (e.g. `order=datVyst desc`) |
| `filter` | Field-based filters |
| `detail` | Level of detail (`summary`, `full`, `ids`) |
| `relations` | Include related entities |
| `includes` | Expand linked evidences |

### Filtering Examples
```
GET /c/demo_de/faktura-vydana.json?limit=10&order=datVyst desc
GET /c/demo_de/faktura-prijata.json?firma=like:Vitex
GET /c/demo_de/faktura-vydana.json?lastUpdate=gte:2024-01-01
```

### CRUD Operations
- `GET` — list or retrieve records  
- `POST` — create records (idempotent via `ext:<id>`)  
- `PUT` — update records  
- `DELETE` — delete records  

All payloads and responses are wrapped under:
```json
{
  "winstrom": {
    "evidence-name": [ ... ]
  }
}
```

### Error Handling
- Check `"success": true` in `"winstrom"`.
- Handle HTTP codes:
  - 200/201 → success
  - 400 → bad request
  - 401 → unauthorized
  - 404 → not found
  - 409 → duplicate external ID
  - 429 → rate limit exceeded (use exponential backoff)
- Display error messages in DataGrid or console output for diagnostics.

### Best Practices for Copilot
- Never hardcode field names or IDs — retrieve them dynamically from `/evidence-list.json`.
- Always generate PHP 8.4+ code following PSR-12, using meaningful names and docblocks.
- Prefer reusable helper classes (e.g. `FlexiClient`, `EvidenceBrowser`, `ApiConnector`).
- When generating UI logic (JS or PHP), ensure proper pagination, sorting, and filter support.
- Always use `ext:` prefixed IDs when creating records to prevent duplication.
- When rendering DataGrid tables, support switching detail levels and related entities (`includes`).

### Developer Tools
- Primary test instance: `https://demo.flexibee.eu/c/demo_de/`
- Main entrypoint for testing: `/evidence-list.json`
- Use Warp Terminal and integrated Copilot Chat to explore new endpoints safely.

By following these rules, Copilot will produce code that correctly interacts with ABRA Flexi and avoids API misuse or data inconsistency.
