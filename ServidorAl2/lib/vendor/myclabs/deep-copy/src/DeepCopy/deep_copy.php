<?php

namespace DeepCopy;

/**
 * Deep copies the given value.
 *
 * @param mixed $value
 * @param bool  $useCloneMethod
 *
 * @return mixed
 */
function deep_copy($value, $useCloneMethod = false)
{
	$c = new DeepCopy;
	//$this->autos = $c->get_all_busca($busca);
    return $c->copy($value);
}
