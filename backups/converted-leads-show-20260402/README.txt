Backup before optimizing converted lead "show" (detail) page:
- /admin/converted-leads/view/{id}
- Same Blade is used by post-sales converted student show (controller also updated).

Files:
- ConvertedLeadController.php.bak (snippet: show() method differs)
- PostSalesConvertedLeadController.php.bak
- show.blade.php.bak
- LeadCallLogService.php.bak

New file introduced: app/Support/ConvertedLeadShowFileHelper.php (copy saved as ConvertedLeadShowFileHelper.php.bak if present)

Revert: restore .bak files over originals, delete app/Support/ConvertedLeadShowFileHelper.php if removing the feature entirely.
