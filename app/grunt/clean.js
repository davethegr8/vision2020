// compile our application less files (and this app's bootstrap theme)
module.exports = function (grunt, options) {
    return {
        css: [
            'html/assets/css/*.css',
            '!html/assets/css/*.min.css'
        ]
    };
};
