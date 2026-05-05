<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById('dashboard-activity-search-input');
        const dateFilter = document.getElementById('dashboard-activity-date-filter');
        const items = Array.from(document.querySelectorAll('.dashboard-activity-item'));

        function filterActivities() {
            const searchValue = searchInput.value.toLowerCase();
            const dateValue = dateFilter.value;
            const now = new Date();

            let filteredItems = [];

            items.forEach(item => {
                const text = item.dataset.text || '';
                const createdAt = new Date(item.dataset.created);

                let showBySearch = text.includes(searchValue);
                let showByDate = true;

                switch (dateValue) {
                    case '24h':
                        showByDate = (now - createdAt) <= 24 * 60 * 60 * 1000;
                        break;
                    case '3d':
                        showByDate = (now - createdAt) <= 3 * 24 * 60 * 60 * 1000;
                        break;
                    case '30d':
                        showByDate = (now - createdAt) <= 30 * 24 * 60 * 60 * 1000;
                        break;
                    case '6m':
                        showByDate = (now - createdAt) <= 183 * 24 * 60 * 60 * 1000;
                        break;
                }

                if (showBySearch && showByDate) {
                    filteredItems.push({
                        item,
                        createdAt
                    });
                }

                // Archive class
                if ((now - createdAt) > 183 * 24 * 60 * 60 * 1000) {
                    item.classList.add('dashboard-activity-archived');
                }
            });

            // 🔥 Sort latest first
            filteredItems.sort((a, b) => b.createdAt - a.createdAt);

            // 🔥 Show only top 10
            const top10 = filteredItems.slice(0, 10);

            // Hide all first
            items.forEach(item => item.style.display = 'none');

            // Show only top 10
            top10.forEach(obj => {
                obj.item.style.display = '';
            });
        }

        searchInput.addEventListener('input', filterActivities);
        dateFilter.addEventListener('change', filterActivities);

        // Initial run
        filterActivities();

    });
</script><?php /**PATH C:\Users\PC\Downloads\steam-two\resources\views/dashboard/script/activity-script.blade.php ENDPATH**/ ?>