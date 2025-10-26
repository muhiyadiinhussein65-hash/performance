<?php
$page_title = "Sumarry Page";
include 'head.php'; 
?>

<body class="bg-background-light dark:bg-background-dark font-display">
    <div class="flex flex-col h-screen">
        <header class="flex-shrink-0 p-4">
            <div class="flex items-center justify-between">
                <button class="text-gray-800 dark:text-gray-200">
                    <svg fill="currentColor" height="24" viewBox="0 0 256 256" width="24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z">
                        </path>
                    </svg>
                </button>
                <div class="w-2/3 mx-auto h-2 bg-gray-200 dark:bg-gray-700 rounded-full">
                    <div class="w-1/3 h-full bg-primary rounded-full"></div>
                </div>
                <div class="w-6"></div>
            </div>
        </header>
        <main class="flex-grow flex flex-col justify-center px-6">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Let's get to know you</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Personalize your learning journey.</p>
            </div>
            <div class="space-y-6">
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="name">Name</label>
                    <input
                        class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-primary focus:border-primary h-14 p-4 text-base"
                        id="name" placeholder="Enter your name" value="" />
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="school">School /
                        University</label>
                    <input
                        class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-primary focus:border-primary h-14 p-4 text-base"
                        id="school" placeholder="Where do you study?" value="" />
                </div>
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="subjects">Subject
                        Preferences</label>
                    <select
                        class="form-select appearance-none w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary h-14 p-4 text-base"
                        id="subjects">
                        <option disabled="" selected="" value="">Choose your subjects</option>
                        <option value="math">Mathematics</option>
                        <option value="science">Science</option>
                        <option value="history">History</option>
                        <option value="english">English</option>
                    </select>
                </div>
                <div class="flex flex-col items-center">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Choose Your Avatar</label>
                    <div class="flex items-center space-x-2">
                        <button class="relative">
                            <img alt="Avatar 1"
                                class="size-14 rounded-full border-2 border-transparent ring-2 ring-primary ring-offset-2 ring-offset-background-light dark:ring-offset-background-dark"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAXC-gIHlZylbZfciJIR-edLKzhlYAESJbNxFWMjnrmBPxLeiZ6RPi8sI3XBbGrYzhvmRoJqF32W46xbI2HiaJCfLZHoXR69O04LQOys0Onc_7ZJ3P5cop22r0yQ7cqRS4FY7XBepmmhl1wqcWBtfa6oagrNL0ee2ZJODCByi6AKNhBXvd1dZzgmRStsioMhPvhcYkKtxp-wWdXfNDPs87nra1XqippuLFB_4YCqOlKX9u3CUXX4PlrhOMdfUk_h6vWSSNo67znf0xG" />
                        </button>
                        <button class="relative">
                            <img alt="Avatar 2"
                                class="size-14 rounded-full border-2 border-transparent hover:ring-2 hover:ring-primary hover:ring-offset-2 hover:ring-offset-background-light dark:hover:ring-offset-background-dark"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDGQyvTEQdAfeoIXxZpHS-k1Ae-F0RPLvsgRZy-vXtivhfOVx85Irh-iHH46QWmBcU3p6Yz1Lg8NCbW8BIT02mdJ--uuSRM6hbjUnQyUDrMNF3ghR3Oe_UDYF8uL9AMBSOw4nB-iIlTObkx7nzzraIyLcXtiCbaTUoW-BXOV5B8YMrROCU29yGHcpdQiW6aEsQdlOFoBcjfJXXHYTxE8MnhWpk_qWW_5RxdtTtVry8WJ4ECrPitUeFo2rcaC7dTngyFnBgERSApzGtI" />
                        </button>
                        <button class="relative">
                            <img alt="Avatar 3"
                                class="size-14 rounded-full border-2 border-transparent hover:ring-2 hover:ring-primary hover:ring-offset-2 hover:ring-offset-background-light dark:hover:ring-offset-background-dark"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAX0Uc2kAxnw7APtgYwMBjlJ2Va7zFFBmA1oxo6csUiFPobz5-Ur7Odk_PwgOP676kRGzj7Bx4PqkQ9kByDjgK-zqjQJEL2Rczf75b7YMdy_j9Qv-7k2meRXMUdp-1AyVg4oDTkDaulFnxmRc58FuFbgaezdC7yJPXFD5jOVVu1SzjGCRKZks1Rub_4zSLz7K13KpUOZPlFRlkJWsflxyQ79Cj5Zo0PEX13pv19FPn9YekTdUpoMFooMHRFwewhkBqsKrCkCbvTQo98" />
                        </button>
                        <button class="relative">
                            <img alt="Avatar 4"
                                class="size-14 rounded-full border-2 border-transparent hover:ring-2 hover:ring-primary hover:ring-offset-2 hover:ring-offset-background-light dark:hover:ring-offset-background-dark"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAth3C3ad9sweYgBO7DLPMMcicWrhC-4P2m3BW61oEXy9G-canucG7-DxIrs6mGgaxils8D5KXwnL8hb0XPhSfqQ1Amq0RsLs9vpaN-ey5CllfuI0vcAVtEWm7_qrMlQXGKQYV8Fb56_Vioxr5qDIh4EnloKWhfEOimvaJJkCrHnHDs2NlwSA5BsboMFkebJAT7fHxhy63jl9mrQoaqujLRw_1RdV3fxfGM9DrYoh_he6pt7hLHlJFpBnhJAR56E63EIbTZGKWwEEXO" />
                        </button>
                        <button
                            class="size-14 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center hover:bg-primary/20 dark:hover:bg-primary/30">
                            <svg fill="currentColor" height="24" viewBox="0 0 256 256" width="24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </main>
        <footer class="flex-shrink-0 p-6">
            <button
                class="w-full h-14 px-5 rounded-xl bg-primary text-white text-base font-bold flex items-center justify-center shadow-lg shadow-primary/30 hover:bg-primary/90 transition-colors">
                Continue
            </button>
        </footer>
    </div>

</body>

</html>