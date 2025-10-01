module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/Shared/Resources/Posts/Schemas/PostForm.php',
    ],
    safelist: [
        'text-xl',
        'text-2xl',
        'text-3xl',
        'text-4xl',
        'max-w-md',
        'mx-auto',
        'mt-8',
        'flex',
        'justify-end',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
