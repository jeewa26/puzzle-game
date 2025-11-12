<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🍌 Guest Banana Game</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="guest-style-game.css">
</head>
<body>
  <div class="game-container">
    <h1>🍌 Banana Puzzle Game</h1>
    <p class="guest-badge">Guest Mode - Beginner Level</p>
    
    <div class="stats-bar">
      <div class="stat-box">
        <div class="stat-label">Time</div>
        <div class="stat-value" id="timer">90</div>
      </div>
      <div class="stat-box">
        <div class="stat-label">Score</div>
        <div class="stat-value" id="score">0</div>
      </div>
    </div>

    <div id="game-area">
      <div class="puzzle-container">
        <div id="puzzle">
          <div class="loading"></div>
          <p class="loading-text">Loading puzzle...</p>
        </div>
      </div>
      
      <input type="number" id="answer" placeholder="Enter your answer (0-9)" min="0" max="9">
      
      <button id="submit-btn" onclick="submitAnswer()">
        Submit Answer ✓
      </button>
      
      <p id="message"></p>
    </div>

    <div class="back-link">
      <a href="index.php">← Back to Home</a>
    </div>
  </div>

  <script>
    let score = 0;
    let timeLeft = 90;
    let timer;
    let currentAnswer = null;
    const difficulty = "easy";

    function startGame() {
      document.getElementById("message").innerText = "";
      loadPuzzle();
      startTimer();
    }

    function startTimer() {
      timer = setInterval(() => {
        timeLeft--;
        document.getElementById("timer").innerText = timeLeft;
        
        // Warning when 10 seconds left
        if (timeLeft === 10) {
          document.getElementById("timer").style.color = "#FF6B6B";
          document.querySelector("#timer").closest(".stat-box").style.borderColor = "rgba(255, 107, 107, 0.5)";
        }
        
        if (timeLeft <= 0) endGame();
      }, 1000);
    }

    function loadPuzzle() {
      const puzzleDiv = document.getElementById("puzzle");
      puzzleDiv.innerHTML = '<div class="loading"></div><p class="loading-text">Loading puzzle...</p>';
      
      fetch(`get_puzzle.php?difficulty=${difficulty}`)
        .then(res => {
          if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
          }
          return res.json();
        })
        .then(data => {
          if (data.error) {
            showError(data.error || "Failed to load puzzle. Please try again.");
            return;
          }
          if (data.image && data.answer !== undefined && data.answer !== null) {
            currentAnswer = parseInt(data.answer);
            puzzleDiv.innerHTML = `<img src="${data.image}" alt="Math Puzzle">`;
            document.getElementById("answer").value = "";
            document.getElementById("answer").focus();
          } else {
            showError("Failed to load puzzle. Invalid response from server.");
          }
        })
        .catch(error => {
          console.error("Error:", error);
          showError("Error loading puzzle. Please check your connection and try again.");
        });
    }

    function submitAnswer() {
      const userAnswer = parseInt(document.getElementById("answer").value);
      
      if (isNaN(userAnswer)) {
        showMessage("Please enter a number!", "warning");
        return;
      }
      
      if (userAnswer < 0 || userAnswer > 9) {
        showMessage("Please enter a number between 0 and 9!", "warning");
        return;
      }
      
      checkAnswer(userAnswer);
    }

    function checkAnswer(userAnswer) {
      const messageEl = document.getElementById("message");
      
      if (userAnswer === currentAnswer) {
        score += 10;
        document.getElementById("score").innerText = score;
        showMessage("✅ Correct! +10 points", "success");
        
        // Load next puzzle after short delay
        setTimeout(() => {
          messageEl.innerText = "";
          messageEl.className = "";
          loadPuzzle();
        }, 1500);
      } else {
        showMessage(`❌ Wrong! The answer was ${currentAnswer}`, "error");
        
        // Load next puzzle after showing answer
        setTimeout(() => {
          messageEl.innerText = "";
          messageEl.className = "";
          loadPuzzle();
        }, 2000);
      }
    }

    function showMessage(text, type) {
      const messageEl = document.getElementById("message");
      messageEl.innerText = text;
      messageEl.className = `message-${type}`;
    }

    function showError(text) {
      const puzzleDiv = document.getElementById("puzzle");
      puzzleDiv.innerHTML = `<p class="error-text">${text}</p>`;
    }

    // Allow Enter key to submit
    document.getElementById("answer").addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        submitAnswer();
      }
    });

    function endGame() {
      clearInterval(timer);
      
      // Disable input and button
      document.getElementById("answer").disabled = true;
      document.getElementById("submit-btn").disabled = true;
      
      const finalMessage = score >= 50 ? "🏆 Excellent!" : score >= 30 ? "🎉 Good job!" : "💪 Keep practicing!";
      
      // Create end game modal
      const modal = document.createElement('div');
      modal.className = 'end-game-modal';
      
      const modalContent = document.createElement('div');
      modalContent.className = 'modal-content';
      
      modalContent.innerHTML = `
        <h2 class="modal-title">⏱️ Time's Up!</h2>
        <p class="modal-message">${finalMessage}</p>
        <p class="modal-score">Score: ${score}</p>
        
        <div class="account-promo">
          <p class="promo-title">🎮 Want to Play More?</p>
          <p class="promo-text">
            Create an account to unlock:
          </p>
          <ul class="feature-list">
            <li>🔥 <strong>Medium & Hard Levels</strong> - More challenging puzzles</li>
            <li>⏱️ <strong>Different Time Limits</strong> - Test your speed</li>
            <li>🏆 <strong>Leaderboard</strong> - Compete with other players</li>
            <li>📊 <strong>Track Your Progress</strong> - See your stats and improvements</li>
          </ul>
        </div>
        
        <div class="modal-buttons">
          <button class="btn-register" onclick="window.location.href='register.php'">
            🚀 Create Account
          </button>
          <button class="btn-login" onclick="window.location.href='index.php'">
            🔑 Login
          </button>
        </div>
        
        <div class="replay-section">
          <button class="btn-replay" onclick="location.reload()">
            🔄 Play Again as Guest
          </button>
        </div>
      `;
      
      modal.appendChild(modalContent);
      document.body.appendChild(modal);
    }

    startGame();
  </script>
</body>
</html>