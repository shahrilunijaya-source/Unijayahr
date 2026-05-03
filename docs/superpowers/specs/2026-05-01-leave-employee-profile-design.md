# Leave Management + Employee Profile — Design Spec
**Date:** 2026-05-01  
**Project:** HRAI Webapp (Unijaya)  
**Status:** Approved — ready for implementation

---

## Scope

Two features built together:
1. **Employee Profile** — extended HR data per staff (profile, bank, emergency contact, education, documents)
2. **Leave Management** — apply, approve, track leave with annual balances

---

## Data Models

### `employee_profiles` (1:1 with users)
| Column | Type | Notes |
|--------|------|-------|
| user_id | FK unique | |
| date_of_birth | date | nullable |
| gender | enum: male, female | nullable |
| marital_status | enum: single, married, divorced, widowed | nullable |
| address | text | nullable |
| city | string | nullable |
| state | string | nullable |
| postcode | string(10) | nullable |
| nationality | string | default: Malaysian |

### `employee_bank_details` (1:1 with users)
| Column | Type | Notes |
|--------|------|-------|
| user_id | FK unique | |
| bank_name | string | |
| account_number | text | encrypted |
| account_holder_name | string | |

### `employee_emergency_contacts` (1:many, typically 1–2)
| Column | Type | Notes |
|--------|------|-------|
| user_id | FK | |
| name | string | |
| relationship | string | |
| phone | string(20) | |

### `employee_education` (1:many)
| Column | Type | Notes |
|--------|------|-------|
| user_id | FK | |
| institution | string | |
| qualification | string | e.g. SPM, Diploma, Degree, Masters |
| field_of_study | string | nullable |
| year_completed | year / smallint | nullable |

### `employee_documents`
| Column | Type | Notes |
|--------|------|-------|
| user_id | FK | |
| category | enum | ic_copy, offer_letter, contract, cert, medical_cert, resignation_letter, other |
| label | string | custom name — required when category = other |
| file_path | string | stored under `storage/app/private/documents/{user_id}/` |
| file_size | unsignedInt | bytes |
| mime_type | string | |
| uploaded_by | FK to users | |

### `leave_types`
| Column | Type | Notes |
|--------|------|-------|
| name | string | e.g. Annual Leave, Medical Leave |
| code | string unique | annual, mc, emergency, maternity, paternity, unpaid, replacement, study, compassionate |
| is_paid | boolean | |
| requires_document | boolean | MC = true |
| max_days_per_year | decimal(5,1) | nullable = unlimited |
| is_active | boolean | default true |

**Seed data (9 types):**
| Name | Code | Paid | Doc Required | Max Days |
|------|------|------|--------------|----------|
| Annual Leave | annual | yes | no | — (HR sets per staff) |
| Medical Leave | mc | yes | yes | 22 |
| Emergency Leave | emergency | yes | no | 3 |
| Maternity Leave | maternity | yes | no | 98 |
| Paternity Leave | paternity | yes | no | 7 |
| Unpaid Leave | unpaid | no | no | — |
| Replacement Leave | replacement | yes | no | — |
| Study Leave | study | yes | no | — |
| Compassionate Leave | compassionate | yes | no | 3 |

### `leave_balances`
| Column | Type | Notes |
|--------|------|-------|
| user_id | FK | |
| leave_type_id | FK | |
| year | smallint | |
| allocated_days | decimal(5,1) | HR-entered |
| carried_over | decimal(5,1) | default 0 |

`used_days` is computed at query time from approved `leave_requests` — not stored.

Unique constraint: `(user_id, leave_type_id, year)`.

### `leave_requests`
| Column | Type | Notes |
|--------|------|-------|
| user_id | FK | applicant |
| leave_type_id | FK | |
| start_date | date | |
| end_date | date | |
| total_days | decimal(4,1) | system-computed excluding weekends; PH exclusion manual (staff adjusts) |
| reason | text | nullable |
| status | enum | pending, approved, rejected, cancelled |
| manager_id | FK to users | who acted on it |
| manager_note | text | nullable |
| hr_notified_at | timestamp | nullable — set on approval |
| document_path | string | nullable — MC cert upload |

---

## Filament Resources & Pages

### Admin / HR

| Resource / Page | Role Access | Purpose |
|-----------------|-------------|---------|
| `EmployeeProfileResource` | admin, hr | All staff profiles — tabbed: Profile / Bank / Emergency / Education / Documents |
| `LeaveTypeResource` | admin only | CRUD leave types — configure paid/doc-required/max days |
| `LeaveBalanceResource` | admin, hr | Set annual allocation per staff per leave type per year |
| `LeaveRequestResource` | admin, hr | All requests — filter by status/staff/date; HR can override status |

### Manager

| Page | Role Access | Purpose |
|------|-------------|---------|
| `LeaveApprovalsPage` | manager | Pending leave requests from direct reports (`superior_id`); approve/reject with note |

### Staff

| Page | Role Access | Purpose |
|------|-------------|---------|
| `MyProfilePage` | staff | View + edit own profile, upload own documents |
| `MyLeavePage` | staff | Apply leave, view balance per type, view own history |

---

## Approval Workflow

```
Staff submits leave request
  → status = pending
  → Filament notification → Manager (superior_id)

Manager approves / rejects
  → status = approved / rejected
  → manager_id + manager_note recorded
  → Filament notification → Staff

On approval:
  → hr_notified_at stamped
  → Filament notification → HR role users
```

`used_days` is always computed live: `SUM(total_days) WHERE status = approved AND year = X`.

---

## Document Storage & Access

- **Path:** `storage/app/private/documents/{user_id}/{filename}`
- **Access:** Filament signed URLs — never direct web paths
- **Allowed types:** PDF, JPG, PNG, DOCX
- **Max size:** 10 MB per file
- **Visibility:**
  - Staff: upload + download own documents only
  - HR / Admin: upload + download all staff documents
  - Manager: read-only view of own team's document list (no download of sensitive docs like bank/IC)

---

## Permissions (Spatie Roles)

| Action | admin | hr | manager | staff |
|--------|-------|----|---------|-------|
| View all profiles | ✓ | ✓ | own team only | own only |
| Edit any profile | ✓ | ✓ | — | own only |
| Upload docs for any staff | ✓ | ✓ | — | own only |
| Manage leave types | ✓ | — | — | — |
| Set leave balances | ✓ | ✓ | — | — |
| View all leave requests | ✓ | ✓ | own team | own only |
| Approve / reject leave | ✓ | ✓ | own team | — |
| Apply for leave | ✓ | ✓ | ✓ | ✓ |

---

## Out of Scope (v1)

- Public holiday calendar integration (weekends excluded from day count; PH exclusion manual for now)
- Leave carry-forward automation (HR enters `carried_over` manually)
- Email notifications (Filament database notifications only)
- Mobile app
