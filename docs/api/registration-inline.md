## Registration Leads Inline APIs

### Inline Edit
- **Method**: `POST`
- **URL**: `/api/v1/registration-leads/inline-update`
- **Auth**: Sanctum (Bearer token)

#### Payload
| Field | Type | Required | Notes |
| --- | --- | --- | --- |
| `lead_detail_id` | integer | yes | Must exist in `leads_details.id`. |
| `field` | string | yes | Editable field name (see list below). |
| `value` | string (nullable) | optional | New value (rules depend on field). |

Allowed `field` values:
`student_name`, `father_name`, `mother_name`, `date_of_birth`, `gender`, `is_employed`, `email`, `phone`, `whatsapp`, `parents_phone`, `father_contact_number`, `father_contact_code`, `mother_contact_number`, `mother_contact_code`, `street`, `locality`, `post_office`, `district`, `state`, `pin_code`, `message`, `subject_id`, `batch_id`, `sub_course_id`, `passed_year`, `programme_type`, `location`, `class_time_id`.

Special rules:
- Phone-related fields (`phone`, `whatsapp`, `parents_phone`, `father_contact`, `mother_contact`) expect `"<country_code>|<number>"`.
- `class_time_id` must belong to the same course and only if the course needs time.
- ID fields validate ownership within the course, and `batch_id` also updates the parent `leads.batch_id`.
- `programme_type` accepts `online`/`offline`; when switching to `online`, `location` is cleared.
- `location` accepts `Ernakulam` or `Malappuram`.

#### Sample Request
```json
POST /api/v1/registration-leads/inline-update
{
  "lead_detail_id": 123,
  "field": "programme_type",
  "value": "online"
}
```

#### Success Response
```json
{
  "status": true,
  "message": "Registration details updated successfully.",
  "data": {
    "new_value": "Online",
    "hide_location": true,
    "show_location": false
  }
}
```

---

### Document Verification
- **Method**: `POST`
- **URL**: `/api/v1/registration-leads/document-verification`
- **Auth**: Sanctum (Bearer token)

#### Payload
| Field | Type | Required | Notes |
| --- | --- | --- | --- |
| `lead_detail_id` | integer | yes | Must exist in `leads_details.id`. |
| `document_type` | string | yes | One of: `sslc_certificate`, `plustwo_certificate`, `plus_two_certificate`, `ug_certificate`, `post_graduation_certificate`, `birth_certificate`, `passport_photo`, `adhar_front`, `adhar_back`, `signature`, `other_document`. |
| `verification_status` | string | yes | `pending` or `verified`. |
| `need_to_change_document` | boolean | optional | Defaults to `false`. Requires `new_file` when `true`. |
| `new_file` | file (pdf/jpg/jpeg/png, ≤2 MB) | optional | Needed when replacing the document. |

#### Behaviour
- Permission checks ensure the caller can access the lead.
- Verification metadata (`*_verification_status`, `*_verified_by`, `*_verified_at`) is updated.
- When `need_to_change_document` is true, the registration status resets to `pending` and reviewer fields are cleared.
- File uploads are stored under `student-documents/` with UUID names; activity logs record uploads, changes, or verification-only updates.

#### Sample Request (Replace document)
```
POST /api/v1/registration-leads/document-verification
Content-Type: multipart/form-data
Authorization: Bearer <token>

lead_detail_id=123
document_type=sslc_certificate
verification_status=verified
need_to_change_document=1
new_file=@/path/to/file.pdf
```

#### Success Response
```json
{
  "status": true,
  "message": "Document verification updated successfully.",
  "data": {
    "document_type": "sslc_certificate",
    "verification_status": "verified",
    "need_to_change_document": true,
    "lead_status": "pending",
    "document_url": "https://.../storage/student-documents/<uuid>.pdf"
  }
}
```

