<?php

namespace Drupal\demo_external_posts\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'demo_external_posts_field' field type.
 *
 * @FieldType(
 *   id = "demo_external_posts_field",
 *   label = @Translation("External post"),
 *   description = @Translation("Displays an external post."),
 *   category = @Translation("Custom"),
 *   default_widget = "demo_external_posts_widget",
 *   default_formatter = "demo_external_posts_formatter"
 * )
 */
class DemoExternalPostsField extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['post_id'] = DataDefinition::create('string')
      ->setLabel(t('Post ID'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'post_id' => [
          'type' => 'int',
          'size' => 'normal',
        ],
      ],
    ];
  }

}
