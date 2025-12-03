---
description: Flexplorer - alternative web interface for ABRA Flexi API exploration and testing
applyTo: '**'
---

# Flexplorer - Copilot Instructions

## Project Overview
Flexplorer is an **alternative web interface** for ABRA Flexi API exploration and testing:
- **API Explorer**: Interactive interface for ABRA Flexi REST API testing
- **Developer Tool**: Designed for developers working with ABRA Flexi accounting system
- **Web Application**: PHP 8.4+ web application with responsive interface
- **Data Browser**: Comprehensive data browsing and editing capabilities
- **API Inspector**: Real-time API call monitoring and debugging

## 📋 Development Standards

### Core Coding Guidelines
- **PHP 8.4+**: Use modern PHP features and strict types: `declare(strict_types=1);`
- **PSR-12**: Follow PHP-FIG coding standards for consistency
- **Type Safety**: Include type hints for all parameters and return types
- **Documentation**: PHPDoc blocks for all public methods and classes
- **Testing**: PHPUnit tests for all new functionality
- **Internationalization**: Use `_()` functions for translatable strings

### Code Quality Requirements
- **Syntax Validation**: After every PHP file edit, run `php -l filename.php` for syntax checking
- **Error Handling**: Implement comprehensive try-catch blocks with meaningful error messages
- **Testing**: Create/update PHPUnit test files for all new/modified classes
- **Performance**: Optimize for real-time API interactions
- **Security**: Ensure code doesn't expose sensitive ABRA Flexi credentials

### Development Best Practices
- **Code Comments**: Write in English using complete sentences and proper grammar
- **Variable Names**: Use meaningful names that describe their purpose
- **Constants**: Avoid magic numbers/strings; define constants instead
- **Exception Handling**: Always provide meaningful error messages
- **Commit Messages**: Use imperative mood and keep them concise
- **Security**: Ensure code is secure and doesn't expose sensitive information
- **Compatibility**: Maintain compatibility with latest PHP and library versions
- **Maintainability**: Follow best practices for maintainable code

### Working Directory Requirements
- **Web Application**: Always run from `src/` or `lib/` directory:
  ```bash
  cd src/
  php index.php
  ```
- **Path Resolution**: Ensures relative paths (`../vendor/autoload.php`, `../.env`) work correctly
- **Debian Compatibility**: Relative paths are intentionally used for packaging

### Testing Requirements
- **PHPUnit Integration**: All new classes require corresponding test files
- **API Testing**: Test API interactions with mock responses
- **UI Testing**: Test web interface functionality
- **Error Handling**: Test error scenarios and edge cases

## 🌐 ABRA Flexi API Integration

Flexplorer provides an alternative web interface for ABRA Flexi (formerly FlexiBee) API exploration and testing.

### General API Rules
- Always use **real ABRA Flexi API structure** — never invent endpoints or parameters
- Always access data through the official REST API, not direct SQL or local JSON files
- Treat all API responses as structured under the root key `"winstrom"`
- Handle both `.json` and `.xml` formats, but prefer `.json` for most operations
- When adding new features, ensure all API calls comply with ABRA Flexi conventions

### API Base Structure
```
https://{server}/c/{company}/{evidence}.{format}
```

Examples:
- `https://demo.flexibee.eu/c/demo_de/evidence-list.json`
- `https://demo.flexibee.eu/c/demo_de/faktura-vydana.json`

Each `.html` endpoint has `.json` and `.xml` machine-readable counterparts.

### Common Evidences
- `faktura-vydana` (issued invoices)
- `faktura-prijata` (received invoices)
- `adresar` (address book)
- `cenik` (price list)
- `pokladna` (cash register)
- `objednavka-prijata` (customer orders)

### Query Parameters
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

### Error Handling Guidelines
- Check `"success": true` in `"winstrom"`
- Handle HTTP codes:
  - 200/201 → success
  - 400 → bad request
  - 401 → unauthorized
  - 404 → not found
  - 409 → duplicate external ID
  - 429 → rate limit exceeded (use exponential backoff)
- Display error messages in DataGrid or console output for diagnostics

### Best Practices for API Integration
- Never hardcode field names or IDs — retrieve them dynamically from `/evidence-list.json`
- Always generate PHP 8.4+ code following PSR-12 standards
- Prefer reusable helper classes (e.g. `FlexiClient`, `EvidenceBrowser`, `ApiConnector`)
- When generating UI logic, ensure proper pagination, sorting, and filter support
- Always use `ext:` prefixed IDs when creating records to prevent duplication
- When rendering DataGrid tables, support switching detail levels and related entities

### Developer Tools
- Primary test instance: `https://demo.flexibee.eu/c/demo_de/`
- Main entrypoint for testing: `/evidence-list.json`
- Use integrated debugging tools to explore new endpoints safely

⚠️ **Important Notes for Copilot:**
- This is a **developer tool** - prioritize usability and debugging capabilities
- **API accuracy** is critical - never invent ABRA Flexi endpoints or parameters
- **Real-time interaction** - optimize for responsive API calls and UI updates
- **Error visibility** - make API errors clearly visible to developers
- **Security awareness** - handle authentication and sensitive data appropriately
