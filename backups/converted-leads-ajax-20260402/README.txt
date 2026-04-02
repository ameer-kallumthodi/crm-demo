Backup created before AJAX / server-side pagination optimization for admin converted leads index.

Files:
- ConvertedLeadController.php.bak — full controller before edits (restore index + remove new methods if reverting)
- index.blade.php.bak — converted leads index view before edits

New route added (not in this folder): GET admin/converted-leads/data → converted-leads.data

Restore: copy .bak files over the originals, remove Route::get('/converted-leads/data', ...) from routes/web.php, delete partials dt-cell-*.blade.php if reverting completely.
