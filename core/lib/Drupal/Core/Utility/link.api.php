<?php

/**
 * @file
 * Hooks and documentation related to links.
 */

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the parameters for links.
 *
 * @param array $variables
 *   An associative array of variables defining a link. The link may be either a
 *   "route link" using \Drupal\Core\Utility\LinkGenerator::link(), which is
 *   exposed as the 'link_generator' service or a link generated by
 *   \Drupal\Core\Utility\LinkGeneratorInterface::generate(). If the link is a
 *   "route link", 'route_name' will be set; otherwise, 'path' will be set.
 *   The following keys can be altered:
 *   - text: The link text for the anchor tag. If the hook implementation
 *     changes this text it needs to preserve the safeness of the original text.
 *     Using t() or \Drupal\Component\Render\FormattableMarkup with
 *     @placeholder is recommended as this will escape the original text if
 *     necessary. If the resulting text is not marked safe it will be escaped.
 *   - url_is_active: Whether or not the link points to the currently active
 *     URL.
 *   - url: The \Drupal\Core\Url object.
 *   - options: An associative array of additional options that will be passed
 *     to either \Drupal\Core\Utility\UnroutedUrlAssembler::assemble() or
 *     \Drupal\Core\Routing\UrlGenerator::generateFromRoute() to generate the
 *     href attribute for this link, and also used when generating the link.
 *     Defaults to an empty array. It may contain the following elements:
 *     - 'query': An array of query key/value-pairs (without any URL-encoding) to
 *       append to the URL.
 *     - absolute: Whether to force the output to be an absolute link (beginning
 *       with http:). Useful for links that will be displayed outside the site,
 *       such as in an RSS feed. Defaults to FALSE.
 *     - language: An optional language object. May affect the rendering of
 *       the anchor tag, such as by adding a language prefix to the path.
 *     - attributes: An associative array of HTML attributes to apply to the
 *       anchor tag. If element 'class' is included, it must be an array; 'title'
 *       must be a string; other elements are more flexible, as they just need
 *       to work as an argument for the constructor of the class
 *       Drupal\Core\Template\Attribute($options['attributes']).
 *
 * @see \Drupal\Core\Utility\UnroutedUrlAssembler::assemble()
 * @see \Drupal\Core\Routing\UrlGenerator::generateFromRoute()
 */
function hook_link_alter(&$variables) {
  // Add a warning to the end of route links to the admin section.
  if (isset($variables['route_name']) && strpos($variables['route_name'], 'admin') !== FALSE) {
    $variables['text'] = new TranslatableMarkup('@text (Warning!)', ['@text' => $variables['text']]);
  }
}

/**
 * @} End of "addtogroup hooks".
 */
