<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Quiz Question</title>
    <script src="tailwind-cdn.js"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&amp;display=swap"
        rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
    <script src="./tailwind.config.js"></script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display">
    <div class="flex flex-col min-h-screen">
        <header class="fixed top-0 left-0 right-0 bg-background-light dark:bg-background-dark z-10 px-4 pt-4">
            <div class="flex items-center justify-between">
                <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Question 1/5</p>
                <button class="text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-primary">
                    <svg fill="currentColor" height="28" viewBox="0 0 256 256" width="28"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 mt-2">
                <div class="bg-primary h-2.5 rounded-full" style="width: 20%"></div>
            </div>
        </header>
        <main class="flex-grow flex flex-col items-center justify-center px-4 pt-24 text-center">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white mb-12">The capital of France is Paris.</h1>
            <div class="w-full max-w-md space-y-4">
                <button
                    class="w-full flex items-center justify-center p-4 rounded-lg border-2 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-lg hover:border-primary/50 dark:hover:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary transition-colors duration-200">
                    <span class="text-2xl mr-3">👍</span> True
                </button>
                <button
                    class="w-full flex items-center justify-center p-4 rounded-lg border-2 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-lg hover:border-primary/50 dark:hover:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary dark:focus:border-primary transition-colors duration-200">
                    <span class="text-2xl mr-3">👎</span> False
                </button>
            </div>
        </main>
        <footer class="p-4 bg-background-light dark:bg-background-dark">
            <button
                class="w-full bg-primary text-white font-bold py-4 rounded-lg text-lg disabled:bg-primary/50 disabled:cursor-not-allowed"
                disabled="">
                SUBMIT
            </button>
        </footer>
        <div class="fixed bottom-0 left-0 right-0 bg-success/10 dark:bg-success/20 p-4 border-t-4 border-success hidden"
            id="feedback-correct">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                        <p class="font-bold text-success">Correct!</p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">You earned +10 XP</p>
                    </div>
                </div>
                <button class="bg-success text-white font-bold py-2 px-6 rounded-lg text-md">CONTINUE</button>
            </div>
        </div>
        <div class="fixed bottom-0 left-0 right-0 bg-error/10 dark:bg-error/20 p-4 border-t-4 border-error hidden"
            id="feedback-incorrect">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                        <p class="font-bold text-error">Incorrect.</p>
                        <p class="text-sm text-slate-600 dark:text-slate-300">The correct answer was: True</p>
                    </div>
                </div>
                <button class="bg-error text-white font-bold py-2 px-6 rounded-lg text-md">CONTINUE</button>
            </div>
        </div>
    </div>
    <script>
    const choiceButtons = document.querySelectorAll('main button');
    const submitButton = document.querySelector('footer button');
    const feedbackCorrect = document.getElementById('feedback-correct');
    const feedbackIncorrect = document.getElementById('feedback-incorrect');
    let selectedButton = null;
    choiceButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Reset styles of all buttons
            choiceButtons.forEach(btn => {
                btn.classList.remove('bg-primary/10', 'dark:bg-primary/20', 'border-primary',
                    'dark:border-primary');
                btn.classList.add('border-slate-300', 'dark:border-slate-700');
            });
            // Apply selected style to the clicked button
            button.classList.add('bg-primary/10', 'dark:bg-primary/20', 'border-primary',
                'dark:border-primary');
            button.classList.remove('border-slate-300', 'dark:border-slate-700');
            selectedButton = button;
            submitButton.disabled = false;
        });
    });
    submitButton.addEventListener('click', () => {
        // This is a dummy logic. In a real app, you'd check the actual answer.
        // Let's assume "True" is the correct answer for this question.
        if (selectedButton && selectedButton.textContent.includes('True')) {
            feedbackCorrect.classList.remove('hidden');
            feedbackIncorrect.classList.add('hidden');
        } else {
            feedbackIncorrect.classList.remove('hidden');
            feedbackCorrect.classList.add('hidden');
        }
        submitButton.parentElement.classList.add('hidden'); // Hide submit button container
    });
    </script>


</body>

</html>