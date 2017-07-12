<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2016-09-22
 * Time: 17:27
 */
declare(strict_types = 1);

namespace Vinnia\Upsales;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

class Validator
{

    /**
     * @var ValidatorInterface
     */
    private $backend;

    function __construct(ValidatorInterface $backend)
    {
        $this->backend = $backend;
    }

    public static function make(): self
    {
        return new self((new ValidatorBuilder)->getValidator());
    }

    protected function getProductConstraint(): Constraint
    {
        return new Collection([
            'allowExtraFields' => true,
            'fields' => [
                'id' => new Type('integer'),
            ],
        ]);
    }

    protected function getOrderRowConstraint(): Constraint
    {
        return new Collection([
            'allowExtraFields' => true,
            'fields' => [
                'product' => $this->getProductConstraint(),
                'price' => new Type('numeric'),
                'quantity' => new Type('numeric'),
            ],
        ]);
    }

    public function validateOrder(array $data): ConstraintViolationListInterface
    {
        return $this->backend->validate($data, new Collection([
            'allowExtraFields' => true,
            'fields' => [
                'description' => new Type('string'),
                'date' => new DateTime([
                    'format' => 'Y-m-d\TH:i:sP', // ISO 8601 eg 2016-09-22T17:50:00+02:00
                ]),
                'user' => new Type('integer'),
                'stage' => new Type('integer'),
                'probability' => new Type('integer'),
                'client' => new Type('integer'),
                'orderRow' => new All($this->getOrderRowConstraint()),
            ],
        ]));
    }

}
