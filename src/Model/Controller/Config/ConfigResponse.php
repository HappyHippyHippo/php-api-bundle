<?php

namespace Hippy\Api\Model\Controller\Config;

use Hippy\Config\Config;
use Hippy\Model\Model;

/**
 * @method Config getConfig()
 */
class ConfigResponse extends Model
{
    /**
     * @param Config $config
     */
    public function __construct(protected Config $config)
    {
        parent::__construct();
    }
}
