<?php

namespace EnumBundle\Tests\Validator\Constraints;

use EnumBundle\Tests\TypeEnum;
use EnumBundle\Validator\Constraints\Enum;
use EnumBundle\Validator\Constraints\EnumValidator;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class EnumValidatorTest extends AbstractConstraintValidatorTest
{

    protected function createValidator()
    {
        $registry = $this->prophesize('EnumBundle\Registry\EnumRegistryInterface');
        $registry->has('state')->willReturn(false);
        $registry->has('type')->willReturn(true);
        $registry->get('type')->willReturn(new TypeEnum);

        return new EnumValidator($registry->reveal());
    }

    public function testAcceptOnlyEnum()
    {
        $this->setExpectedException('Symfony\Component\Validator\Exception\UnexpectedTypeException');
        $this->validator->validate(null, new Choice);
    }

    public function testEnumIsRequired()
    {
        $this->setExpectedException('Symfony\Component\Validator\Exception\ConstraintDefinitionException');
        $this->validator->validate('foo', new Enum);
    }

    public function testValidEnumIsRequired()
    {
        $this->setExpectedException('Symfony\Component\Validator\Exception\ConstraintDefinitionException');
        $this->validator->validate('foo', new Enum('state'));
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new Enum('type'));

        $this->assertNoViolation();
    }

    public function testValidSingleEnum()
    {
        $this->validator->validate('customer', new Enum('type'));

        $this->assertNoViolation();
    }

    public function testInvalidSingleEnum()
    {
        $constraint = new Enum(['enum' => 'type', 'message' => 'myMessage']);

        $this->validator->validate('foo', $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"foo"')
            ->setCode(Choice::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
    }

    public function testValidMultipleEnum()
    {
        $constraint = new Enum(['enum' => 'type', 'multiple' => true]);

        $this->validator->validate(['customer', 'prospect'], $constraint);

        $this->assertNoViolation();
    }

    public function testInvalidMultipleEnum()
    {
        $constraint = new Enum(['enum' => 'type', 'multiple' => true, 'multipleMessage' => 'myMessage']);

        $this->validator->validate(['customer', 'foo'], $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"foo"')
            ->setInvalidValue('foo')
            ->setCode(Choice::NO_SUCH_CHOICE_ERROR)
            ->assertRaised();
    }

}
