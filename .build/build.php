<?php
/**
 * Build script and local test environment
 */

$action     = $argv[1] ?? 'build';
$build_dir  = __DIR__;
$is_windows = 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) );

/**
 * Function to open a URL in the default browser (Cross-Platform)
 */
function open_browser( string $url ) {
	$os = strtoupper( PHP_OS );
	
	if ( 'WIN' === substr( $os, 0, 3 ) ) {
		pclose( popen( "start \"\" \"{$url}\"", "r" ) );
	} elseif ( 'DARWIN' === $os ) {
		exec( "open \"{$url}\" > /dev/null 2>&1 &" );
	} else {
		exec( "xdg-open \"{$url}\" > /dev/null 2>&1 &" );
	}
}

if ( 'startplugin' === $action ) {
	echo "1. Generating plugin test package...\n";
	
	if ( $is_windows ) {
		system( "powershell -ExecutionPolicy Bypass -File \"{$build_dir}\\build.ps1\"" );
	} else {
		system( "bash \"{$build_dir}/build.sh\"" );
	}
	
	$compose_file = $build_dir . DIRECTORY_SEPARATOR . 'docker-compose.yml';
	
	if ( ! file_exists( $compose_file ) ) {
		echo "\nERROR: The docker-compose.yml file was not found in: {$compose_file}\n";
		exit( 1 );
	}
	
	$cleanup = function () use ( $compose_file, $build_dir ) {
		$seed_file = $build_dir . DIRECTORY_SEPARATOR . 'seed_users.php';
		if ( file_exists( $seed_file ) ) {
			@unlink( $seed_file );
		}
		echo "\n\n5. Removing containers and the temporary test database...\n";
		system( "docker compose -f \"{$compose_file}\" down -v" );
		echo "Environment successfully cleaned!\n";
		exit( 0 );
	};
	
	if ( function_exists( 'sapi_windows_set_ctrl_handler' ) ) {
		sapi_windows_set_ctrl_handler( $cleanup );
	} elseif ( function_exists( 'pcntl_async_signals' ) ) {
		pcntl_async_signals( true );
		pcntl_signal( SIGINT, $cleanup );
		pcntl_signal( SIGTERM, $cleanup );
	}
	
	register_shutdown_function( function () use ( $compose_file, $build_dir ) {
		$seed_file = $build_dir . DIRECTORY_SEPARATOR . 'seed_users.php';
		if ( file_exists( $seed_file ) ) {
			@unlink( $seed_file );
		}
		system( "docker compose -f \"{$compose_file}\" down -v" );
	} );
	
	echo "\n2. Starting Docker containers...\n";
	system( "docker compose -f \"{$compose_file}\" up -d" );
	
	echo "\n3. Waiting for the database to start...\n";
	sleep( 12 );
	
	echo "\n4. Automatically installing WordPress (Login: root | Password: root)...\n";
	
	$wp_cli_cmd = 'docker exec rolecraft_wp_test bash -c "'
		. 'curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && '
		. 'chmod +x wp-cli.phar && '
		. './wp-cli.phar core install --allow-root '
		. '--url=\'http://localhost:8181\' '
		. '--title=\'RoleCraft Test Environment\' '
		. '--admin_user=\'root\' '
		. '--admin_password=\'root\' '
		. '--admin_email=\'admin@example.com\' && '
		. './wp-cli.phar plugin activate wpss-ultimate-user-management --allow-root && '
		. './wp-cli.phar plugin install plugin-check --activate --allow-root"';
	
	system( $wp_cli_cmd );
	
	echo "\n4.1. Creating 100 test users (20 subscribers, 20 editors, 20 contributors, 40 authors)...\n";
	
	// Creates the seed script locally to avoid cross-platform shell escaping issues
	$seed_file = $build_dir . DIRECTORY_SEPARATOR . 'seed_users.php';
	$seed_code = '<?php
    $roles_map = [
        "subscriber"  => 20,
        "editor"      => 20,
        "contributor" => 20,
        "author"      => 40
    ];

    $first_names = ["Aragorn","Gandalf","Legolas","Frodo","Samwise","Gimli","Boromir","Arwen","Galadriel","Elrond","Bilbo","Thorin","Balin","Dwalin","Fili","Kili","Bofur","Bombur","Dori","Nori","Oin","Gloin","Bifur","Radagast","Saruman","Sauron","Denethor","Faramir","Eomer","Eowyn","Theoden","Grimma","Hador","Hurin","Tuor","Turgon","Fingolfin","Fanor","Melian","Beren"];
    $last_names  = ["Strider","Stormcrow","Greenleaf","Baggins","Gamgee","Oakenshield","Ironfoot","Silverhair","Lightbringer","Stonehelm","Swiftrunner","Shadowfax","Nightshade","Brightwood","Riverrun","Windrider","Stormborn","Ironhand","Goldensoul","Starkiller"];

    $i = 1;
    foreach ($roles_map as $role => $count) {
        for ($c = 0; $c < $count; $c++) {
            $username = "user_" . $role . "_" . $i;
            $email    = "user{$i}@rolecraft.test";
            $password = "root";
            
            if (!username_exists($username)) {
                $user_id = wp_create_user($username, $password, $email);
                if (!is_wp_error($user_id)) {
                    $user = new WP_User($user_id);
                    $user->set_role($role);
                    
                    $fname = $first_names[array_rand($first_names)];
                    $lname = $last_names[array_rand($last_names)];
                    wp_update_user([
                        "ID"          => $user_id,
                        "first_name"  => $fname,
                        "last_name"   => $lname,
                        "display_name"=> "{$fname} {$lname}"
                    ]);
                }
            }
            $i++;
        }
    }
    echo "100 users successfully created!\n";
    ';
	file_put_contents( $seed_file, $seed_code );
	
	// Copies the script to the container and executes it via WP-CLI
	system( "docker cp \"{$seed_file}\" rolecraft_wp_test:/var/www/html/seed_users.php" );
	system( "docker exec rolecraft_wp_test ./wp-cli.phar eval-file seed_users.php --allow-root" );
	@unlink( $seed_file ); // Removes the local temporary file
	
	$admin_url = 'http://localhost:8181/wp-admin';
	
	echo "\n------------------------------------------------------------\n";
	echo "All set! Environment configured with 100 users.\n";
	echo "URL: {$admin_url}\n";
	echo "Administrator User: root\n";
	echo "Password            root\n";
	echo "Other users:       pass 'root' (eg.: user_subscriber_1)\n";
	echo "------------------------------------------------------------\n";
	
	echo "Starting {$admin_url} in your browser\n";
	open_browser( $admin_url );
	
	echo "\nPress Ctrl+C in this terminal at any time to stop and delete the containers.\n\n";
	
	system( "docker compose -f \"{$compose_file}\" logs -f" );
	
	$cleanup();
}

// Default build execution
if ( $is_windows ) {
	system( "powershell -ExecutionPolicy Bypass -File \"{$build_dir}\\build.ps1\"" );
} else {
	system( "bash \"{$build_dir}/build.sh\"" );
}
