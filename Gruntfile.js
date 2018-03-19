module.exports = function(grunt) {
	require('jit-grunt')(grunt);

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		less: {
			development: {
				options: {
					compress: true,
					yuicompress: true,
					optimization: 2,
				},
				files: {
					'public/css/<%= pkg.name %>.css': 'src/less/main.less'
				}
			}
		},
		postcss: {
			options: {
				processors: [
					require('autoprefixer')({
						browsers: ['last 2 versions']
					})
				]
			},
			dist: {
				src: 'public/css/<%= pkg.name %>.css'
			}
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			development: {
				files: {
					'public/js/vendor.min.js': [
						'node_modules/jquery/dist/jquery.js'
					],
					'public/js/<%= pkg.name %>.min.js': ['src/js/main.js']
				}
			}
		},
		watch: {
			styles: {
				files: ['src/less/**/*.*', 'src/js/**/*.js'],
				tasks: ['less', 'postcss', 'uglify'],
				options: {
					nospawn: true
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-postcss');

	grunt.registerTask('default', ['less', 'postcss', 'uglify', 'watch']);
	grunt.registerTask('production', ['less', 'postcss', 'uglify']);
};
