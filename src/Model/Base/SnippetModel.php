<?php

namespace Model\Base;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Model\Base\AutoStructure\Snippet as SnippetStructure;
use Model\Base\Snippet;

/**
 * SnippetModel
 *
 * Model class for table snippet.
 *
 * @see Model
 */
class SnippetModel extends Model
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
        $this->structure = new SnippetStructure;
        $this->flexible_entity_class = "\Model\Base\Snippet";
    }
}
