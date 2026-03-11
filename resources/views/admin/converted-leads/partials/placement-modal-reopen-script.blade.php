@if(session('placement_modal_converted_lead_id'))
<script>
$(function() {
    show_small_modal('{{ route('admin.converted-leads.move-to-placement', session('placement_modal_converted_lead_id')) }}', 'Move to Placement');
});
</script>
@endif
