document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = document.getElementById("calendar");

  var events = [];

  tasks.forEach(function (task) {
    events.push({
      title: task.content,
      start: task.due.date,
    });
  });

  holidays.forEach(function (holiday) {
    events.push({
      title: holiday.name,
      start: holiday.date.iso,
      className: "holiday-event",
    });
  });

  var addedDates = [];

  forecast.list.forEach(function (weatherData) {
    var date = new Date(weatherData.dt * 1000);
    var dateString = date.toISOString().split("T")[0];

    var weatherClass = "";
    var weatherDescription = weatherData.weather[0].description.toLowerCase();

    if (weatherDescription.includes("rain")) {
      weatherClass = "weather-rainy";
    } else if (
      weatherDescription.includes("cloud") ||
      weatherDescription.includes("overcast")
    ) {
      weatherClass = "weather-cloudy";
    } else if (
      weatherDescription.includes("sun") ||
      weatherDescription.includes("clear")
    ) {
      weatherClass = "weather-sunny";
    }

    if (weatherData.dt_txt.includes("09:00:00")) {
      var temperatureCelsius = (weatherData.main.temp - 273.15).toFixed(1);

      var weatherIcon = "";
      if (weatherDescription.includes("rain")) {
        weatherIcon = '<i class="fas fa-cloud-showers-heavy"></i>';
      } else if (
        weatherDescription.includes("cloud") ||
        weatherDescription.includes("overcast")
      ) {
        weatherIcon = '<i class="fas fa-cloud"></i>';
      } else if (
        weatherDescription.includes("sun") ||
        weatherDescription.includes("clear")
      ) {
        weatherIcon = '<i class="fas fa-sun"></i>';
      }

      if (!addedDates.includes(dateString)) {
        addedDates.push(dateString);

        events.push({
          title: weatherData.weather[0].description,
          start: dateString,
          className: weatherClass,
          temperature: temperatureCelsius,
          icon: weatherIcon,
          url: "/weather/" + date.getTime(),
        });
      }
    }
  });

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    events: events,
    eventDidMount: function (info) {
      var weatherElement = document.createElement("div");
      weatherElement.className = "weather-info";

      if (info.event.extendedProps.temperature) {
        weatherElement.innerHTML =
          info.event.extendedProps.temperature + " °C ";
      }

      if (info.event.extendedProps.icon) {
        weatherElement.innerHTML += info.event.extendedProps.icon;
      }

      info.el.appendChild(weatherElement);
    },
  });

  calendar.render();
});
