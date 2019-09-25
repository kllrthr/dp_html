<?php

namespace Drupal\dp_html\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Content' block.
 *
 * @Block(
 *   id = "dp_html_content",
 *   admin_label = @Translation("Html content"),
 *   category = @Translation("Custom EVRY blocks")
 * )
 */
class HtmlContentBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * File storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $fileStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityStorageInterface $file_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->fileStorage = $file_storage;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('file')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'title' => '',
      'image' => [],
      'text' => [],
      'url' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#default_value' => $this->configuration['image'],
      '#upload_location' => 'public://',
      '#upload_validators'  => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
      ],
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->configuration['title'],
      '#attributes' => [
        'placeholder' => $this->t('Example title'),
      ],
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL'),
      '#default_value' => $this->configuration['url'],
      '#attributes' => [
        'placeholder' => $this->t('http://...'),
      ],
    ];

    $form['text'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Content'),
      '#default_value' => !empty($this->configuration['text']['value']) ? $this->configuration['text']['value'] : '',
      '#format' => !empty($this->configuration['text']['format']) ? $this->configuration['text']['format'] : filter_default_format(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $image_value = $form_state->getValue('image');

    if (isset($image_value[0])) {
      /** @var \Drupal\file\FileInterface $file */
      $file = $this->fileStorage->load($image_value[0]);
      $file->setPermanent();
      $file->save();
      $file_usage = \Drupal::service('file.usage');
      $file_usage->add($file, 'dp_html', 'block', 'HtmlContentBlock');
    }
    $this->configuration['image'] = $image_value;
    $this->configuration['url'] = $form_state->getValue('url');
    $this->configuration['title'] = $form_state->getValue('title');
    $this->configuration['text'] = $form_state->getValue('text');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['#cache'] = [
      'max-age' => -1,
    ];
    $image = $this->configuration['image'];
    $img_render = [];
    // Load the image.
    /** @var \Drupal\file\FileInterface $image */
    if (isset($image[0]) && $image = $this->fileStorage->load($image[0])) {
      $img_render = [
        '#theme' => 'image',
        '#alt' => '',
        '#title' => '',
        '#uri' => $image->getFileUri(),
      ];
    }
    $build['content'] = [
      '#theme' => 'dp_html_html_content_block',
      '#title' => $this->configuration['title'],
      '#image' => $img_render,
      '#text' => [
        '#type' => 'processed_text',
        '#text' => $this->configuration['text']['value'],
        '#format' => $this->configuration['text']['format'],
      ],
      '#url' => $this->configuration['url'],
    ];
    return $build;
  }

}
