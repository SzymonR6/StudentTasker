document.addEventListener('DOMContentLoaded', () => {
    const statusSelects = document.querySelectorAll('.task-status-select');

    statusSelects.forEach((select) => {
        select.addEventListener('change', async () => {
            const taskId = select.dataset.taskId;
            const statusId = select.value;

            select.disabled = true;

            try {
                const response = await fetch('/api/tasks/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        status_id: statusId,
                    }),
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    alert(result.message || 'Nie udało się zmienić statusu zadania.');
                    return;
                }

                select.classList.add('saved');

                setTimeout(() => {
                    select.classList.remove('saved');
                }, 1000);
            } catch (error) {
                alert('Wystąpił błąd podczas komunikacji z serwerem.');
            } finally {
                select.disabled = false;
            }
        });
    });
});