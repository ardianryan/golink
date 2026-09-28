<?php
declare(strict_types = 1);

namespace Helpers\Qr;

use BaconQrCode\Renderer\Path\Path;
use BaconQrCode\Renderer\Eye\EyeInterface;


final class Hexagon implements EyeInterface
{

    /**
     * @var self|null
     */
    private static $instance;

    private function __construct()
    {
    }


    public static function instance() : self
    {
        return self::$instance ?: self::$instance = new self();
    }

    public function getRotation() : array
    {
        return [0, 0, 0];
    }

    public function getExternalPath() : Path
    {
        return (new Path())
            ->move(3.5, 0)
            ->line(1.75, 3)
            ->line(-1.75, 3)
            ->line(-3.5, 0)
            ->line(-1.75, -3)
            ->line(1.75, -3)
            ->close()
            ->move(2.5, 0)
            ->line(1.25, 2.2)
            ->line(-1.25, 2.2)
            ->line(-2.5, 0)
            ->line(-1.25, -2.2)
            ->line(1.25, -2.2)
            ->close();
    }

    public function getInternalPath() : Path
    {
        return (new Path())
            ->move(1.5, 0)
            ->line(0.75, 1.3)
            ->line(-0.75, 1.3)
            ->line(-1.5, 0)
            ->line(-0.75, -1.3)
            ->line(0.75, -1.3)
            ->close();
    }
}