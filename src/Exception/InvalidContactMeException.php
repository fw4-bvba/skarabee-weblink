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
    protected $contacts;

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

    public function getContacts(): array
    {
        return $this->contacts;
    }
}
