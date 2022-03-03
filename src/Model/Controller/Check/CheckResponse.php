<?php

namespace Hippy\Api\Model\Controller\Check;

use Hippy\Model\Model;

class CheckResponse extends Model
{
    /** @var array<string, array<int|string, mixed>> */
    protected array $checks = [];

    /**
     * @param string $name
     * @param bool $success
     * @param string $message
     * @param array<int|string, mixed> $extra
     * @return $this
     */
    public function addCheck(
        string $name,
        bool $success,
        string $message,
        array $extra = []
    ): CheckResponse {
        $this->checks[$name] = array_merge(['success' => $success, 'message' => $message], $extra);

        return $this;
    }
}
