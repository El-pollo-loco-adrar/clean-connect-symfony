/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./templates/**/*.html.twig",
        "./assets/**/*.vue",
        "./assets/**/*.js",
        "./assets/styles/**/*.css",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('daisyui'),
        
    ],
    daisyui: {
        themes: ["light", "dark"],
    },
}