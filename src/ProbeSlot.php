<?php

namespace WishgranterProject\MusicRadar;

use WishgranterProject\MusicProbe\ProbeInterface;

class ProbeSlot
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\MusicProbe\ProbeInterface
     *   A probe.
     * @param int $priority
     *   Priority of the probe.
     */
    public function __construct(
        public readonly ProbeInterface $probe,
        public readonly int $priority,
    ) {
    }
}
