<?php

declare(strict_types=1);

namespace OliverKlee\Oelib\Session;

/**
 * This class represents a fake session that doesn't use any real sessions,
 * thus not sending any HTTP headers.
 *
 * It is intended for testing purposes.
 *
 * @deprecated will be removed in oelib 6.0
 */
class FakeSession extends Session
{
    /**
     * @var array<string, mixed> the data for this session
     */
    private $sessionData = [];

    /**
     * The constructor.
     *
     * This constructor is public to allow direct instantiation of this class
     * for the unit tests, also bypassing the check for a front end.
     *
     * @param int $type
     *
     * @phpstan-ignore-next-line The parameter is unused on purpose.
     */
    public function __construct(int $type = 0)
    {
    }

    /**
     * Gets the value of the data item for the key $key.
     *
     * @param string $key the key of the data item to get, must not be empty
     *
     * @return mixed the data for the key $key, will be an empty string if the key has not been set yet
     */
    protected function get(string $key)
    {
        return $this->sessionData[$key] ?? '';
    }

    /**
     * Sets the value of the data item for the key $key.
     *
     * @param string $key the key of the data item to get, must not be empty
     * @param mixed $value the data for the key $key
     */
    protected function set(string $key, $value): void
    {
        $this->sessionData[$key] = $value;
    }
}
