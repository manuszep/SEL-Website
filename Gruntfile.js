module.exports = function (grunt) {
    "use strict";

    var path = require("path");
    var fs = require("fs");


    require("time-grunt")(grunt);
    require("load-grunt-config")(grunt, {
        configPath: path.join(process.cwd(), "grunt_tasks")
    });

    require("load-grunt-tasks")(grunt);

    grunt.registerTask("dev", [
        "clean:web_styles",
        "clean:web_scripts",
        "sass:dev",
        "autoprefixer:dev",
        "browserify:dev",
        //"bytesize:dev",
        "watch"
    ]);

    grunt.registerTask("build", [
        "clean:web_styles",
        "clean:web_scripts",
        "sass:dist",
        "autoprefixer:dev",
        "browserify:dev",
        "uglify:dist",
        "cssmin:dist"
    ]);

    grunt.registerTask("scsslintall", ["scsslint"])

    grunt.registerTask("default", ["dev"]);
};
