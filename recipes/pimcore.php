<?php
/**
 * w-vision
 *
 * LICENSE
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that is distributed with this source code.
 *
 * @copyright  Copyright (c) 2018 w-vision AG (https://www.w-vision.ch)
 */

namespace Deployer;

task('deploy:pimcore:install-classes', function () {
    run('{{bin/php}} {{bin/console}} pimcore:deployment:classes-rebuild -c');
});

task('deploy:pimcore:rebuild-classes', function () {
    run('{{bin/php}} {{bin/console}} pimcore:deployment:classes-rebuild -c -d -n');
});

task('deploy:pimcore:custom-layouts-rebuild', function () {
    run('{{bin/php}} {{bin/console}} pimcore:deployment:custom-layouts-rebuild -c -d -n');
});

task('deploy:pimcore:migrate:core', function() {
    run('{{bin/php}} {{bin/console}} pimcore:migrations:migrate -s pimcore_core -n');
});

task('deploy:pimcore:migrate', function () {
    run('{{bin/php}} {{bin/console}} pimcore:migrations:migrate --allow-no-migration -n');
});

task('deploy:pimcore:merge:shared', function () {
    if (!has('pimcore_shared_configurations')) {
        return;
    }

    $sharedFiles = get('shared_files');
    $pimcoreSharedConfigFiles = get('pimcore_shared_configurations');

    $all = array_merge($sharedFiles, $pimcoreSharedConfigFiles);
    set('shared_files', $all);
});
before('deploy:shared', 'deploy:pimcore:merge:shared');

// Process the pimcore config files
// Add empty array if file is empty
task('deploy:pimcore:shared:config', function () {
    if (!has('pimcore_shared_configurations')) {
        return;
    }

    $sharedPath = "{{deploy_path}}/shared";
    $emptyContent = "<?php return [];";

    foreach (get('pimcore_shared_configurations') as $configFile) {
        run("[ -s '$sharedPath/$configFile' ] || echo '$emptyContent' >> '$sharedPath/$configFile'");
    }
});
after('deploy:shared', 'deploy:pimcore:shared:config');
