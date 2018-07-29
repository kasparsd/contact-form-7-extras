/* eslint-env es6, node */
/* eslint camelcase: warn, comma-dangle: off */

const ignoreParse = require( 'parse-gitignore' );
const exec = require( 'child_process' ).exec;

const deployConfig = {
	plugin_slug: 'contact-form-7-extras',
	svn_user: 'kasparsd',
	build_dir: '<%= dist_dir %>',
	plugin_main_file: 'plugin.php',
	assets_dir: 'assets/dotorg',
};

module.exports = function( grunt ) {

	// Load all Grunt plugins.
	require( 'load-grunt-tasks' )( grunt );

	const pluginVersion = grunt.file.read( 'plugin.php' ).match( /Version:\s*(.+)$/mi )[1];

	// Get a list of all the files and directories to exclude from the distribution.
	const releaseFiles = ignoreParse( '.distignore', {
		invert: true,
	} );

	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		dist_dir: 'dist',

		clean: {
			build: [ '<%= dist_dir %>' ],
		},

		replace: {
			readme: {
				src: 'readme.txt.md',
				dest: '<%= dist_dir %>/readme.txt',
				replacements: [
					{
						from: /^#\s(.+)$/,
						to: '=== $1 ==='
					},
					{
						from: /^##\s(.+)$/,
						to: '== $1 =='
					},
					{
						from: /^#{3,}\s(.+)$/,
						to: '= $1 ='
					},
				]
			},
			version: {
				src: [
					'<%= dist_dir %>/readme.txt',
					'package.json',
					'composer.json',
				],
				overwrite: true,
				replacements: [
					{
						from: /"version":\s*"(.+)"/,
						to: `"version": "${pluginVersion}"`
					},
					{
						from: 'STABLETAG',
						to: pluginVersion
					},
				]
			}
		},

		copy: {
			dist: {
				src: [ '**' ].concat( releaseFiles ),
				dest: '<%= dist_dir %>',
				expand: true,
			}
		},

		wp_deploy: {
			deploy: {
				options: deployConfig,
			},
			trunk: {
				options: Object.assign( deployConfig, {
					deploy_tag: false,
				} )
			}
		},
	} );

	grunt.registerTask( 'check-diff', function() {
		const done = this.async(); // This won't work with the ES6 fat arrow syntax.

		exec( 'git diff HEAD --quiet', ( err ) => {
			if ( err ) {
				grunt.log.error( 'Found uncommited changes in your current working directory.' );
				done( false );
			}

			done();
		} );
	} );

	grunt.registerTask(
		'build', [
			'clean',
			'copy',
			'replace',
		]
	);

	grunt.registerTask(
		'deploy', [
			'build',
			'check-diff',
			'wp_deploy',
		]
	);

	grunt.registerTask(
		'deploy:trunk', [
			'build',
			'check-diff',
			'wp_deploy:trunk',
		]
	);

};
