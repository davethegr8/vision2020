module.exports = function(grunt, options) {
    return {
        assets: {
            files: [
                'Gruntfile.js',
                'grunt/*.js',
                './assets/css/**/*.scss'
            ],
            tasks: ['watch-tasks'],
            options: {
                interrupt: true
            }
        }
    }
}
