<?php

namespace Drupal\typeapproval_release\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\entity_print\Plugin\EntityPrintPluginManagerInterface;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReleasePdfController extends ControllerBase implements ContainerInjectionInterface {

  protected RendererInterface $renderer;
  protected EntityPrintPluginManagerInterface $pluginManager;

  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container) {
    return new self(
      $container->get('renderer'),
    );
  }

  /**
   * Generates a Release Form PDF for the given webform submission.
   */
  public function generate(WebformSubmissionInterface $webform_submission): Response
  {
    // Ensure this is the expected webform.
    $webform = $webform_submission->getWebform();
    if (!$webform || $webform->id() !== 'type_approval_application_form') {
      throw new AccessDeniedHttpException('Unsupported webform.');
    }

    // Access already checked by route requirement, but double-check ownership/access.
    if (!$webform_submission->access('view')) {
      throw new AccessDeniedHttpException();
    }

    $data = $webform_submission->getData();

    // Map fields we want to show.
    $applicant = [
      'type_of_applicant' => $data['type_of_applicant'] ?? '',
      'individual_or_organization' => $data['individual_or_organization'] ?? '',
      'full_name' => $data['full_name'] ?? '',
      'organization' => $data['organization'] ?? '',
      'email_address' => $data['email_address'] ?? '',
      'contact_person_name' => $data['contact_person_name'] ?? '',
      'country' => $data['country'] ?? '',
      'telephone' => $data['telephone'] ?? '',
      'physical_address' => $data['physical_address'] ?? '',
      'postal_address' => $data['postal_address'] ?? '',
    ];

    $device = [
      'type_of_device' => $data['type_of_device'] ?? '',
      'manufacturer' => $data['manufacturer'] ?? '',
      'model' => $data['model'] ?? '',
      'product_name' => $data['product_name'] ?? '',
      'origin' => $data['origin'] ?? '',
      'frequency_range' => $data['frequency_range'] ?? '',
      'itu_emission_code' => $data['itu_emission_code'] ?? '',
      'modulation' => $data['modulation'] ?? '',
      'power_output' => $data['power_output'] ?? '',
      'intended_use' => $data['intended_use_in_kiribati_select'] ?? '',
      'quantity_of_device' => $data['quantity_of_device'] ?? '',
    ];

    // Prepare assets as data URIs to ensure the PDF engine can render them reliably.
    $modulePath = DRUPAL_ROOT . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'typeapproval_release';
    $logoPath = $modulePath . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'logo.jpg';
    $signPath = $modulePath . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'sign.jpg';

    $assets = [
      'logo_image' => $this->toDataUri($logoPath, 'image/jpeg'),
      'sign_image' => $this->toDataUri($signPath, 'image/jpeg'),
    ];

    $build = [
      '#theme' => 'typeapproval_release_pdf',
      '#webform_submission' => $webform_submission,
      '#applicant' => $applicant,
      '#device' => $device,
      '#generated_on' => \Drupal::service('date.formatter')->format(\Drupal::time()->getRequestTime(), 'custom', 'Y-m-d H:i'),
      '#assets' => $assets,
      '#cache' => ['max-age' => 0],
    ];

    $html = $this->renderer->renderPlain($build);

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultPaperSize', 'a4');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = 'release-form-' . $webform_submission->id() . '.pdf';
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

    return $response;
  }

  /**
   * Convert a local file to a data URI string for embedding in HTML.
   */
  private function toDataUri(string $path, string $mime): string {
    if (!is_readable($path)) {
      return '';
    }
    $data = file_get_contents($path);
    if ($data === false) {
      return '';
    }
    return 'data:' . $mime . ';base64,' . base64_encode($data);
  }
}
