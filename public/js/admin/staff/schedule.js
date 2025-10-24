// public/js/schedule.js
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const day = this.dataset.day;
            const container = document.querySelector(`.schedule-container[data-day="${day}"]`);
            const addButton = document.querySelector(`.add-time-block[data-day="${day}"]`);

            if (this.checked) {
                container.innerHTML = ''; // Clear "Día no laborable"
                addTimeBlock(day, container);
                addButton.style.display = 'inline-block';
            } else {
                container.innerHTML = '<p class="text-muted no-work-day mb-0">Día no laborable.</p>';
                 addButton.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.add-time-block').forEach(button => {
        button.addEventListener('click', function () {
            const day = this.dataset.day;
            const container = document.querySelector(`.schedule-container[data-day="${day}"]`);
            addTimeBlock(day, container);
        });
    });

    document.querySelector('body').addEventListener('click', function (e) {
        if (e.target.closest('.remove-time-block')) {
            e.preventDefault();
            const timeBlock = e.target.closest('.time-block');
            const container = timeBlock.parentElement;
            const day = container.dataset.day;
            const checkbox = document.getElementById(`cb_${day}`);
            const addButton = document.querySelector(`.add-time-block[data-day="${day}"]`);
            
            timeBlock.remove();

            if (container.children.length === 0) {
                checkbox.checked = false;
                container.innerHTML = '<p class="text-muted no-work-day mb-0">Día no laborable.</p>';
                 addButton.style.display = 'none';
            }
        }
    });

    function addTimeBlock(day, container) {
        // Remove "no-work-day" message if it exists
        const noWorkDayMsg = container.querySelector('.no-work-day');
        if (noWorkDayMsg) {
            noWorkDayMsg.remove();
        }
        
        const index = container.getElementsByClassName('time-block').length;
        const newTimeBlock = `
            <div class="row py-1 align-items-end time-block">
                <div class="col-5">
                    <label class="form-check-label">
                        Inicio
                    </label>
                    <input type="time" class="form-control" name="schedules[${day}][${index}][start_time]" required>
                </div>
                <div class="col-5">
                    <label class="form-check-label">
                        Inicio
                    </label>
                    <input type="time" class="form-control" name="schedules[${day}][${index}][end_time]" required>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-time-block"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newTimeBlock);
    }
});
