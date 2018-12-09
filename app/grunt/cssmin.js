module.exports = function (grunt, options) {
    return {
        css: {
            files: [{
                expand: true,
                cwd: 'html/assets/css/',
                src: [ '*.css', '!*.min.css' ],
                dest: 'html/assets/css/',
                ext: '.min.css'
            }]
        }
    }
}
