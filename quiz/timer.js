function startTimer(minutes) {
  const timerElement = document.getElementById("timer");
  let timeLeft = minutes * 60;

  const updateTimer = () => {
    const minutesLeft = Math.floor(timeLeft / 60);
    const secondsLeft = timeLeft % 60;

    timerElement.textContent = `Time Left ${minutesLeft}:${
      secondsLeft < 10 ? "0" : ""
    }${secondsLeft}`;

    // Update background color based on time left
    const fraction = timeLeft / (minutes * 60);
    if (fraction > 0.5) {
      timerElement.style.backgroundColor = "green";
    } else if (fraction > 0.2) {
      timerElement.style.backgroundColor = "orange";
    } else {
      timerElement.style.backgroundColor = "red";
    }

    if (timeLeft > 0) {
      timeLeft--;
    } else {
      document.getElementById(formId).submit();
      clearInterval(timerInterval);
    }
  };

  updateTimer();
  const timerInterval = setInterval(updateTimer, 1000);
}

// Example usage
// Starts a 5-minute timer
