/* jshint node:true */
module.exports = function (grunt) {

	'use strict';

	// Project configuration.
	grunt.initConfig({

		pkg: grunt.file.readJSON( 'package.json' ),

		// Check textdomain errors.
		checktextdomain: {
			options:{
				text_domain: '<%= pkg.name %>',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php', // Include all files
					'!node_modules/**',
					'!dist/**',
					'!orig/**'
				],
				expand: true
			}
		},

		// Compile all .scss files.
		sass: {
			dist: {
				options: {
					sourceMap: false
				},
				files: [{
					'assets/css/frontend.css': 'assets/sass/frontend.scss'
				}]
			}
		},

		cmq: {
			options: {
				compress: false,
				logFile: false
			},
			media: {
				files: {
					'assets/css/frontend.css': ['assets/css/frontend.css']
				}
			}
		},

		postcss: {
			options: {
				processors: [
					require( 'autoprefixer' )({
						browsers: [
							'> 1%',
							'last 2 versions',
							'Firefox ESR',
							'Opera 12.1',
							'ie 9'
						]
					})
				]
			},
			frontend: {
				src: 'assets/css/frontend.css',
				dest: 'assets/css/frontend.css'
			}
		},

	    wpcss: {
	        style: {
	            options: {
	                commentSpacing: true
	            },
	            files: {
	            	'assets/css/frontend.css': ['assets/css/frontend.css']
	            }
	        }
	    },

		// RTLCSS
		rtlcss: {
			main: {
				options: {},
				expand: true,
				ext: '-rtl.css',
				src: [
					'assets/css/frontend.css'
				]
			}
		},

		cssmin: {
			target: {
				files: [{
					expand: true,
					cwd: './',
					src: [
						'assets/css/*.css',
						'!assets/css/*.min.css'],
					dest: './',
					ext: '.min.css'
				}]
			}
		},

		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'assets/js/*.js',
				'!assets/js/*.min.js'
			]
		},

		uglify: {
			options: {
				preserveComments: 'some'
			},
			js: {
				files: [{
					expand: true,
					cwd: 'assets/js/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: 'assets/js/',
					ext: '.min.js'
				}]
			}
		},

		watch: {
			css: {
				files: [
					'assets/sass/*.scss',
					'assets/sass/*/*.scss',
					'assets/sass/*/*/*.scss',
					'assets/sass/*/*/*/*.scss'
				],
				tasks: [
					'sass',
					'cssmin'
				]
			},
			js: {
				files: [
					'assets/js/admin.js'
				],
				tasks: [
					'uglify'
				]
			}
		},

		// Replace text
		replace: {
			Version: {
				src: [
					'<%= pkg.name %>.php'
				],
				overwrite: true,
				replacements: [ {
					from: /^.*Version:.*$/m,
					to: ' * Version: <%= pkg.version %>'
				} ]
			},
			readme: {
				src: [
					'readme.txt'
				],
				overwrite: true,
				replacements: [ {
					from: /^.*Stable tag:.*$/m,
					to: 'Stable tag: <%= pkg.version %>'
				} ]
			}
		},

		wp_readme_to_markdown: {
			github: {
				files: {
					'README.md': 'readme.txt'
				}
			}
		}

	});

	grunt.loadNpmTasks( 'grunt-postcss' );
    grunt.loadNpmTasks( 'grunt-checktextdomain' );
    grunt.loadNpmTasks( 'grunt-combine-media-queries' );
    grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
    grunt.loadNpmTasks( 'grunt-contrib-jshint' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'grunt-sass' );
    grunt.loadNpmTasks( 'grunt-rtlcss' );
    grunt.loadNpmTasks( 'grunt-text-replace' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-wp-css' );
    grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );

	grunt.registerTask( 'css', [
		'sass',
		'cmq',
		'postcss',
		'wpcss',
		'rtlcss',
		'cssmin'
	]);

	grunt.registerTask( 'js', [
		'jshint',
		'uglify'
	]);

	grunt.registerTask( 'prepare', [
		'checktextdomain',
		'js',
		'replace',
		'wp_readme_to_markdown',
		'css'
	]);

};
