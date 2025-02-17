<?php

declare(strict_types=1);

namespace Drupal\Tests\serialization\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Functional tests for serialization system.
 *
 * @group serialization
 */
class SerializationTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['serialization', 'serialization_test'];

  /**
   * The serializer service to test.
   *
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->serializer = $this->container->get('serializer');
  }

  /**
   * Confirms that modules can register normalizers and encoders.
   */
  public function testSerializerComponentRegistration(): void {
    $object = new \stdClass();
    $format = 'serialization_test';
    $expected = 'Normalized by SerializationTestNormalizer, Encoded by SerializationTestEncoder';

    // Ensure the serialization invokes the expected normalizer and encoder.
    $this->assertSame($expected, $this->serializer->serialize($object, $format));

    // Ensure the serialization fails for an unsupported format.
    try {
      $this->serializer->serialize($object, 'unsupported_format');
      $this->fail('The serializer was expected to throw an exception for an unsupported format, but did not.');
    }
    catch (UnexpectedValueException) {
      // Expected exception; just continue testing.
    }
  }

}
