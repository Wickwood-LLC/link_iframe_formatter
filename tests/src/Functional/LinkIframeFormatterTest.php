<?php

namespace Drupal\Tests\link_iframe_formatter\Functional;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;

/**
 * Defines a class for testing link iframe formatter.
 *
 * @group link_iframe_formatter
 */
class LinkIframeFormatterTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'link',
    'link_iframe_formatter',
    'entity_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    if (!FieldStorageConfig::load('entity_test.link')) {
      $storage = FieldStorageConfig::create([
        'entity_type' => 'entity_test',
        'field_name' => 'link',
        'id' => 'entity_test.link',
        'type' => 'link',
      ]);
      $storage->save();
    }

    if (!FieldConfig::load('entity_test.entity_test.link')) {
      $config = FieldConfig::create([
        'field_name' => 'link',
        'entity_type' => 'entity_test',
        'bundle' => 'entity_test',
        'id' => 'entity_test.entity_test.link',
        'label' => 'Link',
      ]);
      $config->save();
    }
    $display = \Drupal::service('entity_display.repository')->getViewDisplay('entity_test', 'entity_test');
    assert($display instanceof EntityViewDisplayInterface);
    $display->setComponent('link', [
      'type' => 'link_iframe_formatter',
      'settings' => [
        'width' => 350,
        'height' => 270,
        'class' => 'some-class',
        'original' => TRUE,
      ],
    ]);
    $display->save();
  }

  /**
   * Tests formatter.
   */
  public function testFormatter(): void {
    $this->drupalLogin($this->createUser(['access content', 'view test entity']));
    $url = sprintf('https://%s.com/index.html', $this->randomMachineName());
    $entity = EntityTest::create([
      'name' => $this->randomMachineName(),
      'type' => 'entity_test',
      'link' => [
        'uri' => $url,
      ],
    ]);
    $entity->save();
    $this->drupalGet($entity->toUrl());
    $iframe = $this->assertSession()->elementExists('css', sprintf('iframe[src="%s"]', $url));
    $this->assertEquals(270, $iframe->getAttribute('height'));
    $this->assertEquals(350, $iframe->getAttribute('width'));
    $this->assertEquals('some-class', $iframe->getAttribute('class'));
    $this->assertEquals('yes', $iframe->getAttribute('scrolling'));
    $link = $this->assertSession()->elementExists('named', ['link', $url]);
    $this->assertEquals($url, $link->getAttribute('href'));
    $this->assertSession()->pageTextContains('You may view the original link at');
  }

}
