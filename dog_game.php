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
        <div class="stat-value" id="timer">35</div>
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
      
      // Create end game modal
      const modal = document.createElement('div');
      modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        animation: fadeIn 0.3s ease;
      `;
      
      const modalContent = document.createElement('div');
      modalContent.style.cssText = `
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 2px solid rgba(255, 140, 66, 0.3);
        border-radius: 32px;
        padding: 48px 40px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s ease;
      `;
      
      modalContent.innerHTML = `
        <h2 style="font-family: 'Fredoka', sans-serif; color: #FF8C42; font-size: 2.5em; margin-bottom: 16px; text-shadow: 0 0 30px rgba(255, 140, 66, 0.6);">
          ⏱️ Time's Up!
        </h2>
        <p style="font-size: 1.8em; color: #FFFFFF; margin: 16px 0; font-weight: 700;">
          ${finalMessage}
        </p>
        <p style="font-size: 2.5em; color: #FF8C42; font-weight: 700; font-family: 'Fredoka', sans-serif; text-shadow: 0 0 20px rgba(255, 140, 66, 0.5); margin: 24px 0;">
          Score: ${score}
        </p>
        
        <div style="background: rgba(255, 235, 59, 0.15); border: 2px solid rgba(255, 235, 59, 0.3); border-radius: 20px; padding: 24px; margin: 32px 0;">
          <p style="color: #FFD93D; font-size: 1.3em; font-weight: 700; margin-bottom: 12px;">
            🍌 Want More Challenge?
          </p>
          <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.05em; line-height: 1.6;">
            Login to play our <strong style="color: #FFEB3B;">Banana Puzzle Game</strong> with different difficulty levels and compete on the leaderboard!
          </p>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap;">
          <button onclick="window.location.href='index.php'" style="flex: 1; min-width: 150px; background: linear-gradient(135deg, #FFEB3B 0%, #FFD93D 100%); color: #1A1B26; border: none; padding: 16px 24px; border-radius: 16px; font-size: 1.1em; font-weight: 700; font-family: 'Fredoka', sans-serif; cursor: pointer; text-transform: uppercase; box-shadow: 0 4px 16px rgba(255, 235, 59, 0.4); transition: all 0.3s ease;">
            🍌 Login & Play
          </button>
          <button onclick="location.reload()" style="flex: 1; min-width: 150px; background: rgba(255, 255, 255, 0.1); border: 2px solid rgba(255, 140, 66, 0.3); color: #FF8C42; padding: 16px 24px; border-radius: 16px; font-size: 1.1em; font-weight: 700; font-family: 'Fredoka', sans-serif; cursor: pointer; text-transform: uppercase; backdrop-filter: blur(10px); transition: all 0.3s ease;">
            🔄 Play Again
          </button>
        </div>
        
        <style>
          @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
          }
          @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
          }
          button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 140, 66, 0.5) !important;
          }
        </style>
      `;
      
      modal.appendChild(modalContent);
      document.body.appendChild(modal);
    }

    startGame();
  </script>
</body>
</html>