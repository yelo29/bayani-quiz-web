// Quiz JavaScript - Visual Effects Only

let correctAnswer = '';

// Initialize quiz state from PHP
function initQuiz() {
    const quizData = document.getElementById('quizData');
    if (quizData) {
        correctAnswer = quizData.dataset.correctAnswer.toLowerCase();
    }

    // Add click handlers to answer buttons for visual feedback only
    const buttons = document.querySelectorAll('.answer-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const selectedOption = this.getAttribute('data-option');
            selectAnswer(selectedOption);
        });
    });
}

// Answer selection - visual feedback only (form submits immediately)
function selectAnswer(selectedOption) {
    // Lock all buttons
    const buttons = document.querySelectorAll('.answer-btn');
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.classList.add('pointer-events-none');
    });

    // Get selected button
    const selectedBtn = document.querySelector(`[data-option="${selectedOption}"]`);

    // Show correct answer
    const correctBtn = document.querySelector(`[data-option="${correctAnswer}"]`);
    if (correctBtn) {
        correctBtn.classList.remove('bg-gray-100');
        correctBtn.classList.add('bg-green-500', 'text-white', 'border-green-500');
    }

    // Handle wrong answer
    if (selectedOption !== correctAnswer && selectedBtn) {
        selectedBtn.classList.remove('bg-gray-100');
        selectedBtn.classList.add('bg-red-500', 'text-white', 'border-red-500', 'shake');
    }

    // Trigger confetti for correct answers
    if (selectedOption === correctAnswer) {
        if (typeof triggerConfetti === 'function') {
            triggerConfetti();
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQuiz);
} else {
    initQuiz();
}
