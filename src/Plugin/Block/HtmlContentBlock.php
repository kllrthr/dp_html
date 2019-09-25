<?php

namespace Drupal\dp_html\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dp_html\Controller\HtmlPageController;

/**
 * Provides a 'Content' block.
 *
 * @Block(
 *   id = "dp_html_content_block",
 *   admin_label = @Translation("Html content"),
 *   category = @Translation("Custom EVRY blocks")
 * )
 */
class HtmlContentBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
      'url' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL to html file'),
      '#default_value' => $this->configuration['url'],
      '#attributes' => [
        'placeholder' => $this->t('http://...'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['url'] = $form_state->getValue('url');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
//    $build['#cache'] = [
//      'max-age' => -1,
//    ];
    $html = 'yoyo';
    if ($this->configuration['url']) {
      $html_controller = new HtmlPageController();
      $html = $html_controller->content($this->configuration['url']);
    }

    $build['content'] = $html;
    return $build;
  }

}
