<?php

/**
 * Credit: Peter Petermann
 * https://www.binpress.com/building-project-skeletons-composer/
 */

// We get the project name from the name of the path that Composer created for us.
$projectname = basename(realpath("."));
echo "projectname $projectname taken from directory name" . PHP_EOL;

// We could do more replaces to our templates here,
// for the example we only do {{ projectname }}
$replaces = [
    "{{ projectname }}" => $projectname
];


// Process templates from skel/templates dir. Notice that we only use files that end
// with -dist again. This makes sense in the context of this example, but depending on your
// requirements you might want to do a more complex things here (like if you want
// to replace files somewhere
// else than in the projects root directory
foreach (glob("skel" . DIRECTORY_SEPARATOR . "templates/{,.}*-dist", GLOB_BRACE) as $distfile) {

    $target = substr($distfile, 15, -5);

    // First we copy the dist file to its new location,
    // overwriting files we might already have there.
    echo "creating clean file ($target) from dist ($distfile)..." . PHP_EOL;
    copy($distfile, $target);

    // Then we apply our replaces for within those templates.
    echo "applying variables to $target..." . PHP_EOL;
    applyValues($target, $replaces);
}
echo "removing dist files" . PHP_EOL;

// Then we drop the skel dir, as it contains skeleton stuff.
delTree("skel");

applyValues("app" . DIRECTORY_SEPARATOR . "settings.php", $replaces);

// We could also remove the composer.phar that the zend skeleton has here,
// but a much better choice is to remove that one from our fork directly.

echo "dist script done..." . PHP_EOL;


/**
 * A method that will read a file, run a strtr to replace placeholders with
 * values from our replace array and write it back to the file.
 *
 * @param string $target the filename of the target
 * @param array $replaces the replaces to be applied to this target
 */
function applyValues($target, $replaces)
{
    file_put_contents(
        $target,
        strtr(
            file_get_contents($target),
            $replaces
        )
    );
}


/**
 * A simple recursive delTree method
 *
 * @param string $dir
 * @return bool
 */
function delTree($dir)
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

exit(0);
