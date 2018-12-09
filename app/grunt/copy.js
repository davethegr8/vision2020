module.exports = function(grunt, options) {
    return {
        bootstrap: {
            cwd: './node_modules/bootstrap/dist/js/',
            src: 'bootstrap.min.js',
            dest: './html/assets/js/',
            flatten: false,
            expand: true
        },
        jquery: {
            cwd: './node_modules/jquery/dist/',
            src: 'jquery.min.js',
            dest: './html/assets/js/',
            flatten: false,
            expand: true
        },
        popper: {
            cwd: './node_modules/popper.js/dist/umd/',
            src: 'popper.min.js',
            dest: './html/assets/js/',
            flatten: false,
            expand: true
        },
        fa: {
            cwd: './node_modules/font-awesome/css/',
            src: 'font-awesome.min.css',
            dest: './html/assets/css/',
            flatten: false,
            expand: true
        }
    }
}
