<?php

namespace Drupal\webform_entity_reference_exclude\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Plugin\Field\FieldWidget\WebformEntityReferenceSelectWidget;

/**
 * Provides a widget for selecting webforms, excluding certain entries.
 */
#[FieldWidget(
  id: "webform_entity_reference_exclude_select",
  label: new TranslatableMarkup("Select list with excluded webforms"),
  description: new TranslatableMarkup("A select field to select webforms, with excluded options."),
  field_types: ["webform"]
)]
class WebformEntityReferenceExcludeSelectWidget extends WebformEntityReferenceSelectWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'excluded_webforms' => [],
      'excluded_webform_categories' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $element['webforms']['#access'] = FALSE;
    $element['excluded_webforms'] = [
      '#type' => 'webform_entity_select',
      '#title' => $this->t('Select webform(s) to exclude'),
      '#description' => $this->t("Specify webform(s) to exclude from select list."),
      '#select2' => TRUE,
      '#multiple' => TRUE,
      '#target_type' => 'webform',
      '#selection_handler' => 'default:webform',
      '#default_value' => $this->getSetting('excluded_webforms'),
    ];
    $this->elementManager->processElement($element['excluded_webforms']);
    $element['excluded_webform_categories'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Specify webform categories to exclude'),
      '#description' => $this->t("Comma-separated list of webform categories to exclude from the select list. Categories can be entered case-insensitive."),
      '#default_value' => $this->getSetting('excluded_webform_categories'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();
    if ($excluded_webform_options = $this->getExcludedWebformsAsOptions()) {
      $summary[] = $this->t('Excluded webforms: @webforms', ['@webforms' => implode('; ', $excluded_webform_options)]);
    }
    if ($excluded_categories = $this->getSetting('excluded_webform_categories')) {
      $summary[] = $this->t('Excluded webform categories: @categories', ['@categories' => $excluded_categories]);
    }
    return $summary;
  }

  /**
   * Returns the array of options for the widget.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity for which to return options.
   *
   * @return array
   *   The array of options for the widget.
   */
  protected function getOptions(FieldableEntityInterface $entity) {
    $options = parent::getOptions($entity);
    $excluded_webform_ids = $this->getSetting('excluded_webforms');
    // If webforms are excluded, remove them from the select list.
    if ($excluded_webform_ids) {
      foreach ($excluded_webform_ids as $excluded_webform_id) {
        if (isset($options[$excluded_webform_id])) {
          unset($options[$excluded_webform_id]);
        }
        else {
          // Also exclude from categories.
          foreach ($options as $option_key => $option) {
            if (is_array($option)) {
              if (isset($option[$excluded_webform_id])) {
                unset($options[$option_key][$excluded_webform_id]);
              }
            }
          }
          // If there are no selectable webforms left in this category, remove
          // it altogether.
          if (empty($options[$option_key])) {
            unset($options[$option_key]);
          }
        }
      }
    }

    $excluded_webform_categories = $this->getSetting('excluded_webform_categories');
    // If webform categories are excluded, remove them from the select list.
    if ($excluded_webform_categories) {
      $excluded_webform_categories = explode(',', $excluded_webform_categories);
      $excluded_webform_categories = array_map(function ($excluded_webform_category) {
        return strtolower(trim($excluded_webform_category));
      }, $excluded_webform_categories);
      foreach ($excluded_webform_categories as $excluded_webform_category) {
        if (isset($options[$excluded_webform_category])) {
          unset($options[$excluded_webform_category]);
        }
      }
    }
    return $options;
  }

  /**
   * Get excluded webforms as options.
   *
   * @return array
   *   Webforms as options.
   */
  protected function getExcludedWebformsAsOptions(): array {
    $webform_ids = $this->getSetting('excluded_webforms');
    if (empty($webform_ids)) {
      return [];
    }

    $webforms = Webform::loadMultiple($webform_ids);
    $options = [];
    foreach ($webforms as $webform) {
      $options[$webform->id()] = $webform->label();
    }
    asort($options);
    return $options;
  }

}
