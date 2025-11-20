<?php

namespace Drupal\webform_equipment\Element;

use Drupal\webform\Element\WebformCompositeBase;

/**
 * Provides a webform element for equipment composite element.
 *
 * @FormElement("equipment")
 */
class WebformEquipment extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    // Use default composite theming and processing provided by parent.
    $info = parent::getInfo();
    // Attach equipment-specific styles to improve flexbox spacing in tables.
    $info['#attached']['library'][] = 'webform_equipment/equipment.styles';
    return $info;
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {
    $elements = [
      // Section: Device and Import Information.
      'device_and_import_information' => [
        '#type' => 'webform_section',
        '#title' => t('Device and Import Information'),
        // Flexbox for approval and type of device.
        'flexbox_13' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'have_you_review_the_list_of_approved_devices' => [
            '#type' => 'select',
            '#title' => t('Has Your Device Been Approved?'),
            '#description' => t('Please refer to the <a href=":url">List of Approved Devices</a> to see if you device has been approved or not.', [':url' => '/list-approved-devices']),
            '#options' => [
              'Yes' => 'Yes',
              'No' => 'No',
            ],
            '#required' => TRUE,
          ],
          'type_of_device' => [
            '#type' => 'select',
            '#title' => t('Type of Device'),
            '#options' => [
              'Broadband Terminal Equipment' => t('Broadband Terminal Equipment'),
              'Fixed Telecommunications Equipment' => t('Fixed Telecommunications Equipment'),
              'Fixed Wireless Terminal Equipment' => t('Fixed Wireless Terminal Equipment'),
              'IoT and Smart Devices' => t('IoT and Smart Devices'),
              'Automotive Communication and Infotainment Equipment' => t('Automotive Communication and Infotainment Equipment'),
              'Radio and Wireless Communication Equipment' => t('Radio and Wireless Communication Equipment'),
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
            '#title' => t('Manufacturer'),
            '#description' => t('E.g. Apple, Samsung, LG etc'),
            '#required' => TRUE,
          ],
          'model' => [
            '#type' => 'textfield',
            '#title' => t('Model'),
            '#description' => t('E.g. iPhone 13 Pro Max, Samsung Galaxy S9 etc'),
            '#required' => TRUE,
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
            '#title' => t('Frequency Range'),
            '#description' => 'Type N/A if not applicable',
            '#required' => TRUE,
          ],
          'itu_emission_code' => [
            '#type' => 'textfield',
            '#title' => t('ITU Emission Code'),
            '#description' => 'Type N/A if not applicable',
            '#required' => TRUE,
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
            '#title' => t('Modulation'),
            '#description' => 'Type N/A if not applicable',
            '#required' => TRUE,
          ],
          'power_output' => [
            '#type' => 'textfield',
            '#title' => t('Power Output'),
            '#description' => 'Type N/A if not applicable',
            '#required' => TRUE,
          ],
        ],
      ],

      // Section: Supporting Documents.
      'supporting_documents' => [
        '#type' => 'webform_section',
        '#title' => t('Supporting Documents'),
        'flexbox_07' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'test_report_s_acma_ce_fcc_itu' => [
            '#type' => 'webform_document_file',
            '#title' => t('Test Report(s) (ACMA, CE, FCC, ITU) '),
            '#max_filesize' => '25',
            '#required' => TRUE,
          ],
        ],
      ],
    ];
    if ($element['#webform'] == 'type_approval_application_form_i') {
      $elements['device_and_import_information']['flexbox_04'] = [
        '#type' => 'webform_flexbox',
        '#attributes' => [
          'class' => ['webform-flex', 'webform-flex--container'],
        ],
        'origin' => [
          '#type' => 'textfield',
          '#title' => t('Device or Equipment Origin'),
          '#required' => TRUE,
        ],
      ];
      $elements['supporting_documents']['flexbox_07']['declaration_of_conformity_certificate'] = [
        '#type' => 'webform_document_file',
        '#title' => 'Declaration of Conformity / Certificate',
        '#required' => true,
        '#max_filesize' => '25',
      ];
      $elements['supporting_documents']['flexbox_08'] = [
        '#type' => 'webform_flexbox',
        '#attributes' => [
          'class' => ['webform-flex', 'webform-flex--container'],
        ],
        'copy_of_labeling_marking' => [
          '#type' => 'webform_document_file',
          '#title' => t('Copy of Labeling / Marking'),
          '#max_filesize' => '25',
          '#required' => TRUE,
        ],
        'operating_manual_catalogue' => [
          '#type' => 'webform_document_file',
          '#title' => t('Operating Manual / Catalogue'),
          '#max_filesize' => '25',
          '#required' => TRUE,
        ],
      ];
    }

    if ($element['#webform'] == 'type_approval_application_form_l') {
      $elements['device_and_import_information']['flexbox_06']['intended_use_in_kiribati_select'] = [
        '#type' => 'select',
        '#title' => t('Intended Use in Kiribati'),
        '#options' => [
          'For Sale' => t('For Sale'),
          'For Personal Use' => t('For Personal Use'),
        ],
      ];
      $elements['quantity_entry_point'] = [
        '#type' => 'webform_section',
        '#title' => t('Quantity & Entry Point'),
        'flexbox_12' => [
          '#type' => 'webform_flexbox',
          '#attributes' => [
            'class' => ['webform-flex', 'webform-flex--container'],
          ],
          'quantity_of_device' => [
            '#type' => 'number',
            '#title' => t('Quantity of Device'),
            '#required' => TRUE,
          ],
          'custom_entry_point' => [
            '#type' => 'select',
            '#title' => t('Location of Parcel'),
            '#required' => TRUE,
            '#options' => [
              'Customs Betio' => t('Customs Betio'),
              'Customs Bonriki' => t('Customs Bonriki'),
              'DHL Tobaraoi' => t('DHL Tobaraoi'),
              'Postal Office Bairiki' => t('Postal Office Bairiki'),
            ],
          ],
        ],
      ];
      $elements['supporting_documents']['description_of_repair_services_in_kiribati'] = [
        '#type' => 'textarea',
        '#title' => t('Description of Repair Services in Kiribati'),
      ];
    }

    return $elements;
  }

}
