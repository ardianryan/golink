<?php
declare(strict_types = 1);

namespace Helpers\Qr;

use BaconQrCode\Encoder\ByteMatrix;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\Path\Path;
use BaconQrCode\Renderer\Module\ModuleInterface;
/**
 * Renders individual modules as distorted squares with spaces.
 */
final class DistortedSquareModule implements ModuleInterface
{
    /**
     * @var float
     */
    private $size;

    public function __construct()
    {
        $this->size = 0.85;
    }

    public function createPath(ByteMatrix $matrix) : Path
    {
        $width = $matrix->getWidth();
        $height = $matrix->getHeight();
        $path = new Path();
        $margin = (1 - $this->size) / 2;

        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                if (! $matrix->get($x, $y)) {
                    continue;
                }

                $pathX = $x + $margin;
                $pathY = $y + $margin;
                $randX = rand(-1, 1) * rand(1, 10)*0.015;
                $randY = rand(-1, 1) * rand(1, 10)*0.02;
                $path = $path
                    ->move($pathX, $pathY)
                    ->line($pathX + $this->size, $pathY+$randY)
                    ->line($pathX+$randX + $this->size, $pathY + $this->size)
                    ->line($pathX, $pathY + $this->size)
                    ->line($pathX+$randX, $pathY)
                    ->close()
                ;
                
            }
        }

        return $path;
    }
}