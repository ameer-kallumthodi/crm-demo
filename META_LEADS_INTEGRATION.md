# Meta Leads Integration

This Laravel application includes integration with Facebook Meta (formerly Facebook) to automatically fetch and process lead data from Facebook Lead Ads.

## Features

- **Automatic Lead Fetching**: Fetches leads from Facebook Lead Ads API
- **Lead Processing**: Processes and stores leads in the `meta_leads` table
- **Lead Distribution**: Automatically distributes leads to telecallers based on workload
- **Phone Number Processing**: Intelligently parses phone numbers with country codes
- **Lead Management**: Admin interface to view, manage, and process Meta leads
- **Scheduled Tasks**: Automated fetching and processing via Laravel scheduler

## Configuration

### Environment Variables

Add the following variables to your `.env` file:

```env
FB_APP_ID=your_facebook_app_id
FB_APP_SECRET=your_facebook_app_secret
FB_ACCESS_TOKEN=your_facebook_access_token
FB_LEAD_FORM_ID=your_lead_form_id
```

### Facebook App Setup

1. Create a Facebook App at [Facebook Developers](https://developers.facebook.com/)
2. Add the "Lead Ads" product to your app
3. Generate a long-lived access token
4. Get your Lead Form ID from the Facebook Ads Manager

## Database Structure

The `meta_leads` table stores the following information:

- `lead_id`: Facebook's unique lead ID
- `full_name`: Lead's full name
- `phone_number`: Lead's phone number
- `email`: Lead's email address
- `other_details`: JSON field containing additional form data
- `form_no`: Form number identifier
- `created_time`: When the lead was created on Facebook
- `created_at`/`updated_at`: Laravel timestamps

## API Endpoints

### Admin Routes (Protected)

- `GET /admin/meta-leads` - View all Meta leads
- `GET /admin/meta-leads/{id}` - View specific lead details
- `DELETE /admin/meta-leads/{id}` - Delete a lead
- `GET /admin/meta-leads/statistics` - Get lead statistics
- `POST /admin/meta-leads/fetch` - Manually fetch leads from Facebook
- `POST /admin/meta-leads/push` - Push Meta leads to main leads table
- `GET /admin/meta-leads/test-token` - Test Facebook token validity
- `GET /admin/meta-leads/debug-env` - Debug environment configuration

## Console Commands

### Manual Commands

```bash
# Fetch leads from Facebook
php artisan meta:fetch-leads

# Push Meta leads to main leads table
php artisan meta:push-leads
```

### Scheduled Tasks

The following tasks are automatically scheduled:

- **Fetch Leads**: Every 30 minutes
- **Push Leads**: Every hour

To run the scheduler, add this to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Lead Processing Logic

### Phone Number Processing

The system uses the `PhoneNumberHelper` to intelligently parse phone numbers:

- Extracts country codes automatically
- Handles various phone number formats
- Supports international numbers
- Falls back to manual parsing for edge cases

### Lead Distribution

When pushing leads to the main leads table:

1. **Duplicate Check**: Ensures leads aren't processed twice
2. **Telecaller Assignment**: Assigns leads to telecallers with the lowest daily lead count
3. **Lead Source**: Automatically sets lead source to "Meta" (ID: 7)
4. **Status**: Sets initial lead status to "New" (ID: 1)
5. **Remarks**: Generates formatted remarks from additional form data

### Additional Data Processing

The system extracts and formats additional information from Facebook form data:

- **City**: Geographic location
- **Job Title**: Professional information
- **Curriculum Type**: Educational preference
- **Child Information**: For educational leads
- **Phone Verification**: Whether phone number is verified

## Usage Examples

### Fetching Leads Manually

```php
use App\Services\FacebookApiService;

$facebookService = new FacebookApiService();
$result = $facebookService->fetchLeads();

if (isset($result['leads'])) {
    // Process leads
    MetaLead::insertLeads($result['leads']);
}
```

### Processing Leads

```php
use App\Models\MetaLead;

$leads = MetaLead::getNewLeads($lastLeadId);
foreach ($leads as $lead) {
    // Process each lead
    $phoneData = get_phone_code($lead->phone_number);
    $remarks = $lead->generateRemarks();
    // ... create main lead record
}
```

## Error Handling

The system includes comprehensive error handling:

- **Token Validation**: Checks Facebook token validity before API calls
- **Rate Limiting**: Respects Facebook API rate limits
- **Duplicate Prevention**: Prevents processing the same lead multiple times
- **Logging**: Detailed logging for debugging and monitoring

## Monitoring

### Logs

Check the Laravel logs for detailed information:

```bash
tail -f storage/logs/laravel.log
```

### Statistics

The admin interface provides real-time statistics:

- Total leads count
- Today's leads
- Leads with phone numbers
- Leads with email addresses
- Leads by form number

## Troubleshooting

### Common Issues

1. **Token Expired**: Regenerate your Facebook access token
2. **No Leads Found**: Check your Lead Form ID and permissions
3. **Phone Parsing Issues**: Verify phone number formats in form data
4. **Permission Denied**: Ensure your Facebook app has Lead Ads permissions

### Debug Commands

```bash
# Test Facebook token
php artisan meta:fetch-leads

# Debug environment variables
# Visit /admin/meta-leads/debug-env in browser
```

## Security Considerations

- Store Facebook credentials securely in environment variables
- Use long-lived access tokens
- Regularly rotate access tokens
- Monitor API usage and rate limits
- Implement proper access controls for admin routes

## Performance Optimization

- Use database indexing on frequently queried fields
- Implement caching for statistics
- Consider using queues for large lead processing
- Monitor memory usage during bulk operations

## Support

For issues or questions:

1. Check the Laravel logs
2. Verify Facebook app configuration
3. Test API endpoints manually
4. Review environment variables
5. Check database connectivity

## Future Enhancements

Potential improvements:

- Real-time lead notifications
- Advanced lead scoring
- Integration with CRM systems
- Automated follow-up sequences
- Lead quality assessment
- Custom form field mapping
