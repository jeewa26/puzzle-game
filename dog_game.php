<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🐶 Dog Guessing Game</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="dog-style.css">
</head>
<body>
  <div class="game-container">
    <h1>🐾 Guess the Dog Breed!</h1>
    
    <div class="stats-bar">
      <div class="stat-box">
        <div class="stat-label">Time</div>
        <div class="stat-value" id="timer">30</div>
      </div>
      <div class="stat-box">
        <div class="stat-label">Score</div>
        <div class="stat-value" id="score">0</div>
      </div>
    </div>

    <div id="game-area">
      <div class="image-container">
        <img id="dog-img" src="" alt="Dog Image">
      </div>
      <div id="options"></div>
    </div>

    <p id="message"></p>
    <button id="next-btn" style="display:none;">Next Round →</button>

    <div class="back-link">
      <a href="index.php">← Back to Home</a>
    </div>
  </div>

  <script>
    let score = 0;
    let timeLeft = 30;
    let timer;
    let correctAnswer = "";
    const breeds = ["beagle", "pug", "labrador", "dalmatian", "bulldog", "boxer", "poodle", "retriever", "husky", "chihuahua"];

    function startGame() {
      document.getElementById("message").innerText = "";
      document.getElementById("next-btn").style.display = "none";
      getDog();
      startTimer();
    }

    function startTimer() {
      timer = setInterval(() => {
        timeLeft--;
        document.getElementById("timer").innerText = timeLeft;
        if (timeLeft <= 0) endGame();
      }, 1000);
    }

    function getDog() {
      correctAnswer = breeds[Math.floor(Math.random() * breeds.length)];
      
      // Show loading state
      const imgContainer = document.querySelector('.image-container');
      imgContainer.innerHTML = '<div class="loading"></div>';
      
      fetch(`https://dog.ceo/api/breed/${correctAnswer}/images/random`)
        .then(res => res.json())
        .then(data => {
          imgContainer.innerHTML = '<img id="dog-img" src="' + data.message + '" alt="Dog Image">';
          showOptions();
        })
        .catch(() => {
          imgContainer.innerHTML = '<img id="dog-img" src="" alt="Dog Image">';
          document.getElementById("message").innerText = "🐶 API Error — Try Again!";
          document.getElementById("message").style.background = "rgba(255, 107, 107, 0.2)";
          document.getElementById("message").style.color = "#FF6B6B";
          document.getElementById("message").style.border = "1px solid rgba(255, 107, 107, 0.3)";
        });
    }

    function showOptions() {
      const shuffled = [...breeds].sort(() => 0.5 - Math.random()).slice(0, 3);
      if (!shuffled.includes(correctAnswer)) shuffled[Math.floor(Math.random() * 3)] = correctAnswer;
      
      const optionsDiv = document.getElementById("options");
      optionsDiv.innerHTML = "";
      
      shuffled.forEach(breed => {
        const btn = document.createElement("button");
        btn.textContent = breed.charAt(0).toUpperCase() + breed.slice(1);
        btn.onclick = () => checkAnswer(breed, btn);
        optionsDiv.appendChild(btn);
      });
    }

    function checkAnswer(breed, button) {
      const buttons = document.querySelectorAll("#options button");
      buttons.forEach(b => b.disabled = true);
      
      const messageEl = document.getElementById("message");
      
      if (breed === correctAnswer) {
        button.classList.add("correct");
        score += 10;
        document.getElementById("score").innerText = score;
        messageEl.innerText = "✅ Correct! +10 points";
        messageEl.style.background = "rgba(107, 203, 119, 0.2)";
        messageEl.style.color = "#6BCB77";
        messageEl.style.border = "1px solid rgba(107, 203, 119, 0.3)";
      } else {
        button.classList.add("wrong");
        messageEl.innerText = "❌ Wrong! It was " + correctAnswer.charAt(0).toUpperCase() + correctAnswer.slice(1);
        messageEl.style.background = "rgba(255, 107, 107, 0.2)";
        messageEl.style.color = "#FF6B6B";
        messageEl.style.border = "1px solid rgba(255, 107, 107, 0.3)";
      }
      
      document.getElementById("next-btn").style.display = "inline-block";
    }

    document.getElementById("next-btn").addEventListener("click", () => {
      const messageEl = document.getElementById("message");
      messageEl.innerText = "";
      messageEl.style.background = "transparent";
      messageEl.style.border = "none";
      getDog();
      document.getElementById("next-btn").style.display = "none";
    });

    function endGame() {
      clearInterval(timer);
      
      const finalMessage = score >= 50 ? "🏆 Amazing!" : score >= 30 ? "🎉 Good job!" : "💪 Keep trying!";
      
      if (confirm(`⏱️ Time's up!\n\n${finalMessage}\nYour final score: ${score} points\n\nClick OK to return home.`)) {
        window.location.href = "index.php";
      } else {
        window.location.href = "index.php";
      }
    }

    startGame();
  </script>
</body>
</html>