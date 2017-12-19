<?php

namespace Civil;

/**
 * ElevationInterface is used to have a separate builder for single purpose.
 *
 * @author Daison Carino <daison12006013@gmail.com>
 */
interface ElevationInterface
{
    /**
     * Handle the builder.
     *
     * @param  mixed $builder
     * @return void
     */
    public function handle($builder);
}
