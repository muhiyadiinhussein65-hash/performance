<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Stitch Design</title>
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
    <link as="style"
        href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
        onload="this.rel='stylesheet'" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script src="tailwind-cdn.js"></script>
    <script src="tailwind.config.js"></script>
    <link href="style.css" rel="stylesheet" />

</head>

<body class="font-display bg-background-light dark:bg-background-dark">
    <div class="flex flex-col min-h-screen">
        <header
            class="fixed top-0 left-0 right-0 p-4 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm z-10">
            <div class="w-full bg-border-light dark:bg-border-dark rounded-full h-2.5">
                <div class="bg-primary h-2.5 rounded-full" style="width: 60%"></div>
            </div>
        </header>
        <main class="flex-grow flex flex-col justify-center px-4 pt-20 pb-4">
            <div class="w-full max-w-md mx-auto">
                <h1 class="text-2xl font-bold text-text-light dark:text-text-dark mb-6 text-center">What is the capital
                    of
                    France?</h1>
                <div class="relative">
                    <textarea
                        class="w-full h-36 p-4 rounded-lg bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark placeholder-subtext-light dark:placeholder-subtext-dark border border-border-light dark:border-border-dark focus:outline-none focus:ring-2 focus:ring-primary/50 dark:focus:ring-primary/50 shadow-sm resize-none"
                        placeholder="Type your answer here..."></textarea>
                </div>
                <button
                    class="mt-6 w-full h-14 flex items-center justify-center rounded-xl bg-primary text-white text-lg font-bold tracking-wide shadow-lg hover:bg-primary/90 transition-colors duration-300">
                    SUBMIT
                </button>
            </div>
        </main>
        <footer class="w-full p-4">
            <div class="w-full max-w-md mx-auto">
                <div
                    class="flex items-center justify-center gap-2 p-3 rounded-lg bg-success-light/20 dark:bg-success-dark/20 text-success-light dark:text-success-dark">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p class="text-sm font-semibold">Correct! +10 XP.</p>
                </div>
            </div>
        </footer>
    </div>

</body>

</html>