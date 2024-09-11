<?php

/*TDD*/

declare(strict_types=1);

namespace ShouldTests;

require_once(__DIR__ . "/../vendor/autoload.php");

use Pst\Testing\Should;

Should::executeTests(function() {
    Should::haveMethods (
        'PST\Testing\Should',

        "beTrue",
        "notBeTrue",
        "beFalse",
        "notBeFalse",
        "beNull",
        "notBeNull",
        "haveMethods",
        "notHaveMethods",
        "beA",
        "notBeA"
    );

    Should::beTrue(true, true);
    Should::notBeTrue(false, false);
    Should::beFalse(false, false);
    Should::notBeFalse(true, true);
    Should::beNull(null, null);
    Should::notBeNull(true, true);
    Should::beA('PST\Testing\Should','PST\Testing\Should','PST\Testing\Should');
    Should::notBeA('PST\Testing\Should', 'PST\Testing\Exceptions\ShouldException', 'PST\Testing\Exceptions\ShouldException');
});