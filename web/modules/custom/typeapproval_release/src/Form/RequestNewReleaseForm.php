<?php

namespace Drupal\typeapproval_release\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Lightweight form to request a new release for the same device.
 *
 * Confirms/collects a new quantity and creates a child webform submission
 * reusing the device fields from the parent submission.
 */
class RequestNewReleaseForm extends FormBase {
  use MessengerTrait;

  /** @var \Drupal\webform\WebformSubmissionInterface|null */
  protected $parentSubmission;

  public function getFormId() {
    return 'typeapproval_release_request_new_release_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission = NULL) {
    $this->parentSubmission = $webform_submission;

    // Basic sanity checks.
    if (!$this->parentSubmission) {
      $this->messenger()->addError($this->t('Missing parent submission.'));
      return $form;
    }

    // Security: only owner can request a new release; and status must be Granted.
    $account = $this->currentUser();
    if ((int) $this->parentSubmission->getOwnerId() !== (int) $account->id()) {
      $this->messenger()->addError($this->t('You do not have permission to request a new release for this submission.'));
      return $form;
    }

    $data = $this->parentSubmission->getData();
    $status = (string) ($data['status'] ?? '');
    if (strcasecmp($status, 'Granted') !== 0 && strcasecmp($status, 'Approved') !== 0) {
      $this->messenger()->addError($this->t('A new release can only be requested when the parent submission is Granted.'));
      return $form;
    }

    // Display device/applicant details read-only for confirmation (following provided sample).
    // Build a mapping in the desired order so labels are clean and values come from submission data.
    $summary_map = [
      'contact_person_name' => 'Contact person name',
      'copy_of_labeling_marking' => 'Copy of labeling/marking',
      'country' => 'Country',
      'custom_entry_point' => 'Custom entry point',
      'declaration_of_conformity_certificate' => 'Declaration of conformity certificate',
      'description_of_repair_services_in_kiribati' => 'Description of repair services in Kiribati',
      'email_address' => 'Email address',
      'fee' => 'Fee',
      'frequency_range' => 'Frequency range',
      'full_name' => 'Full name',
      'have_you_review_the_list_of_approved_devices' => 'Reviewed list of approved devices',
      'individual_or_organization' => 'Individual or organization',
      'intended_use_in_kiribati_select' => 'Intended use in Kiribati',
      'itu_emission_code' => 'ITU emission code',
      'manufacturer' => 'Manufacturer',
      'mobile' => 'Mobile',
      'model' => 'Model',
      'modulation' => 'Modulation',
      'operating_manual_catalogue' => 'Operating manual/catalogue',
      'organization' => 'Organization',
      'origin' => 'Origin',
      'physical_address' => 'Physical address',
      'postal_address' => 'Postal address',
      'power_output' => 'Power output',
      'quantity_of_device' => 'Quantity of device',
      'reason_for_rejection' => 'Reason for rejection',
      'telephone' => 'Telephone',
      'test_report_s_acma_ce_fcc_itu' => 'Test report(s) ACMA/CE/FCC/ITU',
      'type_of_applicant' => 'Type of applicant',
      'type_of_device' => 'Type of device',
      'website' => 'Website',
    ];

    $items = [];
    foreach ($summary_map as $key => $label) {
      $raw = $data[$key] ?? '';
      $value = (string) $raw;
      if (empty($value)) {
        continue;
      }
      // Convert website to link if it looks like a URL.
      if ($key === 'website' && is_string($raw) && preg_match('/^https?:\/\//i', $raw)) {
        try {
          $url = Url::fromUri($raw);
          $link = Link::fromTextAndUrl($raw, $url)->toString();
          $value = $link;
        }
        catch (\Exception $e) {
          // Fallback to plain text if URL is invalid.
          $value = $raw;
        }
      }

      $items[] = [
        '#markup' => '<strong>' . $this->t('@label', ['@label' => $label]) . ':</strong> ' . ($key === 'website' && isset($link) ? $value : $this->t('@value', ['@value' => $value])),
      ];
      unset($link);
    }

    $form['intro'] = [
      '#type' => 'item',
      '#markup' => $this->t('Request a new release for the same device. Please confirm the details and the new quantity.'),
    ];

    $form['device_summary'] = [
      '#type' => 'details',
      '#title' => $this->t('Submission details (read-only)'),
      '#open' => TRUE,
      'list' => [
        '#theme' => 'item_list',
        '#items' => $items,
      ],
    ];

    $form['quantity_of_device'] = [
      '#type' => 'number',
      '#title' => $this->t('New quantity'),
      '#min' => 1,
      '#required' => TRUE,
    ];

    // Hidden to pass parent SID.
    $form['parent_sid'] = [
      '#type' => 'hidden',
      '#value' => $this->parentSubmission->id(),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Create new request'),
      '#button_type' => 'primary',
    ];

    // Provide a cancel link back to the parent submission.
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => $this->parentSubmission->toUrl('canonical'),
      '#attributes' => ['class' => ['button']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $qty = (int) $form_state->getValue('quantity_of_device');
    if ($qty < 1) {
      $form_state->setErrorByName('quantity_of_device', $this->t('Quantity must be at least 1.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $parent = $this->parentSubmission;
    $data = $parent->getData();

    // Prepare new submission data by copying device fields and basic applicant.
    $new_data = $data;

    // Overwrite quantity.
    $new_data['quantity_of_device'] = (int) $form_state->getValue('quantity_of_device');

    // For repeat applications, fee is waived.
    $new_data['fee'] = '$0';

    // Link to parent.
    $new_data['repeat_parent_sid'] = $parent->id();

    // Reset workflow/status to Need Review so back-office can process normally.
    $new_data['status'] = 'Need Review';

    // Create child submission under the same webform and owner.
    $values = [
      'webform_id' => $parent->getWebform()->id(),
      'data' => $new_data,
      'uid' => $parent->getOwnerId(),
      'entity_type' => $parent->getEntityTypeId(),
      'entity_id' => $parent->id(),
    ];

    /** @var \Drupal\webform\WebformSubmissionInterface $child */
    $child = WebformSubmission::create($values);
    $child->save();

    $this->messenger()->addStatus($this->t('New request created from submission @sid.', ['@sid' => $parent->id()]));

    // Redirect to the front page after creating the new request.
    $form_state->setRedirect('<front>');
  }
}
