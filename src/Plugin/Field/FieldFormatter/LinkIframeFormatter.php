<?php

namespace Drupal\link_iframe_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;

/**
 * Plugin implementation of the 'link_iframe_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "link_iframe_formatter",
 *   label = @Translation("Iframe Formatter"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkIframeFormatter extends LinkFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'responsive' => TRUE,
      'width' => '640',
      'height' => '480',
      'class' => '',
      'original' => '',
      'disable_scrolling' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements['responsive'] = array(
      '#title' => $this->t('Responsive iFrame'),
      '#type' => 'checkbox',
      '#description' => $this->t("Make the iframe fill the width of it's container"),
      '#default_value' => $this->getSetting('responsive'),
    );
    $responsive_checked_state = array(
      'visible' => array(
        ':input[name*="responsive"]' => array('checked' => FALSE),
      )
    );

    $elements['width'] = [
      '#title' => $this->t('Width'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('width'),
      '#required' => TRUE,
      '#states' => $responsive_checked_state,
    ];

    $elements['height'] = [
      '#title' => $this->t('Height'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('height'),
      '#required' => TRUE,
      '#states' => $responsive_checked_state,
    ];

    $elements['disable_scrolling'] = [
      '#title' => t('Disable Scrolling'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('disable_scrolling'),
    ];

    $elements['class'] = [
      '#title' => $this->t('Class'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('class'),
      '#required' => FALSE,
    ];
    $elements['original'] = [
      '#title' => $this->t('Show original link'),
      '#type' => 'radios',
      '#options' => [
        TRUE => $this->t('On'),
        FALSE => $this->t('Off'),
      ],
      '#default_value' => $this->getSetting('original'),
      '#required' => FALSE,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    if ($this->getSetting('responsive')) {
      $dimensions = $this->t('Responsive');
    }
    else {
      $dimensions = $this->t('Width: @width, Height: @height', array('@width' => $this->getSetting('width'), '@height' => $this->getSetting('height')));
    }
    $summary[] = $this->t( '@dimensions, Width: @width, Height: @height, Scrolling: @scrolling, Class: @class, Original link is @original', [
      '@dimensions' => $dimensions,
      '@width' => $this->getSetting('width'),
      '@height' => $this->getSetting('height'),
      '@scrolling' => $this->getSetting('disable_scrolling') ? 'no' : 'yes',
      '@class' => $this->getSetting('class') == "" ? 'None' : $this->getSetting('class'),
      '@original' => $this->getSetting('original') ? $this->t('On') : $this->t('Off')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $settings = $this->getSettings();

    foreach ($items as $delta => $item) {
      // By default use the full URL as the link text.
      $url = $this->buildUrl($item);

      $width = ($settings['responsive']) ? '100%' : $settings['width'];
      $height = ($settings['responsive']) ? 'auto' : $settings['height'];

      $element[$delta] = [
        '#theme' => 'link_iframe_formatter',
        '#url' => $url,
        '#width' => $width,
        '#height' => $height,
        '#scrolling' => $settings['disable_scrolling'] ? 'no' : 'yes',
        '#class' => $settings['class'],
        '#original' => $settings['original'],
        '#path' => $url,
      ];
    }
    return $element;
  }

  public function link_iframe_formatter_page_attachments( &$attachments) {
    $attachments['#attached']['library'][] = 'link_iframe_formatter/scripts';
    $attachments['#attached']['drupalSettings']['linkIframeFormatter']['scripts']['responsive'] = $this->getSetting('responsive');
  }
}
