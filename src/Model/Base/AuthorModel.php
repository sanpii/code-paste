<?php

namespace Model\Base;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Model\Base\AutoStructure\Author as AuthorStructure;
use Model\Base\Author;

/**
 * AuthorModel
 *
 * Model class for table author.
 *
 * @see Model
 */
class AuthorModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->structure = new AuthorStructure;
        $this->flexible_entity_class = "\Model\Base\Author";
    }
}
