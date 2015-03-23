<?php  namespace Dencker\Watchtower\Contracts; 


interface WatchtowerActorContract {

    public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false);
}