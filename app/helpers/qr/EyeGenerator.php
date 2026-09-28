<?php
declare(strict_types = 1);

namespace Helpers\Qr;

use BaconQrCode\Renderer\Path\Path;
use BaconQrCode\Renderer\Eye\EyeInterface;
use BaconQrCode\Renderer\Eye\SimpleCircleEye;
use BaconQrCode\Renderer\Eye\SquareEye;

final class EyeGenerator implements EyeInterface
{
    /**
     * @var self|null
     */
    private static $instance;

    private static $frame = 'square';

    private static $eye = 'square';

    /**
     * Eye Styles
     *
     * @version 6.7
     */
    private static $eyestyles = [
        'eye' => Eye::class,
        'circle' => Circle::class,
        'diamond' => Diamond::class,
        'square' => SquareEye::class,
        'butterfly' => Butterfly::class,
        'rounded' => RoundedSquare::class,
        'eyeinverted' => EyeInverted::class,
        'bubble' => RoundedCornerSquare::class,
        'hexagon' => Hexagon::class
    ];

    /**
     * Frame Styles
     *
     * @version 6.7
     */
    private static $framestyles = [
        'eye' => Eye::class,
        'circle' => Circle::class,
        'square' => SquareEye::class,
        'rounded' => RoundedSquare::class,
        'eyeinverted' => EyeInverted::class,
        'bubble' => RoundedCornerSquare::class,
        'hexagon' => Hexagon::class
    ];

    private function __construct()
    {
    }

    public static function instance($eye = null, $frame = null) : self
    {
        self::$eye = array_key_exists($eye, self::$eyestyles) ? self::$eyestyles[$eye] : self::$eyestyles['square'];

        self::$frame = array_key_exists($frame, self::$framestyles) ? self::$framestyles[$frame] : self::$framestyles['square'];

        return self::$instance ?: self::$instance = new self();
    }

    public function getExternalPath() : Path
    {

        $fn = \call_user_func([self::$frame, 'instance']);

        return $fn->getExternalPath();
    }

    public function getInternalPath() : Path
    {
        $fn = \call_user_func([self::$eye, 'instance']);

        return $fn->getInternalPath();
    }

    public function getRotation()
    {
        if(method_exists(self::$eye, 'getRotation')){
            $fn = \call_user_func([self::$eye, 'instance']);
            return $fn->getRotation();
        }

        if(method_exists(self::$frame, 'getRotation')){
            $fn = \call_user_func([self::$frame, 'instance']);
            return $fn->getRotation();
        }
        
        return [0, 90, -90];
    }
}