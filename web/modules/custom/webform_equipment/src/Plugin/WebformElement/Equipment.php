<?php

namespace Drupal\webform_equipment\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformElement\WebformCompositeBase;

/**
 * Provides an 'equipment' composite element.
 *
 * @WebformElement(
 *   id = "equipment",
 *   label = @Translation("Equipment"),
 *   description = @Translation("Provides a composite element for equipment details and supporting documents."),
 *   category = @Translation("Composite elements"),
 *   multiline = TRUE,
 *   composite = TRUE,
 *   states_wrapper = TRUE,
 * )
 */
class Equipment extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getCompositeElements() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function initializeCompositeElements(array &$element) {
    // Fields sourced from the Type Approval Application Form configuration:
    // Sections: "Device and Import Information" and "Supporting Documents".
    $element['#webform_composite_elements'] = [
      // Section: Device and Import Information.
      'device_and_import_information' => [
        '#type' => 'webform_section',
        '#title' => $this->t('Device and Import Information'),
        // Flexbox for approval and type of device.
        'flexbox_13' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'have_you_review_the_list_of_approved_devices' => [
            '#type' => 'select',
            '#title' => $this->t('Has Your Device Been Approved?'),
            '#description' => $this->t('Please refer to the <a href=":url">List of Approved Devices</a> to see if you device has been approved or not.', [':url' => '/list-approved-devices']),
            '#options' => [
              'Yes' => 'Yes',
              'No' => 'No',
            ],
            '#required' => TRUE,
          ],
          'type_of_device' => [
            '#type' => 'select',
            '#title' => $this->t('Type of Device'),
            '#options' => [
              'Broadband Terminal Equipment' => $this->t('Broadband Terminal Equipment'),
              'Fixed Telecommunications Equipment' => $this->t('Fixed Telecommunications Equipment'),
              'Fixed Wireless Terminal Equipment' => $this->t('Fixed Wireless Terminal Equipment'),
              'IoT and Smart Devices' => $this->t('IoT and Smart Devices'),
              'Automotive Communication and Infotainment Equipment' => $this->t('Automotive Communication and Infotainment Equipment'),
              'Radio and Wireless Communication Equipment' => $this->t('Radio and Wireless Communication Equipment'),
            ],
            '#required' => TRUE,
          ],
        ],
        // Flexbox for manufacturer and model.
        'flexbox_03' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'manufacturer' => [
            '#type' => 'textfield',
            '#title' => $this->t('Manufacturer'),
            '#description' => $this->t('E.g. Apple, Samsung, LG etc'),
            '#required' => TRUE,
          ],
          'model' => [
            '#type' => 'textfield',
            '#title' => $this->t('Model'),
            '#description' => $this->t('E.g. iPhone 13 Pro Max, Samsung Galaxy S9 etc'),
            '#required' => TRUE,
          ],
        ],
        // Flexbox for origin.
        'flexbox_04' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'origin' => [
            '#type' => 'textfield',
            '#title' => $this->t('Origin'),
            '#states' => [
              'visible' => [
                ':input[name="type_of_applicant"]' => ['value' => 'International Customer'],
              ],
              'required' => [
                ':input[name="type_of_applicant"]' => ['value' => 'International Customer'],
              ],
            ],
          ],
        ],
        // Flexbox for frequency range and ITU emission code.
        'flexbox_05' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'frequency_range' => [
            '#type' => 'textfield',
            '#title' => $this->t('Frequency Range'),
            '#states' => [
              'visible' => [
                [':input[name="type_of_device"]' => ['value' => 'Satellite']],
                'xor',
                [':input[name="type_of_device"]' => ['value' => 'Radio Transceiver']],
              ],
              'required' => [
                [':input[name="type_of_device"]' => ['value' => 'Satellite']],
                'xor',
                [':input[name="type_of_device"]' => ['value' => 'Radio Transceiver']],
              ],
            ],
          ],
          'itu_emission_code' => [
            '#type' => 'textfield',
            '#title' => $this->t('ITU Emission Code'),
            '#states' => [
              'visible' => [
                [':input[name="type_of_device"]' => ['value' => 'Satellite']],
                'xor',
                [':input[name="type_of_device"]' => ['value' => 'Radio Transceiver']],
              ],
              'required' => [
                [':input[name="type_of_device"]' => ['value' => 'Satellite']],
                'xor',
                [':input[name="type_of_device"]' => ['value' => 'Radio Transceiver']],
              ],
            ],
          ],
        ],
        // Flexbox for modulation and power output.
        'flexbox_06' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'modulation' => [
            '#type' => 'textfield',
            '#title' => $this->t('Modulation'),
            '#states' => [
              'visible' => [
                [':input[name="type_of_device"]' => ['value' => 'Satellite']],
                'xor',
                [':input[name="type_of_device"]' => ['value' => 'Radio Transceiver']],
              ],
              'required' => [
                [':input[name="type_of_device"]' => ['value' => 'Satellite']],
                'xor',
                [':input[name="type_of_device"]' => ['value' => 'Radio Transceiver']],
              ],
            ],
          ],
          'power_output' => [
            '#type' => 'textfield',
            '#title' => $this->t('Power Output'),
            '#states' => [
              'visible' => [
                [':input[name="type_of_device"]' => ['value' => 'Satellite']],
                'xor',
                [':input[name="type_of_device"]' => ['value' => 'Radio Transceiver']],
              ],
              'required' => [
                [':input[name="type_of_device"]' => ['value' => 'Satellite']],
                'xor',
                [':input[name="type_of_device"]' => ['value' => 'Radio Transceiver']],
              ],
            ],
          ],
        ],
        'intended_use_in_kiribati_select' => [
          '#type' => 'select',
          '#title' => $this->t('Intended Use in Kiribati'),
          '#options' => [
            'For Sale' => $this->t('For Sale'),
            'For Personal Use' => $this->t('For Personal Use'),
          ],
          '#states' => [
            'visible' => [
              ':input[name="type_of_applicant"]' => ['value' => 'Local Customer'],
            ],
          ],
        ],
      ],

      // Section: Quantity & Entry Point.
      'quantity_entry_point' => [
        '#type' => 'webform_section',
        '#title' => $this->t('Quantity & Entry Point'),
        '#states' => [
          'visible' => [
            ':input[name="type_of_applicant"]' => ['value' => 'Local Customer'],
          ],
        ],
        'flexbox_12' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'quantity_of_device' => [
            '#type' => 'number',
            '#title' => $this->t('Quantity of Device'),
            '#states' => [
              'required' => [
                ':input[name="type_of_applicant"]' => ['value' => 'Local Customer'],
              ],
            ],
          ],
          'custom_entry_point' => [
            '#type' => 'select',
            '#title' => $this->t('Location of Parcel'),
            '#options' => [
              'Customs Betio' => $this->t('Customs Betio'),
              'Customs Bonriki' => $this->t('Customs Bonriki'),
              'DHL Tobaraoi' => $this->t('DHL Tobaraoi'),
              'Postal Office Bairiki' => $this->t('Postal Office Bairiki'),
            ],
            '#states' => [
              'required' => [
                ':input[name="type_of_applicant"]' => ['value' => 'Local Customer'],
              ],
            ],
          ],
        ],
      ],

      // Section: Supporting Documents.
      'supporting_documents' => [
        '#type' => 'webform_section',
        '#title' => $this->t('Supporting Documents'),
        'flexbox_07' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'test_report_s_acma_ce_fcc_itu' => [
            '#type' => 'webform_document_file',
            '#title' => $this->t('Test Report(s) (ACMA, CE, FCC, ITU) '),
            '#max_filesize' => '25',
            '#required' => TRUE,
          ],
          'declaration_of_conformity_certificate' => [
            '#type' => 'webform_document_file',
            '#title' => $this->t('Declaration of Conformity / Certificate'),
            '#max_filesize' => '25',
            '#states' => [
              'visible' => [
                ':input[name="type_of_applicant"]' => ['value' => 'International Customer'],
              ],
              'required' => [
                ':input[name="type_of_applicant"]' => ['value' => 'International Customer'],
              ],
            ],
          ],
        ],
        'flexbox_08' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'copy_of_labeling_marking' => [
            '#type' => 'webform_document_file',
            '#title' => $this->t('Copy of Labeling / Marking'),
            '#max_filesize' => '25',
            '#states' => [
              'visible' => [
                ':input[name="type_of_applicant"]' => ['value' => 'International Customer'],
              ],
              'required' => [
                ':input[name="type_of_applicant"]' => ['value' => 'International Customer'],
              ],
            ],
          ],
          'operating_manual_catalogue' => [
            '#type' => 'webform_document_file',
            '#title' => $this->t('Operating Manual / Catalogue'),
            '#max_filesize' => '25',
            '#states' => [
              'visible' => [
                ':input[name="type_of_applicant"]' => ['value' => 'International Customer'],
              ],
              'required' => [
                ':input[name="type_of_applicant"]' => ['value' => 'International Customer'],
              ],
            ],
          ],
        ],
        'description_of_repair_services_in_kiribati' => [
          '#type' => 'textarea',
          '#title' => $this->t('Description of Repair Services in Kiribati'),
          '#states' => [
            'visible' => [
              ':input[name="type_of_applicant"]' => ['value' => 'Local Customer'],
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    // No additional element-specific settings at this time.
    return $form;
  }

}
