document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var events = [];

    tasks.forEach(function(task) {
        events.push({
            title: task.content,
            start: task.due.date
        });
    });

    holidays.forEach(function(holiday) {
        events.push({
            title: holiday.name,
            start: holiday.date.iso,
            className: 'holiday-event'
        });
    });
   
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: events
    });

    calendar.render();
});



