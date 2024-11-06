document.addEventListener('DOMContentLoaded', () => {
    const monthYearElem = document.getElementById('monthYear');
    const calendarGrid = document.querySelector('.calendar-grid');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    let currentDate = new Date();
    const today = new Date();

    function renderCalendar(date) {
        calendarGrid.innerHTML = '';

        const month = date.getMonth();
        const year = date.getFullYear();

        monthYearElem.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;

        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);
        const startDay = firstDayOfMonth.getDay();
        const totalDays = lastDayOfMonth.getDate();

        // Add day headers
        calendarGrid.innerHTML += `<div class="calendar-day">Sun</div>`;
        calendarGrid.innerHTML += `<div class="calendar-day">Mon</div>`;
        calendarGrid.innerHTML += `<div class="calendar-day">Tue</div>`;
        calendarGrid.innerHTML += `<div class="calendar-day">Wed</div>`;
        calendarGrid.innerHTML += `<div class="calendar-day">Thu</div>`;
        calendarGrid.innerHTML += `<div class="calendar-day">Fri</div>`;
        calendarGrid.innerHTML += `<div class="calendar-day">Sat</div>`;

        // Add empty days before the first day of the month
        for (let i = 0; i < startDay; i++) {
            calendarGrid.innerHTML += `<div></div>`;
        }

        // Add days of the month
        for (let i = 1; i <= totalDays; i++) {
            const dayClass = (date.getFullYear() === today.getFullYear() &&
                date.getMonth() === today.getMonth() &&
                i === today.getDate()) ? 'today' : '';
            calendarGrid.innerHTML += `<div class="${dayClass}">${i}</div>`;
        }
    }

    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    renderCalendar(currentDate);
});
