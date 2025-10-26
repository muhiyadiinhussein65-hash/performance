/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/**/*.{html,js,ts,jsx,tsx}"],
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                // Primary Colors
                "primary": "#137fec",
                "primary-accent": "#FAB440",
                "active-element": "#4E47C6",

                // Background Colors
                "background-light": "#f6f7f8",
                "background-dark": "#07143F",

                // Text Colors
                "dark-text": "#07143F",
                "foreground-light": "#111418",
                "foreground-dark": "#f6f7f8",
                "text-light": "#1f2937",
                "text-dark": "#e5e7eb",
                "subtext-light": "#6b7280",
                "subtext-dark": "#9ca3af",
                "subtle-light": "#617589",
                "subtle-dark": "#a0b3c6",

                // Status Colors
                "success": "#22c55e",
                "success-light": "#22c55e",
                "success-dark": "#4ade80",
                "error": "#ef4444",
                "error-light": "#ef4444",
                "error-dark": "#f87171",
                "warning": "#f59e0b",

                // UI Colors
                "card-light": "#ffffff",
                "card-dark": "#182431",
                "border-light": "#e5e7eb",
                "border-dark": "#374151",

                // Special Colors
                "gold": "#ffc700",
                "blue-vibrant": "#4E47C6",
                "green-vibrant": "#28a745",
                "gold-vibrant": "#FAB440"
            },
            fontFamily: {
                "display": ["Plus Jakarta Sans", "Noto Sans", "sans-serif"]
            },
            borderRadius: {
                "DEFAULT": "0.75rem",
                "lg": "1rem",
                "xl": "1.5rem",
                "full": "9999px"
            },
            boxShadow: {
                'input': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                'input-focus': '0 0 0 2px #137fec'
            },
            animation: {
                'burst': 'burst 0.5s ease-out forwards',
                'xp-gain': 'slide-up-fade-out 1s forwards'
            },
            keyframes: {
                burst: {
                    '0%': { transform: 'scale(0.5)', opacity: '0' },
                    '50%': { transform: 'scale(1.2)', opacity: '1' },
                    '100%': { transform: 'scale(1)', opacity: '1' }
                },
                'slide-up-fade-out': {
                    '0%': { transform: 'translateY(0)', opacity: '1' },
                    '100%': { transform: 'translateY(-20px)', opacity: '0' }
                }
            }
        },
    },
    plugins: [
        function ({ addUtilities }) {
            addUtilities({
                '.crown-gold': {
                    color: '#FFD700'
                },
                '.crown-silver': {
                    color: '#C0C0C0'
                },
                '.crown-bronze': {
                    color: '#CD7F32'
                },
                '.avatar-ring': {
                    'box-shadow': '0 0 0 4px #f6f7f8'
                },
                '.dark .avatar-ring': {
                    'box-shadow': '0 0 0 4px #101922'
                }
            })
        }
    ],
}