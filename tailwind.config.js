import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans:    ['Space Grotesk', ...defaultTheme.fontFamily.sans],
                heading: ['Archivo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    purple: '#533AB7',
                    teal:   '#1D9E75',
                    coral:  '#D85A30',
                    pink:   '#D4537E',
                    amber:  '#BA7517',
                    blue:   '#378ADD',
                },
                dark: {
                    base:     '#0F172A',
                    surface:  '#1E293B',
                    elevated: '#2D3748',
                    border:   '#334155',
                },
                light: {
                    base:    '#F8FAFC',
                    surface: '#FFFFFF',
                    border:  '#E2E8F0',
                },
            },
            backgroundImage: {
                'aurora':         'linear-gradient(135deg, #533AB7 0%, #1D9E75 50%, #378ADD 100%)',
                'aurora-dark':    'linear-gradient(135deg, #2D1B69 0%, #0D5C42 50%, #1A4F8A 100%)',
                'aurora-subtle':  'linear-gradient(135deg, rgba(83,58,183,0.15) 0%, rgba(29,158,117,0.10) 50%, rgba(55,138,221,0.15) 100%)',
                'brand-gradient': 'linear-gradient(135deg, #533AB7, #D4537E)',
            },
            animation: {
                'fade-in-up':   'fadeInUp 0.6s ease-out forwards',
                'pulse-glow':   'pulseGlow 2s ease-in-out infinite',
                'aurora-shift': 'auroraShift 8s ease-in-out infinite',
            },
            keyframes: {
                fadeInUp: {
                    '0%':   { opacity: '0', transform: 'translateY(24px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                pulseGlow: {
                    '0%, 100%': { boxShadow: '0 0 20px rgba(83,58,183,0.4)' },
                    '50%':      { boxShadow: '0 0 40px rgba(83,58,183,0.7)' },
                },
                auroraShift: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%':      { backgroundPosition: '100% 50%' },
                },
            },
        },
    },

    plugins: [forms, typography],
};
