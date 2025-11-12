let timeLeft = 30;
let timerInterval;
let currentPuzzle = {};
let score = 0;

function startGame() {
    score = 0;
    updateScoreDisplay();

    // set time based on difficulty passed from PHP
    if (difficulty === "easy") {
        timeLeft = 60;
    } else if (difficulty === "medium") {
        timeLeft = 40;
    } else if (difficulty === "hard") {
        timeLeft = 20;
    }

    const timerEl = document.getElementById("timer");
    if (timerEl) {
        timerEl.innerText = timeLeft;
        timerEl.style.color = "#FFEB3B"; // Reset to default color
    }
    const messageEl = document.getElementById("message");
    if (messageEl) {
        messageEl.innerText = "";
        messageEl.className = "";
    }
    getPuzzle();

    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        timeLeft--;
        if (timerEl) {
            timerEl.innerText = timeLeft;
        }
        
        // Add warning color when time is low
        if (timeLeft <= 10) {
            if (timerEl) {
                timerEl.style.color = "#FF6B6B";
            }
            const statBox = timerEl ? timerEl.closest(".stat-box") : null;
            if (statBox) {
                statBox.style.borderColor = "rgba(255, 107, 107, 0.5)";
            }
        }
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            endGame();
        }
    }, 1000);
}

function getPuzzle() {
    const puzzleDiv = document.getElementById("puzzle");
    if (!puzzleDiv) return;
    
    puzzleDiv.innerHTML = '<div class="loading"></div><p class="loading-text">Loading puzzle...</p>';
    
    fetch("https://marcconrad.com/uob/banana/api.php")
        .then(res => res.json())
        .then(data => {
            currentPuzzle = data;
            puzzleDiv.innerHTML = `<img src="${data.question}" alt="Math Puzzle">`;
            const answerInput = document.getElementById("answer");
            if (answerInput) {
                answerInput.value = "";
                answerInput.focus();
            }
        })
        .catch(err => {
            puzzleDiv.innerHTML = '<p class="error-text">⚠️ Failed to load puzzle. Please refresh the page.</p>';
            console.error(err);
        });
}

function submitAnswer() {
    const answerInput = document.getElementById("answer");
    if (!answerInput) return;
    
    let userAnswer = answerInput.value;
    
    if (userAnswer === "") {
        showMessage("Please enter an answer!", "warning");
        return;
    }
    
    const userAnswerNum = parseInt(userAnswer);
    if (isNaN(userAnswerNum) || userAnswerNum < 0 || userAnswerNum > 9) {
        showMessage("Please enter a number between 0 and 9!", "warning");
        return;
    }
    
    const messageEl = document.getElementById("message");
    
    if (userAnswerNum == currentPuzzle.solution) {
        score += 10;
        timeLeft += 5;
        updateScoreDisplay();
        
        showMessage("✅ Correct! +10 points, +5 seconds", "success");
        showNotification("✅ Correct Answer! +10 points", "success");
        
        // Load next puzzle after short delay
        setTimeout(() => {
            if (messageEl) {
                messageEl.innerText = "";
                messageEl.className = "";
            }
            getPuzzle();
        }, 1500);
    } else {
        timeLeft -= 5;
        
        showMessage(`❌ Wrong! -5 seconds (Correct answer: ${currentPuzzle.solution})`, "error");
        showNotification("❌ Wrong Answer! -5 seconds", "error");
        
        // Load next puzzle after showing answer
        setTimeout(() => {
            if (messageEl) {
                messageEl.innerText = "";
                messageEl.className = "";
            }
            getPuzzle();
        }, 2000);
    }
}

function updateScoreDisplay() {
    document.getElementById("score-value").innerText = score;
}

function showNotification(message, type) {
    const notif = document.getElementById("notification");
    if (!notif) {
        const newNotif = document.createElement("div");
        newNotif.id = "notification";
        document.body.appendChild(newNotif);
    }
    
    const notification = document.getElementById("notification");
    notification.innerText = message;
    notification.className = "show";
    
    if (type === "success") {
        notification.style.background = "linear-gradient(135deg, #10b981 0%, #059669 100%)";
    } else if (type === "error") {
        notification.style.background = "linear-gradient(135deg, #ef4444 0%, #dc2626 100%)";
    }
    
    setTimeout(() => {
        notification.className = "";
    }, 3000);
}

function showMessage(message, type) {
    const messageEl = document.getElementById("message");
    if (!messageEl) return;
    
    messageEl.innerText = message;
    messageEl.className = `message-${type}`;
}

function endGame() {
    clearInterval(timerInterval);
    
    // Disable input and button
    const answerInput = document.getElementById("answer");
    const submitBtn = document.getElementById("submit-btn");
    if (answerInput) answerInput.disabled = true;
    if (submitBtn) submitBtn.disabled = true;

    fetch("save_score.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "score=" + score + "&difficulty=" + difficulty
    }).then(() => {
        window.location.href = "leaderboard.php";
    }).catch(err => {
        console.error("Error saving score:", err);
        window.location.href = "leaderboard.php";
    });
}

// Handle Enter key press on answer input
document.addEventListener("DOMContentLoaded", function() {
    const answerInput = document.getElementById("answer");
    if (answerInput) {
        answerInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                submitAnswer();
            }
        });
    }
});

// auto-start when page loads
window.onload = startGame;