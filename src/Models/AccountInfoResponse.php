<?php

declare(strict_types=1);

namespace RunApi\Core\Models;

use RunApi\Core\Support\Payload;

/**
 * RunAPI account profile response.
 */
readonly class AccountInfoResponse extends BaseModel
{
    /**
     * Create an account info response value object.
     *
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public AccountRecord $account,
        array $raw = [],
    ) {
        parent::__construct($raw === [] ? [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'account' => $account->toArray(),
        ] : $raw);
    }

    /**
     * Hydrate an account info response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(
            id: Payload::int($raw, 'id'),
            name: Payload::string($raw, 'name'),
            email: Payload::string($raw, 'email'),
            account: AccountRecord::fromArray(Payload::array($raw, 'account')),
            raw: $raw,
        );
    }
}
