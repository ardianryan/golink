<?php
declare(strict_types = 1);

namespace Helpers\Qr;

use BaconQrCode\Renderer\Path\Path;
use BaconQrCode\Renderer\Eye\EyeInterface;

final class EyeInverted implements EyeInterface
{
    /**
     * @var self|null
     */
    private static $instance;

    private static $i = 0;

    private function __construct()
    {
    }

    public static function instance() : self
    {
        return self::$instance ?: self::$instance = new self();
    }

    public function getExternalPath() : Path
    {

        return (new Path())
            ->move(-3.5, -3.5)
            ->curve(3.5, -3.5, 3.5, -3.5, 3.5, 3.5)
            ->move(3.5, 3.5)
            ->curve(-3.5, 3.5, -3.5, 3.5, -3.5, -3.5)
            ->move(-2.5, -2.5)
            ->curve(2.5, -2.5, 2.5, -2.5, 2.5, 2.5)
            ->move(2.5, 2.5)
            ->curve(-2.5, 2.5, -2.5, 2.5, -2.5, -2.5)
        ;            
    }

    public function getInternalPath() : Path
    {
        return (new Path())
            ->move(-1.5, -1.5)
            ->curve(1.5, -1.5, 1.5, -1.5, 1.5, 1.5)
            ->move(1.5, 1.5)
            ->curve(-1.5, 1.5, -1.5, 1.5, -1.5, -1.5)
        ;
    }
}


