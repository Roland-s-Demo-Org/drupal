<?php

namespace Drupal\router_test;

use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\ParamConverter\ParamNotConvertedException;
use Drupal\user\UserInterface;
use Drupal\Core\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Psr7\Response as Psr7Response;

/**
 * Controller routines for testing the routing system.
 */
class TestControllers {

  const LONG_TEXT = 'This is text long enough to trigger Apache mod_deflate to add a `vary: accept-encoding` header to the response.';

  public function test() {
    return new Response('test');
  }

  public function test1() {
    return new Response(self::LONG_TEXT);
  }

  public function test2() {
    return ['#markup' => "test2"];
  }

  public function test3($value) {
    return ['#markup' => $value];
  }

  public function test4($value) {
    return ['#markup' => $value];
  }

  public function test5() {
    return ['#markup' => "test5"];
  }

  public function test6() {
    return new Response('test6');
  }

  public function test7() {
    return new Response('test7text');
  }

  public function test8() {
    return new Response('test8');
  }

  public function test9($uid) {
    $text = 'Route not matched.';
    try {
      $match = \Drupal::service('router.no_access_checks')->match('/user/' . $uid);
      if (isset($match['user']) && $match['user'] instanceof UserInterface) {
        $text = sprintf('User route "%s" was matched.', $match[RouteObjectInterface::ROUTE_NAME]);
      }
    }
    catch (ParamNotConvertedException) {
    }
    return new Response($text);
  }

  /**
   * Tests controller for ExceptionHandlingTest::testBacktraceEscaping().
   *
   * Passes unsafe HTML as an argument to a method which throws an exception.
   * This can be used to test if the generated backtrace is properly escaped.
   */
  public function test10() {
    $this->removeExceptionLogger();
    $this->throwException('<script>alert(\'xss\')</script>');
  }

  public function test18() {
    return [
      '#cache' => [
        'contexts' => ['url'],
        'tags' => ['foo'],
        'max-age' => 60,
      ],
      'content' => [
        '#markup' => 'test18',
      ],
    ];
  }

  public function test21() {
    return new CacheableResponse('test21');
  }

  public function test23() {
    return new Psr7Response(200, [], 'test23');
  }

  public function test24() {
    $this->removeExceptionLogger();
    throw new \Exception('Escaped content: <p> <br> <h3>');
  }

  public function test25() {
    return [
      '#cache' => [
        'url',
      ],
      '#markup' => \Drupal::requestStack()->getCurrentRequest()->getUri(),
    ];
  }

  public function testRouteName(Request $request) {
    return [
      '#markup' => $request->attributes->get(RouteObjectInterface::ROUTE_NAME),
    ];
  }

  /**
   * Rejects requests with query keys.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The given request.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response.
   */
  public function rejectsQueryStrings(Request $request) {
    return new Response('', $request->query->keys() ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK);
  }

  /**
   * Throws an exception.
   *
   * @param string $message
   *   The message to use in the exception.
   *
   * @throws \Exception
   *   Always thrown.
   */
  protected function throwException($message) {
    throw new \Exception($message);
  }

  protected function removeExceptionLogger() {
    // Remove the exception logger from the event dispatcher. We are going to
    // throw an exception to check if it is properly escaped when rendered as a
    // backtrace. The exception logger does a call to error_log() which is not
    // handled by the test error handler and would cause a test failure.
    $event_dispatcher = \Drupal::service('event_dispatcher');
    $exception_logger = \Drupal::service('exception.logger');
    $event_dispatcher->removeSubscriber($exception_logger);
  }

}
