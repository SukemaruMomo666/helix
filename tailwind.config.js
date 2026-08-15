/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {},
    },
    // INI BAGIAN PALING PENTING UNTUK PROJECT YANG SUDAH JALAN
    corePlugins: {
        preflight: false, // Mematikan CSS Reset Tailwind agar CSS lama Anda tidak hancur
    },
};
