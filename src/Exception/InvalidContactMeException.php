<?php

/*
 * This file is part of the fw4/skarabee-weblink library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skarabee\Weblink\Exception;

class InvalidContactMeException extends Exception
{
    /** @var array<int, array<string, mixed>> */
    protected $contacts;

    /**
     * @param array<int, array<string, mixed>> $contacts Array of contact errors
     */
    public function __construct(array $contacts)
    {
        $this->contacts = $contacts;

        $errors = array_reduce($contacts, function ($carry, $item) {
            return array_merge($carry, $item['errors'] ?? []);
        }, []);

        $message = 'InsertContactMes resulted in ' . count($errors) . ' issues with ' . count($contacts) . ' contact' . (count($contacts) === 1 ? '' : 's');

        parent::__construct($message . ': ' . implode('. ', array_map(function ($a) {
            return rtrim($a, '.');
        }, $errors)) . '.');
    }

    /** @return array<int, array<string, mixed>> */
    public function getContacts(): array
    {
        return $this->contacts;
    }
}
