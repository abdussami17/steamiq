<script id="nq6s2d">
    function duplicateEvent(eventId) {
    
        if (!confirm('Duplicate this event?')) return;
    
        fetch(`/events/${eventId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                alert('Event duplicated successfully');
                location.reload();
            } else {
                alert(res.message || 'Something went wrong');
            }
        })
        .catch(() => {
            alert('Server error');
        });
    }
    </script><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/events/scripts/duplicate-event-script.blade.php ENDPATH**/ ?>