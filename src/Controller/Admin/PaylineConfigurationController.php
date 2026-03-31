<?php
/**
 * Payline module for PrestaShop
 *
 * @author    Monext <support@payline.com>
 * @copyright Monext - http://www.payline.com
 */

declare(strict_types=1);

namespace PrestaShop\Module\Payline\Controller\Admin;

use Configuration;
use PaylinePaymentGateway;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Entry point for the Payline module configuration page.
 *
 * Each tab has its own action method and Symfony form.
 *
 * Note: Extends FrameworkBundleAdminController for compatibility with PrestaShop < 9.
 * @see https://devdocs.prestashop-project.org/9/modules/creation/module-translation/new-system/#module-controllers
 */
class PaylineConfigurationController extends FrameworkBundleAdminController
{
    /**
     * @var FormHandlerInterface
     */
    private $generalFormHandler;

    /**
     * @var FormHandlerInterface
     */
    private $webPaymentFormHandler;

    /**
     * @var FormHandlerInterface
     */
    private $nxPaymentFormHandler;

    /**
     * @var FormHandlerInterface
     */
    private $recurringPaymentFormHandler;

    /**
     * @var FormHandlerInterface
     */
    private $contractsFormHandler;

    public function __construct(
        FormHandlerInterface $generalFormHandler,
        FormHandlerInterface $webPaymentFormHandler,
        FormHandlerInterface $nxPaymentFormHandler,
        FormHandlerInterface $recurringPaymentFormHandler,
        FormHandlerInterface $contractsFormHandler
    ) {
        $this->generalFormHandler = $generalFormHandler;
        $this->webPaymentFormHandler = $webPaymentFormHandler;
        $this->nxPaymentFormHandler = $nxPaymentFormHandler;
        $this->recurringPaymentFormHandler = $recurringPaymentFormHandler;
        $this->contractsFormHandler = $contractsFormHandler;
    }

    /**
     * Get common template variables for the layout (menu state).
     */
    private function getLayoutVariables(string $activeTab): array
    {
        $apiStatus = (bool) Configuration::get('PAYLINE_API_STATUS');
        $contractsErrors = false;

        if ($apiStatus && class_exists('PaylinePaymentGateway')) {
            $contractsErrors = (Configuration::get('PAYLINE_WEB_CASH_ENABLE')
                    || Configuration::get('PAYLINE_RECURRING_ENABLE')
                    || Configuration::get('PAYLINE_SUBSCRIBE_ENABLE'))
                && empty(PaylinePaymentGateway::getEnabledContracts());
        }

        return [
            'activeTab' => $activeTab,
            'apiStatus' => $apiStatus,
            'contractsErrors' => $contractsErrors,
        ];
    }

    /**
     * Landing page - What is Monext?
     */
    public function landing(): Response
    {
        return $this->render('@Modules/payline/views/templates/admin/tabs/landing.html.twig',
            $this->getLayoutVariables('landing')
        );
    }

    /**
     * General configuration (credentials) - Symfony form.
     */
    public function general(Request $request): Response
    {
        $form = $this->generalFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->generalFormHandler->save($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Configuration saved.', 'Admin.Notifications.Success'));
                return $this->redirectToRoute('payline_configuration_general');
            }

            $this->addFlashErrors($errors);
        }

        return $this->render('@Modules/payline/views/templates/admin/tabs/general.html.twig',
            array_merge(
                $this->getLayoutVariables('general'),
                ['form' => $form->createView()]
            )
        );
    }

    /**
     * Simple payment (web payment) - Symfony form.
     */
    public function webPayment(Request $request): Response
    {
        $form = $this->webPaymentFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->webPaymentFormHandler->save($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Configuration saved.', 'Admin.Notifications.Success'));
                return $this->redirectToRoute('payline_configuration_web');
            }

            $this->addFlashErrors($errors);
        }

        return $this->render('@Modules/payline/views/templates/admin/tabs/web.html.twig',
            array_merge(
                $this->getLayoutVariables('web'),
                ['form' => $form->createView()]
            )
        );
    }

    /**
     * NX payment - Symfony form.
     */
    public function nxPayment(Request $request): Response
    {
        $form = $this->nxPaymentFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->nxPaymentFormHandler->save($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Configuration saved.', 'Admin.Notifications.Success'));
                return $this->redirectToRoute('payline_configuration_nx');
            }

            $this->addFlashErrors($errors);
        }

        return $this->render('@Modules/payline/views/templates/admin/tabs/nx.html.twig',
            array_merge(
                $this->getLayoutVariables('nx'),
                ['form' => $form->createView()]
            )
        );
    }

    /**
     * Recurring payment - Symfony form.
     */
    public function recurringPayment(Request $request): Response
    {
        $form = $this->recurringPaymentFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->recurringPaymentFormHandler->save($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Configuration saved.', 'Admin.Notifications.Success'));
                return $this->redirectToRoute('payline_configuration_recurring');
            }

            $this->addFlashErrors($errors);
        }

        //
        $template = '@Modules/payline/views/templates/admin/tabs/recurring.html.twig';
        if (class_exists('PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType')){
            $template = '@Modules/payline/views/templates/admin/tabs/recurring_17.html.twig';
        }

        return $this->render($template,
            array_merge(
                $this->getLayoutVariables('recurring'),
                ['form' => $form->createView()]
            )
        );
    }

    /**
     * Contracts configuration - Symfony form.
     */
    public function contracts(Request $request): Response
    {
        $form = $this->contractsFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->contractsFormHandler->save($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Configuration saved.', 'Admin.Notifications.Success'));
                return $this->redirectToRoute('payline_configuration_contracts');
            }

            $this->addFlashErrors($errors);
        }

        // Retrieve contracts available for the current POS
        $currentPos = Configuration::get('PAYLINE_POS');
        $contractsList = [];
        $fallbackContractsList = [];
        $enabledContracts = [];
        $enabledAltContracts = [];

        if (!empty($currentPos) && class_exists('PaylinePaymentGateway')) {
            $enabledContracts = PaylinePaymentGateway::getEnabledContracts();
            $enabledAltContracts = PaylinePaymentGateway::getFallbackEnabledContracts();
            $contractsList = PaylinePaymentGateway::getContractsByPosLabel($currentPos, $enabledContracts);
            $fallbackContractsList = PaylinePaymentGateway::getContractsByPosLabel($currentPos, $enabledAltContracts);
        }

        return $this->render('@Modules/payline/views/templates/admin/tabs/contracts.html.twig',
            array_merge(
                $this->getLayoutVariables('contracts'),
                [
                    'form' => $form->createView(),
                    'contractsList' => $contractsList,
                    'fallbackContractsList' => $fallbackContractsList,
                    'enabledContracts' => $enabledContracts,
                    'enabledAltContracts' => $enabledAltContracts,
                    'modulePath' => _MODULE_DIR_ . 'payline/',
                ]
            )
        );
    }

    /**
     * Logs viewing.
     */
    public function logs(): Response
    {
        $logFiles = $this->getPaylineLogsFilesList();

        return $this->render('@Modules/payline/views/templates/admin/tabs/logs.html.twig',
            array_merge(
                $this->getLayoutVariables('logs'),
                ['logFiles' => $logFiles]
            )
        );
    }

    /**
     * Return payline logs directory
     * @since 2.3.14
     * @return string
     */
    public function getPaylineLogsDirectory(): string
    {
        return _PS_ROOT_DIR_.'/var/logs/payline' . DIRECTORY_SEPARATOR;
    }

    public function getPaylineLogsFilesList()
    {
        $logsFiles = [];
        $directoryPath = $this->getPaylineLogsDirectory();
        if (is_dir($directoryPath)) {
            $files = scandir($directoryPath, SCANDIR_SORT_DESCENDING);
            $files = array_diff($files, array('.', '..')); // Exclure les entrées spéciales
            foreach ($files as $file) {
                $logsFiles[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
        return $logsFiles;
    }

    /**
     * AJAX endpoint for loading log file content.
     */
    public function logsAjax(Request $request): Response
    {
        $pattern = '/\[(?P<date>.*)\] (?P<logger>[\w-]+).(?P<level>\w+): (?P<message>[^\[\{]+) (?P<context>[\[\{].*[\]\}]) (?P<extra>[\[\{].*[\]\}])/';
        $logFilename = $request->query->get('logfile', '');

        $logFileContent = [];
        if ($logFilename && in_array($logFilename, $this->getPaylineLogsFilesList())) {
            $logFile = $this->getPaylineLogsDirectory() . $logFilename.'.log';
            foreach (file($logFile) as $line) {
                if (preg_match($pattern, trim($line), $matches)) {
                    $logFileContent[] = [
                        'date'    => $matches['date'],
                        'logger' => $matches['logger'],
                        'level'   => $matches['level'],
                        'message' => trim($matches['message']),
                        'context' => json_decode($matches['context'], true),
                        'extra'   => json_decode($matches['extra'], true),
                    ];
                }
            }
        }

        $response = json_encode(array_reverse($logFileContent));
        return new Response($response);
    }

    /**
     * Add multiple error messages as flash messages.
     *
     * This method is available in PrestaShopAdminController but not in
     * FrameworkBundleAdminController, so we implement it here for compatibility.
     *
     * @param array $errorMessages Array of error messages (strings or ['key', 'parameters', 'domain'])
     */
    protected function addFlashErrors(array $errorMessages): void
    {
        foreach ($errorMessages as $error) {
            $message = is_array($error)
                ? $this->trans($error['key'], $error['domain'] ?? 'Admin.Notifications.Error', $error['parameters'] ?? [])
                : $error;
            $this->addFlash('error', $message);
        }
    }
}
