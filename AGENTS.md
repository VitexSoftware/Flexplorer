# 🧠 AI Developer Context — ABRA Flexi API for Flexplorer Modernization

## 🧩 Project Overview

**Flexplorer** is an **alternative web interface** for the ERP system **ABRA Flexi** (formerly FlexiBee), built for **developers** to explore, inspect, and manipulate data through the **ABRA Flexi REST API**.

**Repository**: git@github.com:VitexSoftware/Flexplorer.git

Your task as an AI assistant is to:

* Fully understand **how the ABRA Flexi API works** — its endpoints, formats, evidences, filters, and data structure.
* Generate **accurate and stable code** that integrates with this API.
* Help modernize Flexplorer's frontend (DataGrid, filters, detail panels) and backend logic (API queries, pagination, error handling).

---

## 🧭 ABRA Flexi REST API — Complete Reference

### 🌍 Base URL

```
https://{server}/c/{company}/{evidence}.{format}
```

Examples:

* `https://demo.flexibee.eu/c/demo_de/evidence-list.json`
* `https://demo.flexibee.eu/c/demo_de/faktura-vydana.json`

Each `.html` endpoint has machine-readable `.json` and `.xml` versions.

### 🔐 Authentication

* HTTP Basic Auth (`username:password`)
* Token-based authentication (via session or API key)
* Always use HTTPS for production.

---

## 🗂️ Evidences

The system is organized around **evidences** — top-level entities similar to database tables, such as:

| Example Evidence     | Description                           |
| -------------------- | ------------------------------------- |
| `faktura-vydana`     | Issued invoices                       |
| `faktura-prijata`    | Received invoices                     |
| `adresar`            | Address book (companies and contacts) |
| `cenik`              | Price list                            |
| `pokladna`           | Cash register                         |
| `objednavka-prijata` | Customer orders                       |

### Metadata

* List of all evidences:
  `GET /c/{company}/evidence-list.json`
* Each evidence defines:

  * **Fields** (columns)
  * **Relations** to other evidences
  * **Supported operations**

Flexplorer should use this metadata to **dynamically render field editors and DataGrids**.

---

## 🔍 Filtering, Pagination, and Sorting

### Basic filters

```
GET /c/demo_de/faktura-vydana.json?limit=20&order=datVyst desc
GET /c/demo_de/faktura-prijata.json?firma=like:Vitex
GET /c/demo_de/faktura-vydana.json?lastUpdate=gte:2024-01-01
```

### Common query parameters

| Parameter   | Description                                          |
| ----------- | ---------------------------------------------------- |
| `limit`     | Maximum number of records to return                  |
| `start`     | Offset for pagination                                |
| `order`     | Sort order (e.g. `order=datVyst desc`)               |
| `filter`    | Advanced filtering syntax                            |
| `relations` | Include related entities                             |
| `detail`    | Response detail level (`summary`, `full`, `id-only`) |

### Detail levels

| Level             | Description              |
| ----------------- | ------------------------ |
| `summary`         | Minimal set of fields    |
| `full`            | All fields and relations |
| `ids` / `id-only` | Only identifiers         |

---

## ⚙️ CRUD Operations

### Read (GET)

```bash
GET /c/demo_de/faktura-vydana.json?detail=full&limit=10
```

### Create (POST)

```bash
POST /c/demo_de/faktura-vydana.json
{
  "winstrom": {
    "faktura-vydana": [
      {
        "id": "ext:INV-2025-001",
        "firma": "code:VITEX",
        "sumCelkem": 12000.50,
        "datVyst": "2025-01-10"
      }
    ]
  }
}
```

### Update (PUT)

```bash
PUT /c/demo_de/faktura-vydana/123.json
{
  "winstrom": {
    "faktura-vydana": [
      { "sumCelkem": 12500.00 }
    ]
  }
}
```

### Delete (DELETE)

```bash
DELETE /c/demo_de/faktura-vydana/123.json
```

---

## 🧠 JSON Response Structure

Every API response wraps results inside a root `"winstrom"` object.

Example:

```json
{
  "winstrom": {
    "faktura-vydana": [
      {
        "id": "123",
        "kod": "INV-2025-001",
        "firma@showAs": "Vitex Software s.r.o.",
        "sumCelkem": 12000.50
      }
    ],
    "success": true,
    "stats": { "totalCount": 1 }
  }
}
```

On errors:

```json
{
  "winstrom": {
    "results": [
      {
        "success": false,
        "message": "Invalid field name",
        "code": "E_BAD_FIELD"
      }
    ]
  }
}
```

---

## 🚨 Error Handling & Limits

* Handle HTTP codes:
  `200 OK`, `201 Created`, `400 Bad Request`, `401 Unauthorized`,
  `404 Not Found`, `409 Conflict`, `429 Too Many Requests`
* Implement retries for rate-limiting (429) with exponential backoff.
* Always validate that the top-level `"success"` flag is `true`.

---

## 🧱 Best Practices for Flexplorer

1. **Dynamic evidence loading** – load `/evidence-list.json` to populate navigation.
2. **Metadata-driven UI** – fields, relations, and formats must be fetched dynamically.
3. **DataGrid integration** – support filters, sorting, paging, and column visibility.
4. **Error visibility** – show API errors and diagnostic JSON payloads in the UI.
5. **External IDs** – always use `ext:<id>` to ensure idempotency.
6. **Caching** – cache metadata and evidence schemas to reduce API calls.
7. **Configurable endpoints** – allow server URL, company code, and authentication settings.

---

## 🧩 Useful References

* [API Ninja Guide (official tutorial)](https://podpora.flexibee.eu/cs/articles/5432305-api-ninja-uvod)
* [General API usage guide](https://podpora.flexibee.eu/cs/articles/12495537-jak-pouzivat-api)
* [Demo evidence list JSON](https://demo.flexibee.eu/c/demo_de/evidence-list.json)

---

## 🧪 AI Behavior Guidelines

When generating or modifying code for **Flexplorer**:

* Never guess endpoint paths or parameters — always use real ABRA Flexi API structure.
* When unsure about a field or evidence name, query `/evidence-list.json` or refer to demo endpoints.
* Keep code modular and metadata-driven.
* Ensure consistency between API data and displayed fields in the DataGrid.
* Always wrap API data operations in proper error checks and handle the `"winstrom"` response object correctly.

---

### ✅ Expected Output from AI

* Correct PHP/JS code using the official ABRA Flexi API conventions.
* Accurate JSON parsing and safe data updates.
* Intelligent autocompletion for filters, evidence names, and parameters.
* Suggested UI/UX improvements for editing and visualizing API data.
