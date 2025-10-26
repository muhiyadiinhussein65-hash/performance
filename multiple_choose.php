<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Stitch Design</title>
    <link href="data:image/x-icon;base64," rel="icon" type="image/x-icon" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&amp;display=swap"
        rel="stylesheet" />
    <script src="tailwind-cdn.js"></script>
    <link href="style.css" rel="stylesheet" />
    <script src="tailwind.config.js"></script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display">
    <div class="flex flex-col min-h-screen">
        <header class="fixed top-0 left-0 right-0 bg-background-light dark:bg-background-dark z-10 px-4 pt-4">
            <div class="flex items-center gap-4">
                <button class="text-foreground-light dark:text-foreground-dark">
                    <svg fill="currentColor" height="28" viewBox="0 0 256 256" width="28"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z">
                        </path>
                    </svg>
                </button>
                <div class="flex-1 h-3 bg-border-light dark:bg-border-dark rounded-full">
                    <div class="h-full w-1/5 bg-primary rounded-full"></div>
                </div>
            </div>
        </header>
        <main class="flex-grow pt-20 pb-40 px-4">
            <h1 class="text-3xl font-bold text-foreground-light dark:text-foreground-dark mb-8">
                What is the primary function of the hippocampus in the human brain?
            </h1>
            <div class="space-y-4 group">
                <div
                    class="option-card border-2 border-border-light dark:border-border-dark p-5 rounded-lg text-lg font-bold text-foreground-light dark:text-foreground-dark cursor-pointer transition-all duration-200 hover:border-primary/50 correct-answer">
                    Memory Formation
                </div>
                <div
                    class="option-card border-2 border-border-light dark:border-border-dark p-5 rounded-lg text-lg font-bold text-foreground-light dark:text-foreground-dark cursor-pointer transition-all duration-200 hover:border-primary/50 incorrect-answer">
                    Motor Control
                </div>
                <div
                    class="option-card border-2 border-border-light dark:border-border-dark p-5 rounded-lg text-lg font-bold text-foreground-light dark:text-foreground-dark cursor-pointer transition-all duration-200 hover:border-primary/50">
                    Sensory Processing
                </div>
                <div
                    class="option-card border-2 border-border-light dark:border-border-dark p-5 rounded-lg text-lg font-bold text-foreground-light dark:text-foreground-dark cursor-pointer transition-all duration-200 hover:border-primary/50">
                    Emotional Regulation
                </div>
            </div>
        </main>
        <footer class="fixed bottom-0 left-0 right-0">
            <div
                class="p-4 border-t border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark">
                <button
                    class="w-full h-14 bg-primary text-white text-xl font-bold rounded-lg disabled:bg-border-light dark:disabled:bg-border-dark disabled:text-gray-500 dark:disabled:text-gray-400"
                    disabled="">
                    CHECK
                </button>
            </div>
        </footer>
    </div>
    <script>
    // Simple logic to handle option selection and button state
    const options = document.querySelectorAll('.option-card');
    const checkButton = document.querySelector('footer button');
    const mainGroup = document.querySelector('.group');
    options.forEach(option => {
        option.addEventListener('click', () => {
            // Remove 'selected' from all other options in case user changes their mind
            options.forEach(opt => mainGroup.classList.remove('selected'));
            // Add 'selected' to the parent group
            mainGroup.classList.add('selected');
            // Visually mark the selected card by changing its parent styles (see CSS)
            // For demonstration, we'll just add a temporary class to the clicked element.
            // In a real app, you'd manage state.
            document.querySelectorAll('.option-card').forEach(c => c.style.borderColor = '');
            option.style.borderColor = '#137fec';
            checkButton.disabled = false;
        });
    });
    checkButton.addEventListener('click', () => {
        // This is a simplified logic. In a real app, you'd check against the correct answer.
        // For this demo, let's assume the first option is correct.
        const selectedOption = document.querySelector('.option-card[style*="137fec"]');
        if (selectedOption && selectedOption.textContent.trim() === 'Memory Formation') {
            mainGroup.classList.remove('selected');
            mainGroup.classList.add('correct');
            showFeedback(true);
        } else {
            mainGroup.classList.remove('selected');
            mainGroup.classList.add('incorrect');
            showFeedback(false);
        }
    });

    function showFeedback(isCorrect) {
        const footer = document.querySelector('footer');
        footer.innerHTML = ''; // Clear the check button
        if (isCorrect) {
            footer.innerHTML = `
                <div class="p-4 bg-success/10 border-t-4 border-success">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-success">✅ Awesome!</h2>
                        <span class="font-bold text-warning text-lg">+10 XP</span>
                    </div>
                    <button class="w-full h-14 bg-primary text-white text-xl font-bold rounded-lg">
                        CONTINUE
                    </button>
                </div>
            `;
        } else {
            footer.innerHTML = `
                <div class="p-4 bg-error/10 border-t-4 border-error">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-error">❌ Almost! Review the summary again.</h2>
                    </div>
                    <button class="w-full h-14 bg-primary text-white text-xl font-bold rounded-lg">
                        CONTINUE
                    </button>
                </div>
            `;
        }
    }
    </script>

</body>

</html>