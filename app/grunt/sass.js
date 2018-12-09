// compile our application less files (and this app's bootstrap theme)
module.exports = function (grunt, options) {
    return {
        options: {
            sourceMap: false
        },
        compile: {
            files: {
                "./html/assets/css/bootstrap.css": "./assets/css/bootstrap.scss",
                "./html/assets/css/style.css": "./assets/css/style.scss"
            }
        }
    };
};
