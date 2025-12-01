# Converted Leads API

## Endpoint

**GET** `/api/v1/converted-leads`

## Authentication

This endpoint requires authentication via Laravel Sanctum. Include the bearer token in the Authorization header:

```
Authorization: Bearer {your_token}
```

## Query Parameters

All parameters are optional and can be combined for filtering:

### Pagination Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | Page number for pagination |
| `per_page` | integer | 25 | Number of records per page (max: 100) |

### Filter Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `search` | string | Search in name, phone, email, or register_number |
| `course_id` | integer | Filter by course ID |
| `batch_id` | integer | Filter by batch ID |
| `admission_batch_id` | integer | Filter by admission batch ID |
| `status` | string | Filter by status (Paid, Admission cancel, Active, Inactive) |
| `reg_fee` | string | Filter by registration fee status (Received, Not Received) |
| `exam_fee` | string | Filter by exam fee status (Pending, Not Paid, Paid) |
| `id_card` | string | Filter by ID card status (processing, download, not downloaded) |
| `tma` | string | Filter by TMA status (Uploaded, Not Upload) |
| `date_from` | date | Filter by created date from (format: YYYY-MM-DD) |
| `date_to` | date | Filter by created date to (format: YYYY-MM-DD) |

## Response Format

### Success Response (200)

```json
{
    "status": true,
    "data": [
        {
            "id": 1,
            "lead_id": 123,
            "name": "John Doe",
            "phone": "1234567890",
            "phone_code": "+91",
            "phone_display": "+91 1234567890",
            "email": "john@example.com",
            "dob": "1990-01-01",
            "dob_display": "01-01-1990",
            "register_number": "REG123456",
            "status": "Active",
            "converted_date": "01-01-2024",
            "created_at": "2024-01-01 10:00:00",
            "created_at_display": "01-01-2024 10:00 AM",
            "updated_at": "2024-01-01 10:00:00",
            "is_academic_verified": true,
            "academic_verified_at": "2024-01-01 10:00:00",
            "academic_verified_at_display": "01-01-2024 10:00 AM",
            "academic_verified_by_id": 5,
            "academic_verified_by": {
                "id": 5,
                "name": "Academic Assistant"
            },
            "is_support_verified": true,
            "support_verified_at": "2024-01-01 10:00:00",
            "support_verified_at_display": "01-01-2024 10:00 AM",
            "support_verified_by_id": 6,
            "support_verified_by": {
                "id": 6,
                "name": "Support Team"
            },
            "academic_document_approved_at": "2024-01-01 10:00:00",
            "academic_document_approved_at_display": "01-01-2024 10:00 AM",
            "course": {
                "id": 1,
                "title": "NIOS"
            },
            "course_id": 1,
            "batch": {
                "id": 1,
                "title": "Batch 2024"
            },
            "batch_id": 1,
            "admission_batch": {
                "id": 1,
                "title": "Admission Batch 2024"
            },
            "admission_batch_id": 1,
            "subject": {
                "id": 1,
                "title": "Mathematics"
            },
            "subject_id": 1,
            "student_details": {
                "reg_fee": "Received",
                "exam_fee": "Paid",
                "id_card": "download",
                "tma": "Uploaded",
                "enroll_no": "ENR123",
                "converted_date": "2024-01-01"
            },
            "academic_assistant": {
                "id": 3,
                "name": "Assistant Name"
            },
            "academic_assistant_id": 3,
            "created_by": {
                "id": 2,
                "name": "Creator Name"
            },
            "created_by_id": 2
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 25,
        "total": 100,
        "last_page": 4,
        "from": 1,
        "to": 25
    }
}
```

### Error Response (401)

```json
{
    "status": false,
    "message": "Unauthorized"
}
```

## Example Requests

### Basic Request

```bash
curl -X GET "https://crm-demo.test/api/v1/converted-leads" \
  -H "Authorization: Bearer {your_token}"
```

### With Pagination

```bash
curl -X GET "https://crm-demo.test/api/v1/converted-leads?page=2&per_page=50" \
  -H "Authorization: Bearer {your_token}"
```

### With Filters

```bash
curl -X GET "https://crm-demo.test/api/v1/converted-leads?course_id=1&status=Active&date_from=2024-01-01&date_to=2024-12-31" \
  -H "Authorization: Bearer {your_token}"
```

### With Search

```bash
curl -X GET "https://crm-demo.test/api/v1/converted-leads?search=John" \
  -H "Authorization: Bearer {your_token}"
```

### Combined Filters

```bash
curl -X GET "https://crm-demo.test/api/v1/converted-leads?course_id=1&batch_id=5&status=Active&reg_fee=Received&page=1&per_page=25" \
  -H "Authorization: Bearer {your_token}"
```

## Role-Based Access

The API respects the same role-based filtering as the web interface:

- **General Manager**: Can see all converted leads
- **Team Lead**: Can see converted leads from their team
- **Admission Counsellor**: Can see all converted leads
- **Academic Assistant**: Can see all converted leads
- **Telecaller**: Can only see converted leads from leads assigned to them
- **Support Team**: Only see academically verified leads
- **Mentor**: Currently no filtering (same as web controller)

## Notes

- All date fields are returned in both ISO format (`Y-m-d H:i:s`) and display format (`d-m-Y h:i A`)
- Phone numbers are returned with both raw values and formatted display values
- The API uses lazy loading (pagination) to improve performance
- Maximum `per_page` is limited to 100 records per request

