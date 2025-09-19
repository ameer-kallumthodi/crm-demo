# Voxbay Integration Documentation

This document explains how to use the Voxbay calling integration in the CRM system.

## Overview

The Voxbay integration allows telecallers to make outgoing calls directly from the CRM system and automatically logs all call activities. The integration includes:

- Outgoing call functionality
- Call logs management
- Call reports and analytics
- Webhook support for incoming call logs

## Setup

### 1. Environment Variables

Add the following environment variables to your `.env` file:

```env
UID_NUMBER=your_voxbay_uid_number
UPIN=your_voxbay_upin
```

### 2. Database

The integration uses the existing `voxbay_call_logs` table. Make sure the table exists and has the proper structure.

## API Endpoints

### Public Endpoints (No Authentication Required)

- `POST /api/voxbay/outgoing-call` - Initiate an outgoing call
- `GET /api/voxbay/telecaller/{id}/extension` - Get telecaller extension
- `GET /api/voxbay/test-connection` - Test Voxbay connection
- `POST /api/voxbay/webhook` - Webhook for call logs

### Protected Endpoints (Authentication Required)

- `GET /admin/call-logs` - View all call logs
- `GET /admin/call-logs/{id}` - View specific call log
- `GET /leads/{lead}/call-logs` - View call logs for a specific lead
- `GET /admin/reports/voxbay-call-logs` - Voxbay call logs report

## Usage

### Making Calls

1. Navigate to a lead's detail page
2. Click the "Call Lead" button (only visible to telecallers)
3. The system will automatically initiate the call through Voxbay

### Viewing Call Logs

1. **All Call Logs**: Go to Admin → Call Logs
2. **Lead-specific Call Logs**: Go to Lead Details → Call Logs
3. **Call Log Reports**: Go to Admin → Reports → Voxbay Call Logs

### Call Logs Report

The Voxbay Call Logs report provides:

- Summary statistics (total calls, answer rate, duration)
- Telecaller performance metrics
- Team performance comparison
- Daily call statistics
- Export to Excel/PDF

## Features

### 1. Outgoing Calls

- Automatic call initiation through Voxbay API
- Extension validation before making calls
- Real-time call status updates
- Error handling and user feedback

### 2. Call Logs Management

- Automatic logging of all call activities
- Filter by type, status, date range, telecaller
- Search functionality
- Pagination support

### 3. Reports and Analytics

- Comprehensive call statistics
- Performance metrics by telecaller and team
- Export capabilities (Excel/PDF)
- Date range filtering

### 4. Webhook Support

- Automatic call log creation from Voxbay webhooks
- Support for incoming, outgoing, and missed calls
- Recording URL storage

## Configuration

### Telecaller Setup

Each telecaller must have:
- A valid extension number (`ext_no` field in users table)
- Proper role assignment (Telecaller role)

### Voxbay Configuration

Ensure your Voxbay account is properly configured with:
- Valid UID number
- Valid UPIN
- Proper department settings

## Troubleshooting

### Common Issues

1. **Call Button Disabled**
   - Check if telecaller has extension number configured
   - Verify telecaller role assignment

2. **Call Not Initiated**
   - Check Voxbay credentials in environment variables
   - Verify Voxbay service availability
   - Check network connectivity

3. **Call Logs Not Appearing**
   - Verify webhook configuration
   - Check database connection
   - Review error logs

### Testing

Use the test connection endpoint to verify Voxbay integration:

```bash
curl -X GET http://your-domain.com/api/voxbay/test-connection
```

## Security

- Public API endpoints are designed for Voxbay webhook integration
- All protected endpoints require proper authentication
- CSRF protection is enabled for all forms
- Input validation is implemented for all endpoints

## Support

For technical support or questions about the Voxbay integration, please contact your system administrator or refer to the Voxbay documentation.
