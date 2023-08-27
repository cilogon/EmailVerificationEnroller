const FULL_DASH_ARRAY = 283;
// the numbers are percents
const WARNING_THRESHOLD = 0.5;
const INFO_THRESHOLD = 0.25;

const COLOR_CODES = {
  info: {
    color: "green",
    threshold: INFO_THRESHOLD
  },
  warning: {
    color: "orange",
    threshold: WARNING_THRESHOLD
  },
  alert: {
    color: "red"
  }
};

let timePassed = 0;
let timerInterval = null;
let remainingPathColor = COLOR_CODES.alert.color;

function appendTimerToDocument(timeLeft) {
  document.getElementById("app-timer").innerHTML = `
<div class="base-timer">
  <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
    <g class="base-timer__circle">
      <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
      <path
        id="base-timer-path-remaining"
        stroke-dasharray="283"
        class="base-timer__path-remaining ${remainingPathColor}"
        d="
          M 50, 50
          m -45, 0
          a 45,45 0 1,0 90,0
          a 45,45 0 1,0 -90,0
        "
      ></path>
    </g>
  </svg>
  <span id="base-timer-label" class="base-timer__label">${formatTime(
    timeLeft
  )}</span>
</div>
`;
}

function onTimesUp() {
  clearInterval(timerInterval);
}

function startTimer(timeLimit, timeLeft) {
  timerInterval = setInterval(() => {
    timePassed = timePassed += 1;
    timeLeft = timeLimit - timePassed;
    document.getElementById("base-timer-label").innerHTML = formatTime(
      timeLeft
    );
    setCircleDasharray(timeLimit, timeLeft);
    setRemainingPathColor(timeLimit, timeLeft);

    if (timeLeft === 0) {
      onTimesUp();
    }
  }, 1000);
}

function formatTime(time) {
  const minutes = Math.floor(time / 60);
  let seconds = time % 60;

  if (seconds < 10) {
    seconds = `0${seconds}`;
  }

  return `${minutes}:${seconds}`;
}

function setRemainingPathColor(timeLimit, timeLeft) {
  const {info, warning, alert} = COLOR_CODES;
  if (timeLeft/timeLimit <= info.threshold) {
    document
      .getElementById("base-timer-path-remaining")
      .classList.remove(warning.color);
    document
      .getElementById("base-timer-path-remaining")
      .classList.add(info.color);
  } else if (timeLeft/timeLimit <= warning.threshold) {
    document
      .getElementById("base-timer-path-remaining")
      .classList.remove(alert.color);
    document
      .getElementById("base-timer-path-remaining")
      .classList.add(warning.color);
  }
}

function calculateTimeFraction(timeLimit, timeLeft) {
  const rawTimeFraction = timeLeft / timeLimit;
  return rawTimeFraction - (1 / timeLimit) * (1 - rawTimeFraction);
}

function setCircleDasharray(timeLimit, timeLeft) {
  const circleDasharray = `${(
    calculateTimeFraction(timeLimit, timeLeft) * FULL_DASH_ARRAY
  ).toFixed(0)} 283`;
  document
    .getElementById("base-timer-path-remaining")
    .setAttribute("stroke-dasharray", circleDasharray);
}

